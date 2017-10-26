<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Post::class, function (Faker $faker) {
    return [
        'subject' => $faker->text(),
        'body' => $faker->text(1000),
        'user_id' => \App\Models\User::all()->random()->id,
        'is_approved' => $faker->boolean(50),
    ];
});
