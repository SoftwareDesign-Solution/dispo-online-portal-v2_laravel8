<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFilter extends Model
{
    public $timestamps = false;

    public function Order()
    {
        return $this->hasOne('App\Models\Order', 'Knr', 'Knr');
    }

    public function Project()
    {
        return $this->hasOne(Project::class, 'Projekt_Nr', 'Projekt_Knr');
    }

}
