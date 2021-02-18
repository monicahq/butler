<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Models\ContactLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $account = Account::factory()->create();

        return [
            'contact_id' => Contact::factory(),
            'author_id' => function () use ($account) {
                return User::factory()->create([
                    'account_id' => $account->id,
                ])->id;
            },
            'action' => 'account_created',
            'author_name' => 'Dwight Schrute',
            'objects' => '{"user": 1}',
        ];
    }
}
