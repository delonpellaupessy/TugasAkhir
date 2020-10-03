<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'users_id',
        'inscurance_price',
        'shipping_price',
        'transaction_status',
        'total_price',
        'code' 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    public function user()
    {
        return $this->belongsTo(User::class,  'users_id', 'id');
    }

    public function transaction_details()
    {
        return $this->belongsTo(transaction_details::class,  'transaction_details_id', 'id');
    }
}
