<?php

namespace App\Service\Admin;

use App\Support\Excel\BaseExcelExport;
use App\Support\Excel\BaseMultiSheetExport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

readonly class ExportBookingService
{
    public function __construct(
        private BookingService $bookingService,
    ) {}

    /**
     * Export bookings to Excel with filters
     *
     * @param array $filters
     * @return BinaryFileResponse
     */
    public function exportToExcel(array $filters = []): BinaryFileResponse
    {
        $bookings = $this->bookingService->getForExport($filters);

        $fileName = $this->generateFileName($filters);

        $export = $this->createExport($bookings);

        return Excel::download($export, $fileName);
    }

    /**
     * Get export summary (for preview/stats)
     */
    public function getExportSummary(array $filters = []): array
    {
        $bookings = $this->bookingService->getForExport($filters);

        return [
            'total_bookings' => $bookings->count(),
            'total_items' => $bookings->sum(fn($b) => $b->items->count()),
            'total_amount' => $bookings->sum('total_amount'),
            'date_from' => $filters['date_from'] ?? null,
            'date_to' => $filters['date_to'] ?? null,
        ];
    }

    /**
     * Generate dynamic file name based on filters
     */
    private function generateFileName(array $filters): string
    {
        $parts = ['bookings'];

        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $from = $filters['date_from'] ?? 'start';
            $to = $filters['date_to'] ?? 'end';
            $parts[] = "{$from}_to_{$to}";
        }

        if (!empty($filters['status'])) {
            $parts[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $parts[] = $filters['payment_status'];
        }

        $parts[] = now()->format('YmdHis');

        return implode('_', $parts) . '.xlsx';
    }

    /**
     * Create multi-sheet export instance
     */
    private function createExport(Collection $bookings): BaseMultiSheetExport
    {
        return new class($bookings) extends BaseMultiSheetExport
        {
            public function sheets(): array
            {
                return [
                    $this->createBookingListSheet(),
                    $this->createBookingItemsSheet(),
                ];
            }

            /**
             * Sheet 1: Booking list
             */
            private function createBookingListSheet(): BaseExcelExport
            {
                return new class($this->data) extends BaseExcelExport
                {
                    public function headings(): array
                    {
                        return [
                            'ID',
                            'Mã đơn hàng',
                            'Tên khách hàng',
                            'Số điện thoại',
                            'Email',
                            'Số lượng sản phẩm',
                            'Tổng tiền gốc',
                            'Giảm giá',
                            'Thuế',
                            'Tổng tiền',
                            'Trạng thái',
                            'Trạng thái thanh toán',
                            'Phương thức thanh toán',
                            'Nguồn',
                            'Ghi chú',
                            'Ngày tạo',
                        ];
                    }

                    public function map($booking): array
                    {
                        return [
                            $booking->id,
                            $booking->code,
                            $booking->customer_name,
                            $booking->customer_phone,
                            $booking->customer_email,
                            $booking->items->count(),
                            $this->formatCurrency($booking->subtotal),
                            $this->formatCurrency($booking->discount),
                            $this->formatCurrency($booking->tax),
                            $this->formatCurrency($booking->total_amount),
                            $booking->status->label(),
                            $booking->payment_status->label(),
                            $booking->payment_method->label(),
                            $booking->source ?? 'N/A',
                            $booking->note ?? '',
                            $this->formatDateTime($booking->created_at),
                        ];
                    }

                    public function title(): string
                    {
                        return 'Danh sách đơn hàng';
                    }

                    protected function getHeaderColor(): string
                    {
                        return 'E2EFDA'; // Green
                    }
                };
            }

            /**
             * Sheet 2: Booking items detail
             */
            private function createBookingItemsSheet(): BaseExcelExport
            {
                // Transform bookings to flat items collection
                $items = collect();

                foreach ($this->data as $booking) {
                    foreach ($booking->items as $item) {
                        $items->push((object)[
                            'booking_code' => $booking->code,
                            'customer_name' => $booking->customer_name,
                            'product_name' => $item->product_name,
                            'product_code' => $item->product_code,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total_price' => $item->total_price,
                            'start_date' => $item->start_date,
                            'end_date' => $item->end_date,
                            'passengers_count' => $item->passengers->count(),
                        ]);
                    }
                }

                return new class($items) extends BaseExcelExport
                {
                    public function headings(): array
                    {
                        return [
                            'Mã đơn hàng',
                            'Tên khách hàng',
                            'Tên sản phẩm',
                            'Mã sản phẩm',
                            'Số lượng',
                            'Đơn giá',
                            'Thành tiền',
                            'Ngày bắt đầu',
                            'Ngày kết thúc',
                            'Số hành khách',
                        ];
                    }

                    public function map($item): array
                    {
                        return [
                            $item->booking_code,
                            $item->customer_name,
                            $item->product_name,
                            $item->product_code ?? 'N/A',
                            $item->quantity,
                            $this->formatCurrency($item->price),
                            $this->formatCurrency($item->total_price),
                            $this->formatDate($item->start_date),
                            $this->formatDate($item->end_date),
                            $item->passengers_count,
                        ];
                    }

                    public function title(): string
                    {
                        return 'Chi tiết sản phẩm';
                    }

                    protected function getHeaderColor(): string
                    {
                        return 'FFF2CC'; // Yellow
                    }
                };
            }
        };
    }
}

