<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cheque;
use App\Member;

class Purchase extends Model
{
    function member(){
        return $this->belongsTo(Member::class);
    }

    public function mediated_by()
    {
        return Member::find($this->mediator_id);
    }

    public static function MediatingIDs($member_id)
    {
         return static::where('mediator_id', '=', $member_id)->pluck('member_id');
    }

    public static function cashes($id)
    {
        return static::select('cash')->where('member_id',$id)->get();
    }

    function cheques(){
        return $this->hasMany(Cheque::class);
    }

    public static function user_cheques($user_id){
        return static::where('member_id',$user_id)->cheques;
    }

}
