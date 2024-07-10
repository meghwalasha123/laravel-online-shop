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
        Schema::create('wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_uni_id', 50)->nullable()->index('user_uni_id_2');
            $table->string('reference_id', 50)->nullable()->index('reference_id');
            $table->string('gateway_order_id', 100)->nullable();
            $table->string('gateway_payment_id', 100);
            $table->string('transaction_code', 100)->nullable()->index('transaction_code');
            $table->text('wallet_history_description')->nullable();
            $table->decimal('transaction_amount', 10)->nullable()->default(0);
            $table->decimal('amount', 10)->nullable()->default(0);
            $table->string('main_type', 50)->nullable()->index('main_type');
            $table->string('created_by', 50)->nullable();
            $table->decimal('admin_percentage', 10)->nullable()->default(0);
            $table->decimal('gst_amount', 10)->nullable()->default(0);
            $table->decimal('astro_amount', 10)->nullable()->default(0);
            $table->decimal('admin_amount', 10)->nullable()->default(0);
            $table->decimal('tds_amount', 10)->nullable()->default(0);
            $table->decimal('offer_amount', 10)->nullable()->default(0);
            $table->decimal('gateway_charge', 10)->nullable()->default(0);
            $table->decimal('coupan_amount', 10)->nullable()->default(0);
            $table->string('currency', 10)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('where_from', 10)->nullable();
            $table->boolean('status')->default(false)->index('status');
            $table->integer('gift_status')->nullable();
            $table->boolean('offer_status')->nullable()->default(false);
            $table->string('offer_code')->nullable();
            $table->dateTime('created_at')->nullable()->index('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index(['user_uni_id', 'reference_id', 'transaction_code', 'main_type', 'status', 'created_at'], 'user_uni_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
