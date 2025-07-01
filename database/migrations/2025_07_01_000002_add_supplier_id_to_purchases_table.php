<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('store_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->dropColumn('supplier');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('supplier')->nullable();
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};

