<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add store_id to users table (user bisa bekerja di multiple store)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('current_store_id')->nullable();
            $table->foreign('current_store_id')->references('id')->on('stores')->onDelete('set null');
        });

        // Create user_store pivot table (untuk multiple store per user)
        Schema::create('user_store', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('store_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['user_id', 'store_id']);
        });

        // Add store_id to products
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Add store_id to categories
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Add store_id to customers
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Add store_id to sales
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Add store_id to purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Add store_id to finances
        Schema::table('finances', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Add store_id to settings (untuk setting per store)
        Schema::table('settings', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('id')->nullable();
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            // Drop existing unique key and add new composite unique
            $table->dropUnique(['key']);
            $table->unique(['store_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
            $table->unique(['key']);
        });

        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::dropIfExists('user_store');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_store_id']);
            $table->dropColumn('current_store_id');
        });
    }
};
