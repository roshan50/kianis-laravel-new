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
    private static $UNIT_OF_PAYMENT = 10000000;
    private static $VALID_CHEQUE_SCORE = 2 ;
    private static $CASH_SCORE = 5;

    private $user_id;
    private $cash;
    private $cheque;
    private $cheque_expired;
    private $cheque_passed;
    public $total;

    function __construct($user_id,$cash,$cheque,$cheque_expired,$cheque_passed)
    {
        $this->user_id  = $user_id;
        $this->cash     = $cash;
        $this->cheque   = $cheque;
        $this->cheque_expired = $cheque_expired;
        $this->cheque_passed  = $cheque_passed;
        $this->total = $this->calc_score();
    }

    public function calc_score()
    {
        return static::calc_cash_score($this->cash) +
               static::calc_cheque_score($this->cheque,$this->cheque_expired,$this->cheque_passed)+
               static::calc_Mediating_score($this->user_id);
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