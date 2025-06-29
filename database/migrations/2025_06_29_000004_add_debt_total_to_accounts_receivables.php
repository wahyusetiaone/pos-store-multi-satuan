<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accounts_receivables', function (Blueprint $table) {
            $table->decimal('debt_total', 10, 2)->after('pending_payment')->default(0);
        });
    }

    public function down()
    {
        Schema::table('accounts_receivables', function (Blueprint $table) {
            $table->dropColumn('debt_total');
        });
    }
};
