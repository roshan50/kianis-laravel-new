<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    function purchace(){
        return static::belongsTo(Purchase::class);
    }
}
