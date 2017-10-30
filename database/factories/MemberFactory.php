<?php

use Faker\Generator as Faker;
use Josh\Faker\Faker as persian_faker;

$factory->define(App\Member::class, function (Faker $faker) {
    static $password;
    return [
        'name'      => persian_faker::firstname(),
        'last_name' => persian_faker::lastname(),
        'password'  => $password ?: $password = bcrypt('secret'),
        'mobile'    => persian_faker::mobile(),
        'birth_date'=> rand(1350,1390).'-'.rand(1,12).'-'.rand(1,30),
        'score'     => rand(0,1000),
        'installed' => rand(0,1)
    ];
});
