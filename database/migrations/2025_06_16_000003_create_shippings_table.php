<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('number_shipping');
            $table->dateTime('shipping_date');
            $table->string('supplier')->nullable();
            $table->decimal('total', 15, 2);
            $table->enum('status', ['shipped', 'completed'])->default('shipped');
            $table->dateTime('ship_date')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
