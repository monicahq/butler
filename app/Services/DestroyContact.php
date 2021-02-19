<?php

namespace App\Services;

use App\Models\User;
use App\Models\Contact;
use App\Interfaces\ServiceInterface;

class DestroyContact extends BaseService implements ServiceInterface
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
            'action' => 'contact_destroyed',
            'objects' => json_encode([
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
            'contact_id' => 'required|integer|exists:contacts,id',
        ];
    }

    /**
     * Destroy a contact.
     *
     * @param array $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);
        $this->contact = $this->validateContactBelongsToAccount($data);

        $this->data = $data;

        $this->contact->delete();

        $this->createAuditLog();
    }
}
