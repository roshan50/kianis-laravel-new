<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cheque;
use App\Member;

class Purchase extends Model
{
    function cheques(){
        return $this->basMany(Cheque::class);
    }

    function member(){
        return $this->belongsTo(Member::class);
    }

    public function mediated_by()
    {
        return Member::find($this->mediator_id);
    }

    public function findMediatingIDs($member_id)
    {
         return static::where('mediator_id', '=', $member_id)->pluck('member_id');
    }

}
