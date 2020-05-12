<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Template extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'name',
    ];

    /**
     * Get the account associated with the template.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the information values associated with the template.
     *
     * @return BelongsToMany
     */
    public function informations()
    {
        return $this->belongsToMany(Information::class);
    }
}
