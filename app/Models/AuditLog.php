<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'author_id',
        'author_name',
        'action',
        'objects',
    ];

    /**
     * Get the Account record associated with the audit log.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the User record associated with the audit log.
     *
     * @return BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the JSON object.
     *
     * @param mixed $value
     * @return array
     */
    public function getObjectAttribute($value)
    {
        return json_decode($this->objects);
    }
}
