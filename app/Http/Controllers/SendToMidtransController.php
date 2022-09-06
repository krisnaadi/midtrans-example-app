<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMidtransRequest;
use App\Models\Transaction;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SendToMidtransController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SendMidtransRequest $request)
    {
        $transaction = Transaction::create($request->validated());
        $transaction->details()->createMany($request->details);
        $client = new Client();
        $url = config('midtrans.url_charge');
        $params = [
            'transaction_details' => [
                'gross_amount' => $request->total,
                'order_id' => $request->invoice_number
            ],
            'credit_card' =>  [
                'secure' => true
            ],
            'customer_details' => [
                'email' => $request->email,
                'first_name' => $request->name,
                'last_name' => '',
                'phone' => $request->phone
            ],

        ];

        $i = 1;
        foreach ($request->details as $detail) {
            $param['item_details'][] =
                [
                    'id' => $i,
                    'price' => $detail['amount'],
                    'quantity' => $detail['qty'],
                    'name' => $detail['name'],
                ];
            $i++;
        }

        $headers = [
            'Authorization' => 'Basic ' . base64_encode(config('midtrans.server_key') . ':'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
        $response = $client->request('POST', $url, [
            'json' => $params,
            'headers' => $headers,
            'verify'  => false,
        ]);

        return response($response->getBody());
    }
}
