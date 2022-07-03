<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCartItem extends Model
{
    use HasFactory;

    public function OrderCart()
    {
        return $this->hasOne('App\Models\OrderCart', 'id', 'ordercart_id');
    }

    public function Order()
    {
        return $this->hasOne('App\Models\Order', 'Knr', 'Knr');
    }

}
