<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'author_id' => function (array $attributes) {
                return User::factory()->create([
                    'account_id' => $attributes['account_id'],
                ])->id;
            },
            'action' => 'account_created',
            'author_name' => 'Dwight Schrute',
            'objects' => '{"user": 1}',
        ];
    }
}
