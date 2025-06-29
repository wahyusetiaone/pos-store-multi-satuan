<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('voucher_code')->nullable()->after('fixed_discount');
            $table->decimal('voucher_discount', 10, 2)->default(0)->after('voucher_code');
            $table->decimal('grand_total', 10, 2)->default(0)->after('total');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['voucher_code', 'voucher_discount', 'total', 'grand_total', 'paid']);
        });
    }
};
