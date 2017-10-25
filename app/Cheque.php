<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    function purchase(){
        return static::belongsTo(Purchase::class);
    }
}
