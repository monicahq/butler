<?php

namespace App\Services\Account;

use App\Models\AuditLog;
use App\Services\BaseService;
use App\Interfaces\ServiceInterface;

class LogAccountAction extends BaseService implements ServiceInterface
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
            'author_name' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'objects' => 'required|json',
        ];
    }

    /**
     * Log an action that happened in an account.
     *
     * @param array $data
     * @return AuditLog
     */
    public function execute(array $data): AuditLog
    {
        $this->validateRules($data);
        $this->validateAuthorBelongsToAccount($data);

        return AuditLog::create([
            'account_id' => $data['account_id'],
            'author_id' => $data['author_id'],
            'author_name' => $data['author_name'],
            'action' => $data['action'],
            'objects' => $data['objects'],
        ]);
    }
}
