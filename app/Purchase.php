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

//    public static function cash_of_purchase($purchase_id)
//    {
//        return static::find($purchase_id)->get(['cash']);
//    }

    function cheques(){
        return $this->hasMany(Cheque::class);
    }

    public static function user_cheques($user_id){
        return static::where('member_id',$user_id)->cheques;
    }

//    public static function cheque_of_purchase($purchase_id)
//    {
//        return static::find($purchase_id)->cheques;
//    }

}
