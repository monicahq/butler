<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\SetupAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SetupAccountTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_an_account_up(): void
    {
        $michael = factory(User::class)->create();

        SetupAccount::dispatch($michael);

        $this->assertDatabaseHas('templates', [
            'account_id' => $michael->account_id,
            'name' => 'Default template',
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_gender_information_name'),
            'allows_multiple_entries' => false,
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_birthdate_attribute'),
            'allows_multiple_entries' => false,
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_address_attribute'),
            'allows_multiple_entries' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_gender_information_name'),
            'has_default_value' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_birthdate_attribute'),
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_label'),
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_city'),
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_province'),
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_postal_code'),
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_country'),
            'has_default_value' => false,
        ]);
    }
}
