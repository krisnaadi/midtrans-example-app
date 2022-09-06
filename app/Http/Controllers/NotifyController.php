<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = $request->json()->all();
        $transction = Transaction::where('invoice_number', $data['order_id'])->first();
        if (!$transction) {
            return response(['success' => false, 'message' => 'order not found'], 404);
        }

        if ($data['transaction_status'] == 'settlement' || $data['transaction_status'] == 'capture') {
            $transction->is_paid = true;
        }

        $transction->save();

        return response(['success' => true, 'message' => 'data updated'], 200);
    }
}
