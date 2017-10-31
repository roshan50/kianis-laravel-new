<?php

use Faker\Generator as Faker;

$factory->define(App\Purchase::class, function (Faker $faker) {
    $member_id = rand(2,5);
    return [
        'member_id'      => $member_id,
        'mediator_id'    => $member_id-1,
//        'which_purchase' => rand(1,3),
        'cash'           => rand(10000000,100000000)
    ];
});
