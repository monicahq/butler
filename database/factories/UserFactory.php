<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Models\AuditLog;
use App\Models\Template;
use App\Models\Attribute;
use App\Models\ContactLog;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Models\AttributeDefaultValue;

$factory->define(Account::class, function () {
    return [];
});

$factory->define(User::class, function (Faker $faker) {
    return [
        'account_id' => factory(Account::class)->create()->id,
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName(),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'account_id' => factory(Account::class)->create()->id,
        'first_name' => $faker->firstName(),
    ];
});

$factory->define(Attribute::class, function () {
    return [
        'account_id' => factory(Account::class)->create()->id,
        'name' => 'gender',
        'type' => 'text',
    ];
});

$factory->define(AttributeDefaultValue::class, function () {
    return [
        'attribute_id' => factory(Attribute::class)->create()->id,
        'value' => 'male',
    ];
});

$factory->define(Template::class, function () {
    return [
        'account_id' => factory(Account::class)->create()->id,
        'name' => 'business',
    ];
});

$factory->define(AuditLog::class, function () {
    return [
        'account_id' => factory(Account::class)->create()->id,
        'author_id' => function (array $data) {
            return factory(User::class)->create([
                'account_id' => $data['account_id'],
            ])->id;
        },
        'action' => 'account_created',
        'author_name' => 'Dwight Schrute',
        'objects' => '{"user": 1}',
    ];
});

$factory->define(ContactLog::class, function () {
    $account = factory(Account::class)->create();

    return [
        'contact_id' => factory(Contact::class)->create(['account_id' => $account->id])->id,
        'author_id' => function () use ($account) {
            return factory(User::class)->create([
                'account_id' => $account->id,
            ])->id;
        },
        'action' => 'account_created',
        'author_name' => 'Dwight Schrute',
        'objects' => '{"user": 1}',
    ];
});
