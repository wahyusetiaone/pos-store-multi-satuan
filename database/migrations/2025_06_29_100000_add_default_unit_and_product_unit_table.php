<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('default_unit_id')->nullable()->after('category_id');
            $table->foreign('default_unit_id')->references('id')->on('units')->onDelete('set null');
        });

        Schema::create('product_unit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('unit_id');
            $table->decimal('conversion_factor', 15, 4)->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->unique(['product_id', 'unit_id']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['default_unit_id']);
            $table->dropColumn('default_unit_id');
        });
        Schema::dropIfExists('product_unit');
    }
};

