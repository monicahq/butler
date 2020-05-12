<?php

namespace App\Services;

use App\Models\User;
use App\Models\Account;
use App\Models\Template;
use App\Models\Attribute;
use Illuminate\Support\Facades\Hash;

class CreateAccount extends BaseService
{
    private User $user;
    private Account $account;
    private Template $template;

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|unique:users,email|email|max:255',
            'password' => 'required|alpha_dash|string|max:255',
        ];
    }

    /**
     * Create an account.
     *
     * @param array $data
     * @return User
     */
    public function execute(array $data): User
    {
        $this->validateRules($data);

        $this->account = Account::create();

        $this->addFirstUser($data);
        $this->addTemplate($data);
        $this->addFirstAttributes($data);

        return $this->user;
    }

    /**
     * Add the first user in the account.
     *
     * @param array $data
     */
    private function addFirstUser(array $data): void
    {
        $this->user = User::create([
            'account_id' => $this->account->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Add the first template.
     *
     * @param array $data
     */
    private function addTemplate(array $data): void
    {
        $request = [
            'account_id' => $this->account->id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_template_name'),
        ];

        $this->template = (new CreateTemplate)->execute($request);
    }

    /**
     * Add the first attributes in the account, like gender, birthdate,...
     *
     * @param array $data
     */
    private function addFirstAttributes(array $data): void
    {
        $this->addGenderAttribute();
        $this->addBirthdateAttribute();
    }

    /**
     * Add the gender attribute.
     */
    private function addGenderAttribute(): void
    {
        $request = [
            'account_id' => $this->account->id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_gender_attribute_name'),
            'type' => 'dropdown',
            'has_default_value' => true,
        ];

        $attribute = (new CreateAttribute)->execute($request);

        // associate the attribute to the template
        (new AssociateAttributeToTemplate)->execute([
            'account_id' => $this->account->id,
            'author_id' => $this->user->id,
            'template_id' => $this->template->id,
            'attribute_id' => $attribute->id,
            'position' => 1,
        ]);

        $this->addDefaultValue($attribute, trans('app.default_gender_man'));
        $this->addDefaultValue($attribute, trans('app.default_gender_woman'));
        $this->addDefaultValue($attribute, trans('app.default_gender_other'));
    }

    /**
     * Add the birthdate attribute.
     */
    private function addBirthdateAttribute(): void
    {
        $request = [
            'account_id' => $this->account->id,
            'author_id' => $this->user->id,
            'name' => trans('app.default_birthdate_attribute'),
            'type' => 'date',
            'has_default_value' => false,
        ];

        $attribute = (new CreateAttribute)->execute($request);

        // associate the attribute to the template
        (new AssociateAttributeToTemplate)->execute([
            'account_id' => $this->account->id,
            'author_id' => $this->user->id,
            'template_id' => $this->template->id,
            'attribute_id' => $attribute->id,
            'position' => 2,
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
            'account_id' => $this->account->id,
            'author_id' => $this->user->id,
            'attribute_id' => $attribute->id,
            'value' => $name,
        ];

        (new AddDefaultValueToAttribute)->execute($request);
    }
}
