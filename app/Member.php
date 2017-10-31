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
        return $this->hasManyThrough(Member::class, Purchase::class,
                                'member_id','id',
                                'id','mediator_id');
//        $ids= array_unique($this->purchases()->pluck('mediator_id')->toArray());
//        return static::whereIn('id', $ids)->get();
    }

    // select all users which this user is mediating. by the purchase table.
    public function mediating()
    {
        return $this->hasManyThrough(Member::class, Purchase::class,
                                        'mediator_id','id',
                                        'id','member_id');
//        $user_ids = Purchase::MediatingIDs($this->id);
//        return static::whereIn('id', $user_ids)->get();
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
        if($memberPassword == $password ) {
//            if (Hash::check('secret', $hashedPassword)){}
//        if(Hash::check($password,$memberPassword) ) {
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
                "name"   => ($isUser) ? $member->name : '' ,
                "family" => ($isUser) ? $member->last_name : '',
                "grade"  => ($isUser) ? static::grade($member->id) : 0
            ]
        ]);
    }

    public static function generate_password()
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        // $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet);

        for ($i=0; $i < 4; $i++) {
            $token .= $codeAlphabet[rand(0, $max-1)];
        }
        return $token;
    }
}
