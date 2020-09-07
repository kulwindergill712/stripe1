<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class custtrans extends Model
{
    protected $fillable = [
        'customer',
        'amount',
        'balance_transaction',
        'payment_method',
    ];
}
