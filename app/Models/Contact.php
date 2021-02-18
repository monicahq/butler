<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'middle_name',
        'surname',
    ];

    /**
     * Get the account associated with the contact.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the full name of the contact.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        $completeName = $this->first_name;

        if (! is_null($this->middle_name)) {
            $completeName = $completeName.' '.$this->middle_name;
        }

        if (! is_null($this->last_name)) {
            $completeName = $completeName.' '.$this->last_name;
        }

        return $completeName;
    }
}
