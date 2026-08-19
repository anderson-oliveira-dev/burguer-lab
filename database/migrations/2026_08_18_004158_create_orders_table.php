<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->enum('type', ['delivery', 'pickup'])->default('delivery');

            $table->string('address')->nullable();
            $table->string('phone')->nullable();

            $table->enum('payment_method', ['cash', 'card', 'pix'])->default('cash');

            $table->enum('status', [
                'awaiting_confirmation',
                'preparing',
                'ready_for_delivery',
                'delivered',
                'canceled'
            ])->default('awaiting_confirmation');

            $table->decimal('total_price', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);

            $table->text('observations')->nullable();

            $table->timestamp('estimated_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
