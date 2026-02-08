<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\BookingStatusEnum;
use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentMethodEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique()->comment('Mã đơn hàng: BK-2025...'); 
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            
            $table->string('customer_name');
            $table->string('customer_phone')->index(); 
            $table->string('customer_email')->index(); 
            $table->text('note')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->string('status')
                ->default(BookingStatusEnum::Pending->value)
                ->index();

            $table->string('payment_status')
                ->default(PaymentStatusEnum::Unpaid->value)
                ->index();

            $table->string('payment_method')
                ->default(PaymentMethodEnum::COD->value);

            $table->string('source')->default('website')->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
