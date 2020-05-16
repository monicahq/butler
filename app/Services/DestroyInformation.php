<?php

namespace App\Services;

use App\Models\User;
use App\Models\Information;

class DestroyInformation extends BaseService
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
            'action' => 'information_destroyed',
            'objects' => json_encode([
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
            'information_id' => 'required|integer|exists:information,id',
        ];
    }

    /**
     * Destroy an information.
     *
     * @param array $data
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);
        $this->author = $this->validateAuthorBelongsToAccount($data);

        $this->information = Information::where('account_id', $data['account_id'])
            ->findOrFail($data['information_id']);

        $this->data = $data;

        $this->information->delete();

        $this->createAuditLog();
    }
}
