<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Member extends Model
{
    // properties
    public static $Score;

    // methods

    public function purchases()
    {
         return $this->hasMany(Purchase::class);
    }

    public function cheques()
    {
         return $this->hasManyThrough(Cheque::class, Purchase::class);
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

    public static function loginJSONResponse($password, $member)
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









    // need to refactor this








    public function get_cashes($id)
    {
        return Purchase::select('cash')->where('user_id',$id)->get();
//        return $this->purchases()->pluck('cash')->toArray();
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

    public static function generate_password()
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < 6; $i++) {
            $token .= $codeAlphabet[rand(0, $max-1)];
        }

        return $token;
    }

    public static function score(Request $request){
        $score = new \App\Repository\Score($this->id,$request->cash,$request->cheque,$request->cheque_expired,$request->cheque_passed);
        return $score->total;
    }
}
