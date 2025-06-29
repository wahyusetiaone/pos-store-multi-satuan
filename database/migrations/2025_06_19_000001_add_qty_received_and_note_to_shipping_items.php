<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyReceivedAndNoteToShippingItems extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipping_items', function (Blueprint $table) {
            $table->integer('qty_received')->nullable()->after('quantity');
            $table->text('note')->nullable()->after('qty_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_items', function (Blueprint $table) {
            $table->dropColumn(['qty_received', 'note']);
        });
    }
}
