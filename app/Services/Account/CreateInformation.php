<?php

namespace App\Services\Account;

use App\Models\User;
use App\Models\Information;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;

class CreateInformation extends BaseService implements ServiceInterface
{
    private User $author;
    private array $data;
    private Information $information;

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
            'action' => 'information_created',
            'objects' => json_encode([
                'information_id' => $this->information->id,
                'information_name' => $this->information->name,
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
            'name' => 'required|string|max:255',
            'allows_multiple_entries' => 'nullable|boolean',
        ];
    }

    /**
     * Create an information.
     *
     * @param array $data
     * @return Information
     */
    public function execute(array $data): Information
    {
        $this->data = $data;
        $this->validate();

        $this->information = Information::create([
            'account_id' => $data['account_id'],
            'name' => $data['name'],
            'allows_multiple_entries' => $this->valueOrFalse($data, 'allows_multiple_entries'),
        ]);

        $this->createAuditLog();

        return $this->information;
    }

    private function validate(): void
    {
        $this->validateRules($this->data);
        $this->author = $this->validateAuthorBelongsToAccount($this->data);
    }
}
