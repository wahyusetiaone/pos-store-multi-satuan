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
        Schema::table('shippings', function (Blueprint $table) {
            // Hapus kolom supplier lama jika ada
            if (Schema::hasColumn('shippings', 'supplier')) {
                $table->dropColumn('supplier');
            }
            // Tambahkan kolom supplier_id
            if (!Schema::hasColumn('shippings', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('shipping_date');
                $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippings', function (Blueprint $table) {
            // Kembalikan kolom supplier (string)
            if (!Schema::hasColumn('shippings', 'supplier')) {
                $table->string('supplier')->nullable()->after('shipping_date');
            }
            // Hapus foreign key dan kolom supplier_id
            if (Schema::hasColumn('shippings', 'supplier_id')) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            }
        });
    }
};

