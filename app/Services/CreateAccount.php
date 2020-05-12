<?php

namespace App\Services;

use App\Models\User;
use App\Models\Account;
use App\Jobs\SetupAccount;
use Illuminate\Support\Facades\Hash;

class CreateAccount extends BaseService
{
    private User $user;
    private Account $account;

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

        SetupAccount::dispatch($this->user)->onQueue('low');

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
}
