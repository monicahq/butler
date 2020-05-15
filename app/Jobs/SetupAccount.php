<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Models\Attribute;
use App\Models\Information;
use Illuminate\Bus\Queueable;
use App\Services\CreateTemplate;
use App\Services\CreateAttribute;
use App\Services\CreateInformation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AddDefaultValueToAttribute;
use App\Services\AssociateInformationToTemplate;

class SetupAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    public User $user;

    /**
     * The template instance.
     *
     * @var Template
     */
    protected $template;

    /**
     * Create a new job instance.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->addTemplate();
        $this->addFirstInformation();
    }

    /**
     * Add the first template.
     */
    private function addTemplate(): void
    {
        $request = [
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_template_name'),
        ];

        $this->template = (new CreateTemplate)->execute($request);
    }

    /**
     * Add the first information in the account, like gender, birthdate,...
     */
    private function addFirstInformation(): void
    {
        $this->addGenderInformation();
        $this->addBirthdateInformation();
        $this->addAddressField();
        $this->addPetField();
        $this->addContactInformationField();
        $this->addFoodPreferences();
    }

    /**
     * Add the gender information.
     */
    private function addGenderInformation(): void
    {
        $information = (new CreateInformation)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_gender_information_name'),
            'allows_multiple_entries' => false,
        ]);

        $this->associateToTemplate($information, 1);

        $attribute = (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_gender_information_name'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ]);

        $this->addDefaultValue($attribute, trans('app.default_gender_man'));
        $this->addDefaultValue($attribute, trans('app.default_gender_woman'));
        $this->addDefaultValue($attribute, trans('app.default_gender_other'));
    }

    /**
     * Add the birthdate information.
     */
    private function addBirthdateInformation(): void
    {
        $information = (new CreateInformation)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_birthdate_information'),
            'allows_multiple_entries' => false,
        ]);

        $this->associateToTemplate($information, 2);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_birthdate_information'),
            'type' => 'date',
            'has_default_value' => false,
        ]);
    }

    /**
     * Add the address field information.
     */
    private function addAddressField(): void
    {
        $information = (new CreateInformation)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_address_information'),
            'allows_multiple_entries' => true,
        ]);

        $this->associateToTemplate($information, 3);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_address_label'),
            'type' => 'text',
        ]);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_address_city'),
            'type' => 'text',
        ]);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_address_province'),
            'type' => 'text',
        ]);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_address_postal_code'),
            'type' => 'text',
        ]);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_address_country'),
            'type' => 'text',
        ]);
    }

    /**
     * Add the pet field information.
     */
    private function addPetField(): void
    {
        $information = (new CreateInformation)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_pet_information'),
            'allows_multiple_entries' => true,
        ]);

        $this->associateToTemplate($information, 4);

        $attribute = (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_pet_type'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ]);

        $this->addDefaultValue($attribute, trans('app.default_pet_type_dog'));
        $this->addDefaultValue($attribute, trans('app.default_pet_type_cat'));

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_pet_name'),
            'type' => 'text',
        ]);
    }

    /**
     * Add the contact information panel.
     */
    private function addContactInformationField(): void
    {
        $information = (new CreateInformation)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_contact_information_information'),
            'allows_multiple_entries' => true,
        ]);

        $this->associateToTemplate($information, 5);

        $attribute = (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_contact_information_type_attribute'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ]);

        $this->addDefaultValue($attribute, trans('app.default_contact_information_facebook'));
        $this->addDefaultValue($attribute, trans('app.default_contact_information_email'));
        $this->addDefaultValue($attribute, trans('app.default_contact_information_twitter'));

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_contact_information_value'),
            'type' => 'text',
        ]);
    }

    /**
     * Add the food preferences panel.
     */
    private function addFoodPreferences(): void
    {
        $information = (new CreateInformation)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_food_preferences_information'),
            'allows_multiple_entries' => false,
        ]);

        $this->associateToTemplate($information, 6);

        (new CreateAttribute)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'information_id' => $information->id,
            'name' => trans('app.default_food_preferences_information'),
            'type' => 'textarea',
        ]);
    }

    /**
     * Add a default value to an attribute.
     *
     * @param Attribute $attribute
     * @param string $name
     */
    private function addDefaultValue(Attribute $attribute, string $name): void
    {
        $request = [
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'attribute_id' => $attribute->id,
            'value' => $name,
        ];

        (new AddDefaultValueToAttribute)->execute($request);
    }

    /**
     * Associate the information to the template.
     *
     * @param Information $information
     * @param integer $position
     */
    private function associateToTemplate(Information $information, int $position): void
    {
        (new AssociateInformationToTemplate)->execute([
            'account_id' => $this->user->account_id,
            'author_id' => $this->user->id,
            'template_id' => $this->template->id,
            'information_id' => $information->id,
            'position' => $position,
        ]);
    }
}
