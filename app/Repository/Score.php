<?php

namespace App\Repository;

use App\Member;

class Score
{
    private static $UNIT_OF_PAYMENT = 10000000;
    private static $VALID_CHEQUE_SCORE = 2 ;
    private static $CASH_SCORE = 5;

    public $total;
    private $cash;
    private $cheque;
    private $user_id;
    private $cheque_passed;
    private $cheque_expired;

    function __construct($user_id,$cash,$cheque,$cheque_expired,$cheque_passed)
    {
        $this->cash             = $cash;
        $this->total            = $this->score();
        $this->cheque           = $cheque;
        $this->user_id          = $user_id;
        $this->cheque_passed    = $cheque_passed;
        $this->cheque_expired   = $cheque_expired;
    }

    private function score()
    {
        return $this->cash_score($this->cash) +
               $this->mediating_score($this->user_id)+
               $this->cheque_score(
                $this->cheque,$this->cheque_expired,$this->cheque_passed
            );
    }

    private function mediating_score($user_id)
    {
        $score = 0;

        $cashes  = Member::mediating_cashes($user_id);
        for($i = 0; $i < count($cashes); $i++){
            $score +=  $this->cash_score((int)$cashes[$i]);
        }

        $cheques = Member::mediating_cheques($user_id);
        for($i = 0; $i < count($cheques); $i++){
            $score += $this->cash_score((int)$cheques[$i]);
        }

        return $score;
    }

    /**
     * @param $buy_cash
     * @return mixed
     */
    private function cash_score($cash)
    {
        $score = floor($cash / self::UNIT_OF_PAYMENT) * self::CASH_SCORE;
        return $score;
    }

    /**
     * @param $cheques
     * @return mixed
     */
    private function cheque_score($cheques,$cheques_expired,$cheques_passed)
    {
        $score = 0;
        for($i=0; $i<count($cheques); $i++){
            $expired = $this->cheque_expired($cheques_expired[$i]);
            $score += ($cheques_passed[$i] === 'f' && !$expired)
             ?
             floor($cheques[$i] / self::UNIT_OF_PAYMENT) * self::VALID_CHEQUE_SCORE
             :
             0;
        }

        return $score;
    }

    private function cheque_expired($d1){
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