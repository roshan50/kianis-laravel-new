<?php
namespace App\Repository;
/**
 * Created by PhpStorm.
 * User: Lemon-PC
 * Date: 10/28/2017
 * Time: 9:17 AM
 */
use App\Member;

class Score
{
    const FESTIVAL_END_DATE = '1396/10/15';
    const UNIT_OF_PAYMENT = 10000000;
    const VALID_CHEQUE_SCORE = 2 ;
    const CASH_SCORE = 5;

    public function calc_score($user_id,$cash,$cheque,$cheque_expired,$cheque_passed)
    {
        $score = calc_cash_score($cash) + calc_cheque_score($cheque,$cheque_expired,$cheque_passed);

        $med_score = calc_Mediating_score($user_id);

        return ($score + $med_score);
    }

    public static function calc_Mediating_score($user_id)
    {
        $score = 0;

        $cashes  = Member::get_mediating_cashes($user_id);
        for($i = 0; $i < count($cashes); $i++){
            $score +=  static::calc_cash_score((int)$cashes[$i]);
        }

        $cheques = Member::get_mediating_cheques($user_id);
        for($i = 0; $i < count($cheques); $i++){
            $score +=  static::calc_cash_score((int)$cheques[$i]);
        }

        return $score;
    }

    /**
     * @param $buy_cash
     * @param $cheques_expired
     * @param $cheques_passed
     * @param $buy_cheque
     * @return mixed
     */
//    function calc_update_score($cashs, $buy_cheques,$cheques_expired,$cheques_passed, $mediating)
//    {
//        $buy_cashs = explode(",", $cashs);
//        $buy_cheques = explode(",", $buy_cheques);
//        $cheques_expired = explode(",", $cheques_expired);
//        $cheques_passed = explode(",", $cheques_passed);
//
//        $score = 0;
//        for($i=0; $i<count($buy_cashs); $i++){
//            $score += calc_cash_score($buy_cashs[$i])
//                + calc_cheque_score($buy_cheques[$i],$cheques_expired[$i],$cheques_passed[$i]);
//        }
//
//        if(!$mediating) {
//            $med_score = calc_referred_score($mediating);
//        }else{
//            $med_score = 0;
//        }
//        return ($score + $med_score);
//    }

    /**
     * @param $buy_cash
     * @return mixed
     */
    public static function calc_cash_score($cash)
    {
        $score = floor($cash / self::UNIT_OF_PAYMENT) * self::CASH_SCORE;
        return $score;
    }

    /**
     * @param $cheques
     * @return mixed
     */
    function calc_cheque_score($cheques,$cheques_expired,$cheques_passed)
    {
        $score = 0;
        for($i=0; $i<count($cheques); $i++){
            $expired = if_cheque_expired($cheques_expired[$i]);
            $score += ($cheques_passed[$i] === 'f' && !$expired) ? floor($cheques[$i] / self::UNIT_OF_PAYMENT) * self::VALID_CHEQUE_SCORE : 0;
        }

        return $score;
    }

    function if_cheque_expired($d1){
        $d1 = explode("/", $d1);
        $d2 = explode("/", self::FESTIVAL_END_DATE);

        $y = $d1[0] - $d2[0];
        $m = $d1[1] - $d2[1];
        $d = $d1[2] - $d2[2];

        if($y == 0){
            if($m == 0){
                if($d > 0){
                    return true;
                }
            }else{
                if($m > 0){
                    return true;
                }
            }
        }else{
            if($y>0) return true;
        }
        return false;
    }

}