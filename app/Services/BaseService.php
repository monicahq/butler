<?php

namespace App\Services;

use App\Models\User;
use App\Models\Account;
use App\Models\Contact;
use App\Jobs\LogAccountAudit;
use App\Jobs\LogContactAudit;
use Illuminate\Support\Facades\Validator;

abstract class BaseService
{
    /**
     * Get the object to log after executing the service.
     *
     * @return array
     */
    public function logs(): array
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Validate an array against a set of rules.
     *
     * @param array $data
     * @return bool
     */
    public function validateRules(array $data): bool
    {
        //$validator = Validator::make($data, $this->rules());
        $validator = Validator::make($data, $this->rules())->validate();

        /*if ($validator->fails()) {
            dd($validator->errors());
        }*/

        return true;
    }

    /**
     * Validate that the author of the action belongs to the account.
     *
     * @param array $data
     * @return User
     */
    public function validateAuthorBelongsToAccount(array $data): User
    {
        $user = User::where('account_id', $data['account_id'])
            ->with('account')
            ->findOrFail($data['author_id']);

        return $user;
    }

    /**
     * Validate that the contact belongs to the account.
     *
     * @param array $data
     * @return Contact
     */
    public function validateContactBelongsToAccount(array $data): Contact
    {
        $contact = Contact::where('account_id', $data['account_id'])
            ->with('account')
            ->findOrFail($data['contact_id']);

        return $contact;
    }

    /**
     * Create an audit log.
     */
    public function createAuditLog(): void
    {
        LogAccountAudit::dispatch($this->logs())->onQueue('low');
    }

    /**
     * Create a contact log.
     *
     * @param array $data
     */
    public function createContactLog(array $data): void
    {
        LogContactAudit::dispatch($data)->onQueue('low');
    }

    /**
     * Returns the value if it's defined, or false otherwise.
     *
     * @param mixed $data
     * @param mixed $index
     * @return mixed
     */
    public function valueOrFalse($data, $index)
    {
        if (empty($data[$index])) {
            return false;
        }

        return $data[$index];
    }

    /**
     * Checks if the value is empty or null.
     *
     * @param mixed $data
     * @param mixed $index
     *
     * @return mixed
     */
    public function valueOrNull($data, $index)
    {
        if (empty($data[$index])) {
            return;
        }

        return $data[$index] == '' ? null : $data[$index];
    }
}
