<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contact;

class CreateContact extends BaseService
{
    private User $author;
    private array $data;
    private Contact $contact;

    /**
     * Get the data to log after calling the service.
     *
     * @return array
     */
    public function logs(): array
    {
        return [
            'account_id' => $this->data['account_id'],
            'author_id' => $this->author->id,
            'author_name' => $this->author->name,
            'action' => 'contact_created',
            'objects' => json_encode([
                'contact_id' => $this->contact->id,
                'contact_name' => $this->contact->name,
            ]),
        ];
    }

    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'is_dummy' => 'nullable|boolean',
        ];
    }

    /**
     * Create a contact.
     *
     * @param array $data
     * @return Contact
     */
    public function execute(array $data): Contact
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);
        $this->data = $data;

        $this->contact = Contact::create([
            'account_id' => $data['account_id'],
            'first_name' => $data['first_name'],
            'last_name' => $this->valueOrNull($data, 'last_name'),
            'middle_name' => $this->valueOrNull($data, 'middle_name'),
            'surname' => $this->valueOrNull($data, 'surname'),
            'is_dummy' => $this->valueOrFalse($data, 'first_name'),
        ]);

        $this->createAuditLog();

        return $this->contact;
    }
}
