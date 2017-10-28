<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Member extends Model
{
    public function purchases()
    {
         return $this->hasMany(Purchase::class);
    }

    public function get_cashes()
    {
        return $this->purchases()->pluck('cash')->toArray();
    }

    public function cheques()
    {
         return $this->hasManyThrough(Cheque::class, Purchase::class);
    }

    public function purchases_with_cheques()
    {
        return $this->hasMany(Purchase::class)->with('cheques');
    }

    // select users who mediated this user by selecting the mediotor_id on the
    // purchase table and fetch the mediator_id on the user to pull the out.
    public function mediated_by()
    {
//        return $this->hasManyThrough(Member::class,Purchase::class,'member_id','id','member_id','mediator_id');
        $ids= array_unique($this->purchases()->pluck('mediator_id')->toArray());

        return static::whereIn('id', $ids)->get();
    }

    // select all users which this user is mediating. by the purchase table.
    public function mediating()
    {
//        return $this->hasManyThrough(Member::class,Purchase::class,'member_id','id','member_id','mediator_id');
        $user_ids = Purchase::findMediatingIDs($this->id);

        return static::whereIn('id', $user_ids)->get();
    }

    public static function grade($id){
        $members = static::select('score', 'id')->orderBy('score','dsce')->get();
        return $members->pluck('id')->search($id);
    }

    public static function findMemberByUserName($username)
    {
        return static::where('mobile', $username)
             ->first(['id','password','name','last_name']);
    }

    public static function verifyPassword($password, $memberPassword)
    {
        if(Hash::check($password,$memberPassword) ) {
            return true;
        }
        return false;
    }

    public static function generateJSONResponse($password, $member)
    {
        $isUser = ($member && static::verifyPassword($password, $member->password))
         ? true : false;

        return response()->json([
            "result" => ($isUser) ? 1 : 0 ,
            "info" => [
                "name" => ($isUser) ? $member->name : '' ,
                "family" => ($isUser) ? $member->last_name : '',
                "grade" => ($isUser) ? static::grade($member->id) : 0
            ]
        ]);
    }

    public static function get_mediating_cashes($mediator_id)
    {
        return Purchase::where('mediator_id', $mediator_id)
            ->pluck('cash');
    }

    public static function get_mediating_cheques($mediator_id){
        $purchases_ids = Purchase::where('mediator_id', $mediator_id)
            ->pluck('id');
        return Cheque::whereIn('purchase_id',$purchases_ids)->pluck('amount');
    }
}
