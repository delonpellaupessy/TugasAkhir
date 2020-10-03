<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Http\Controllers\CheckoutController;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function notifications(Request $request){
        dd($request);
    }
}
