<?php

use Faker\Generator as Faker;

$factory->define(App\Cheque::class, function (Faker $faker) {
    return [
        'purchase_id'   => rand(1,10),
        'expire_date'   => rand(1396,1397).'-'.rand(1,12).'-'.rand(1,30),
        'amount'        => rand(10000000,100000000)
    ];
});
