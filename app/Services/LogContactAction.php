<?php

namespace App\Services;

use App\Models\ContactLog;
use App\Interfaces\ServiceInterface;

class LogContactAction extends BaseService implements ServiceInterface
{
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
            'author_name' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'objects' => 'required|json',
        ];
    }

    /**
     * Log an action that happened to a contact.
     *
     * @param array $data
     * @return ContactLog
     */
    public function execute(array $data): ContactLog
    {
        $this->validateRules($data);
        $this->validateAuthorBelongsToAccount($data);
        $this->validateContactBelongsToAccount($data);

        return ContactLog::create([
            'contact_id' => $data['contact_id'],
            'author_id' => $data['author_id'],
            'author_name' => $data['author_name'],
            'action' => $data['action'],
            'objects' => $data['objects'],
        ]);
    }
}
