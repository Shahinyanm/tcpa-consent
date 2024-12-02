<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int       $id
 * @property string    $name
 * @property string    $hash
 * @property Consent[] $consents
 */
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'hash',
        'phone_number',
    ];

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }
}
