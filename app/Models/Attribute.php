<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'name',
        'type',
        'unit',
        'unit_placement_after',
        'has_default_value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_default_value' => 'boolean',
        'unit_placement_after' => 'boolean',
    ];

    /**
     * Get the account associated with the attribute.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the attribute default values associated with the attribute.
     *
     * @return HasMany
     */
    public function defaultValues()
    {
        return $this->hasMany(AttributeDefaultValue::class);
    }

    /**
     * Get the templates associated with the attribute.
     *
     * @return belongsToMany
     */
    public function templates()
    {
        return $this->belongsToMany(Template::class);
    }
}
