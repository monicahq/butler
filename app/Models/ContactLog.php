<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_id',
        'author_id',
        'author_name',
        'action',
        'objects',
    ];

    /**
     * Get the contact record associated with the contact log.
     *
     * @return BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the User record associated with the contact log.
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
