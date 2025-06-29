<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function check(Request $request)
    {
        $code = $request->query('code');

        $voucher = Voucher::where('code', $code)
            ->where('store_id', auth()->user()->current_store_id)
            ->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher tidak ditemukan'
            ]);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher sudah tidak berlaku'
            ]);
        }

        return response()->json([
            'valid' => true,
            'discount_amount' => $voucher->discount_amount,
            'message' => 'Voucher valid'
        ]);
    }
}
