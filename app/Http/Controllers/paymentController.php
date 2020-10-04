<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\User;
use App\Product;
use App\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // method untuk menerima notifikasi dari midtrans
    public function notifications(Request $request){
      $transaction = $request->transaction_status;
      $type = $request->payment_type;
      $order_id = $request->order_id;
      $fraud = $request->fraud_status;
      $gross_amount = intval($request->gross_amount);

      if ($transaction == 'settlement'){
        // Update transaction status di table transaction to "PAID"
        $updateStatus = Transaction::whereCode($order_id)->update([
          "transaction_status" => "PAID"
        ]);

        // Update revenue di admin = 0,1 x gross_amount
        $revenue_admin = User::whereRoles("ADMIN")->first();
        $revenue_admin->increment('revenue',$gross_amount*0.1);

        // Update revenue di seller
        $trans_id = Transaction::whereCode($order_id)->value('id');
        $product  = TransactionDetail::where('transaction_id', $trans_id)->pluck('products_id')->toArray();
        $user_id = Product::whereIn('id', $product)->value('users_id');
        $revenue_seller = User::whereId($user_id)->first();
        $revenue_seller->increment('revenue', $gross_amount-($gross_amount*0.1));

        }
        else if($transaction == 'pending'){
          // update payment status
            $updateStatus = Transaction::whereCode($order_id)->update([
              "transaction_status" => Str::upper($transaction)
          ]);
        }
        else if ($transaction == 'deny') {
          $updateStatus = Transaction::whereCode($order_id)->update([
            "transaction_status" => Str::upper($transaction)
          ]);
        }
        else if ($transaction == 'expire') {
          $updateStatus = Transaction::whereCode($order_id)->update([
            "transaction_status" => Str::upper($transaction)
          ]);
        }
        else if ($transaction == 'cancel') {
          $updateStatus = Transaction::whereCode($order_id)->update([
            "transaction_status" => Str::upper($transaction)
          ]);
      }
    }
}
