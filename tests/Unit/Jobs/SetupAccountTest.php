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
            'name' => trans('app.default_birthdate_information'),
            'allows_multiple_entries' => false,
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_address_information'),
            'allows_multiple_entries' => true,
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_pet_information'),
            'allows_multiple_entries' => true,
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_contact_information_information'),
            'allows_multiple_entries' => true,
        ]);

        $this->assertDatabaseHas('information', [
            'account_id' => $michael->account_id,
            'name' => trans('app.default_food_preferences_information'),
            'allows_multiple_entries' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_gender_information_name'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_birthdate_information'),
            'type' => 'date',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_label'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_city'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_province'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_postal_code'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_address_country'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_pet_type'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_pet_name'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_contact_information_type_attribute'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_contact_information_value'),
            'type' => 'text',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attributes', [
            'name' => trans('app.default_food_preferences_information'),
            'type' => 'textarea',
            'has_default_value' => false,
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_gender_man'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_gender_woman'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_gender_other'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_pet_type_dog'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_pet_type_cat'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_contact_information_facebook'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_contact_information_email'),
        ]);

        $this->assertDatabaseHas('attribute_default_values', [
            'value' => trans('app.default_contact_information_twitter'),
        ]);
    }
}
