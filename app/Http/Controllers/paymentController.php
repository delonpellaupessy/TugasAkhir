<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Http\Controllers\CheckoutController;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function notification(Request $request){
        dd($request);
    }

    public function completed(Request $request)
    {
        # code...
    }

    public function unfinish(Request $request)
    {
        # code...
    }

    public function failed(Request $request)
    {
        # code...
    }
}
