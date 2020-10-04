<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Cart;
use App\Transaction;
use App\TransactionDetail;

use Exception;

use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    public function process (Request $request)
    {
        //save user data
        $user = Auth::user();
        $user->update($request->except('total_price'));

        //proses checkout
        $code = 'STORE-' . mt_rand(0000,9999);
        $carts = Cart::with(['product', 'user'])
                    ->where('users_id', Auth::user()->id)
                    ->get();

        //transaction create
        $transaction = Transaction::create([
            'users_id' => Auth::user()->id,
            'inscurance_price' => 0,
            'shipping_price' => 0,
            'total_price' => $request->total_price,
            'transaction_status' => 'PENDING',
            'code' => $code
        ]);

        foreach ($carts as $cart) {
            $trx = 'TRX-' . mt_rand(0000,9999);

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'products_id' => $cart->product->id,
                'price' => $cart->product->price,
                'shipping_status' => 'PENDING',
                'resi' => mt_rand(0000,9999),
                'code' => $trx
            ]);
        }

        //delete cart data
        Cart::where('users_id', Auth::user()->id)->delete();

        //konfigurasi mitrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        //buat array untuk dikirm ke midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => $code,
                'gross_amount' => (int) $request->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'enabled_payments' => [
                'gopay', 'permata_va', 'bank_transfer'
            ],
            'vtweb' => []
        ];

        try {
            // Get Snap Payment Page URL
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;

            // Redirect to Snap Payment Page
           return redirect($paymentUrl);
        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    public function processAjax (Request $request)
    {
      //save user data
      $user = Auth::user();
      $user->update($request->except('total_price'));

      //proses checkout
      $code = 'STORE-' . mt_rand(0000,9999);
      $carts = Cart::with(['product', 'user'])
                  ->where('users_id', Auth::user()->id)
                  ->get();

      //transaction create
      $transaction = Transaction::create([
          'users_id' => Auth::user()->id,
          'inscurance_price' => 0,
          'shipping_price' => 0,
          'total_price' => $request->total_price,
          'transaction_status' => 'PENDING',
          'code' => $code
      ]);

      foreach ($carts as $cart) {
          $trx = 'TRX-' . mt_rand(0000,9999);

          TransactionDetail::create([
              'transaction_id' => $transaction->id,
              'products_id' => $cart->product->id,
              'price' => $cart->product->price,
              'shipping_status' => 'PENDING',
              'resi' => mt_rand(0000,9999),
              'code' => $trx
          ]);
      }

      //delete cart data
      Cart::where('users_id', Auth::user()->id)->delete();

      //konfigurasi mitrans
      Config::$serverKey = config('services.midtrans.serverKey');
      Config::$isProduction = config('services.midtrans.isProduction');
      Config::$isSanitized = config('services.midtrans.isSanitized');
      Config::$is3ds = config('services.midtrans.is3ds');

      //buat array untuk dikirm ke midtrans
      $midtrans = [
          'transaction_details' => [
              'order_id' => $code,
              'gross_amount' => (int) $request->total_price,
          ],
          'customer_details' => [
              'first_name' => Auth::user()->name,
              'email' => Auth::user()->email,
          ],
          'enabled_payments' => [
              'gopay', 'permata_va', 'bank_transfer'
          ],
          'vtweb' => []
      ];

      $snaptoken = Snap::getSnapToken($midtrans);
      return response()->json($snaptoken, 200);
    }

    public function callback (Request $request)
    {

        dd($request);
        $transaction = $request->transaction_status;
        $type = $request->payment_type;
        $order_id = $request->order_id;
        $fraud = $request->fraud_status;
        $signature_key = $transaction->signature_key;

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card'){
                if($fraud == 'challenge'){
                // TODO set payment status in merchant's database to 'Challenge by FDS'
                // TODO merchant should decide whether this transaction is authorized or not in MAP
                echo "Transaction order_id: " . $order_id ." is challenged by FDS";
                }
                else {
                // TODO set payment status in merchant's database to 'Success'
                echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
                }
            }
        } else if ($transaction == 'settlement') {
                // update payment status
                $updateStatus = Transaction::whereCode($order_id)->update([
                    "transaction_status" => "PAID"
                ]);
            }
            else if($transaction == 'pending'){
                // update payment status
                $updateStatus = Transaction::whereCode($order_id)->update([
                    "transaction_status" => Str::upper($transaction)
                ]);
            }
            else if ($transaction == 'deny') {
                // update payment status
                $updateStatus = Transaction::whereCode($order_id)->update([
                    "transaction_status" => Str::upper($transaction)
                ]);
            }
            else if ($transaction == 'expire') {
                // update payment status
                $updateStatus = Transaction::whereCode($order_id)->update([
                    "transaction_status" => Str::upper($transaction)
                ]);
            }
            else if ($transaction == 'cancel') {
                // update payment status
                $updateStatus = Transaction::whereCode($order_id)->update([
                    "transaction_status" => Str::upper($transaction)
                ]);
        }
    }

}
