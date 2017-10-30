<?php

namespace App\Repository;

use App\Cheque;
use App\Member;
use App\Purchase;

class Score
{
    private static $UNIT_OF_PAYMENT = 10000000;
    private static $VALID_CHEQUE_SCORE = 2 ;
    private static $CASH_SCORE = 5;

    public $total;
    private $cash;
    private $cheques;
    private $user_id;
    private $isRejected;
    private $purchase_ids;
    private $cheques_expired;

    function __construct($user_id,$request)
    {
        $this->user_id          = $user_id;
        $this->cash             = $request->cash;
        $this->cheques           = $request->cheques;
        $this->isRejected       = $request->isRejected;
        $this->cheques_expired   = $request->cheques_expired;
        $this->purchase_ids     = $request->purchase_ids;
        $this->total            = $this->score();
    }

    private function score()
    {
        return $this->cash_score() +
               $this->cheque_score()+
               $this->mediating_score();
    }

    private function mediating_score()
    {
        $score = 0;//dd($this->purchase_ids);
        $purchase_ids = explode(',',$this->purchase_ids);
        foreach ($purchase_ids as $purchase_id){
            $cash = Purchase::find($purchase_id)->cash;
            $score += $this->calc_cash_score($cash);

            $cheques = Purchase::find($purchase_id)->cheques->toArray();
            for ($i = 0; $i < count($cheques); $i++) {
                $expired = false;//$this->cheque_expired($cheques[$i]['expire_date']);
                if($cheques[$i]['isRejected'] === 0 && !$expired)
                    $score += $this->calc_cheque_score($cheques[$i]['amount']);
            }
        }

        return $score;
    }

    /**
     * @param $buy_cash
     * @return mixed
     */
    private function cash_score()
    {
        $score = $this->calc_cash_score($this->cash);

        if($this->user_id){
            foreach (Purchase::cashes($this->user_id)->toArray() as $cash){
                $score += $this->calc_cash_score($cash['cash']);
            }
        }

        return $score;
    }

    private function calc_cash_score($cash){
        return floor($cash / self::$UNIT_OF_PAYMENT) * self::$CASH_SCORE;
    }

    /**
     * @param $cheques
     * @return mixed
     */
    private function cheque_score()
    {
        $score = 0;
        $cheques = explode(',',$this->cheques);
        $isRejected = explode(',',$this->isRejected);
        $cheques_expired = explode(',',$this->cheques_expired);

        for ($i = 0; $i < count($cheques); $i++) {
            $expired = $this->cheque_expired($cheques_expired[$i]);
            if ($isRejected[$i] == 0 && !$expired) {
                $score += $this->calc_cheque_score($cheques[$i]);
            }
        }
        if($this->user_id) {
            $purchase_ids = Purchase::where('member_id',$this->user_id)->pluck
            ('id');
//            dd($purchase_ids);
            $old_cheques = Cheque::whereIn('purchase_id', $purchase_ids)->get
            ()->toArray();
//            dd($old_cheques);
            foreach ($old_cheques as $item) {
                $expired = false;//$this->cheque_expired($item['expire_date']);
                if($item['isRejected'] === 0 && !$expired)
                $score += $this->calc_cheque_score($item['amount']);
            }
        }
//dd($score);
        return $score;
    }

    private function calc_cheque_score($cheque){
        return floor($cheque /self::$UNIT_OF_PAYMENT)
            * self::$VALID_CHEQUE_SCORE;
    }

    private function cheque_expired($d1){
        $d1 = explode("/", $d1);
        $d2 = explode("/", \App\Repository\Info::$FESTIVAL_END_DATE);

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