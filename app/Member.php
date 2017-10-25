<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Purchase;
use App\Cheque;

class Member extends Model
{
    public function purchases()
    {
         return $this->hasMany(Purchase::class);
    }

    public function cheques()
    {
         return $this->hasManyThrow(Cheque::class, Purchase::class);
    }

    // select users who mediated this user by selecting the mediotor_id on the
    // purchase table and fetch the mediator_id on the user to pull the out.
    public function mediated_by()
    {
        $ids= array_unique($this->purchases()->pluck('mediator_id')->toArray());

        return static::whereIn('id', $ids)->get();
    }

    // select all users which this user is mediating. by the purchase table.
    public function mediating()
    {
        $user_ids = Purchase::findMediatingIDs($this->user_id);

        return static::whereIn('id', $user_ids)->get();
    }

}
