<?php

namespace App\Service\Admin;

use App\Context\QueryContext;
use App\Exception\NotFoundException;
use App\Repository\Contract\BookingLogRepositoryInterface;
use App\Repository\Contract\BookingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

readonly class BookingService
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private BookingLogRepositoryInterface $bookingLogRepository,
        private Request $request,
    ) {
    }

    public function list(QueryContext $context, array $searchFields = []): LengthAwarePaginator
    {
        return $this->bookingRepository->list($context, $searchFields);
    }

    /**
     * Get bookings for export with filters
     */
    public function getForExport(array $filters = [])
    {
        $query = $this->bookingRepository->getModel()
            ->with(['items.passengers', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by payment_status
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Limit results
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        return $query->get();
    }

    /**
     * @throws NotFoundException
     */
    public function show(int $id): Model
    {
        $booking = $this->bookingRepository->find($id, ['user', 'items.passengers', 'items.productable', 'logs']);

        if (!$booking) {
            throw new NotFoundException('Booking', $id);
        }

        return $booking;
    }

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            $data['code'] = $this->generateBookingCode();

            $totals = $this->calculateTotals($items);
            $data['subtotal'] = $totals['subtotal'];
            $data['total_amount'] = $totals['total_amount'];

            $booking = $this->bookingRepository->create($data);

            if (!empty($items)) {
                $this->syncItems($booking, $items);
            }
            $this->logBooking($booking, 'created', null);

            return $booking->load(['items.passengers', 'items.productable']);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function update(int $id, array $data): Model
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            throw new NotFoundException('Booking', $id);
        }

        return DB::transaction(function () use ($id, $data, $booking) {
            $oldValues = $booking->only([
                'customer_name',
                'customer_phone',
                'customer_email',
                'note',
                'discount',
                'tax',
                'status',
                'payment_status',
                'payment_method',
                'source',
            ]);

            $items = $data['items'] ?? null;
            unset($data['items']);

            if ($items !== null) {
                $totals = $this->calculateTotals($items);
                $data['subtotal'] = $totals['subtotal'];
                $data['total_amount'] = $totals['total_amount'];
            }

            $updatedBooking = $this->bookingRepository->update($id, $data);

            if ($items !== null) {
                $this->syncItems($updatedBooking, $items);
            }

            $event = 'updated';
            if (isset($data['status']) && $data['status'] !== $oldValues['status']) {
                $event = 'status_changed';
            } elseif (isset($data['payment_status']) && $data['payment_status'] !== $oldValues['payment_status']) {
                $event = 'payment_status_changed';
            }

            $this->logBooking($updatedBooking, $event, $oldValues);

            return $updatedBooking->load(['items.passengers', 'items.productable']);
        });
    }

    /**
     * @throws NotFoundException
     */
    public function destroy(int $id): bool
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            throw new NotFoundException('Booking', $id);
        }

        return DB::transaction(function () use ($id, $booking) {
            $this->logBooking($booking, 'deleted', null);
            return $this->bookingRepository->delete($id);
        });
    }

    public function destroyMultiple(array $ids): int
    {
        return DB::transaction(function () use ($ids) {
            $bookings = $this->bookingRepository->findAllBy(['id' => $ids]);

            foreach ($bookings as $booking) {
                $this->logBooking($booking, 'deleted', null);
            }

            return $this->bookingRepository->deleteMultiple($ids);
        });
    }

    private function generateBookingCode(): string
    {
        $timestamp = now()->format('YmdHis');
        $random = strtoupper(Str::random(4));

        return "BK-{$timestamp}-{$random}";
    }

    /**
     * Calculate subtotal and total_amount from items
     *
     * @param array $items
     * @return array{subtotal: float, total_amount: float}
     */
    private function calculateTotals(array $items): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $subtotal += $quantity * $price;
        }

        $total = $subtotal;

        return [
            'subtotal' => $subtotal,
            'total_amount' => $total,
        ];
    }

    /**
     * Sync booking items and passengers
     */
    private function syncItems(Model $booking, array $items): void
    {
        $booking->items()->delete();

        foreach ($items as $itemData) {
            $passengers = $itemData['passengers'] ?? [];
            unset($itemData['passengers']);

            $itemData['booking_id'] = $booking->id;

            $itemData['total_price'] = ($itemData['quantity'] ?? 1) * ($itemData['price'] ?? 0);
            $item = $booking->items()->create($itemData);
            if (!empty($passengers)) {
                foreach ($passengers as $passengerData) {
                    $passengerData['booking_item_id'] = $item->id;
                    $item->passengers()->create($passengerData);
                }
            }
        }
    }

    /**
     * Log booking changes
     */
    private function logBooking(Model $booking, string $event, ?array $oldValues): void
    {
        $user = Auth::guard('admin')->user();

        $logData = [
            'booking_id' => $booking->id,
            'user_id' => $user?->id,
            'causer_name' => $user?->name ?? 'System',
            'event' => $event,
            'old_values' => $oldValues,
            'new_values' => $booking->only([
                'customer_name',
                'customer_phone',
                'customer_email',
                'note',
                'subtotal',
                'discount',
                'tax',
                'total_amount',
                'status',
                'payment_status',
                'payment_method',
                'source',
            ]),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ];

        $this->bookingLogRepository->create($logData);
    }
}

