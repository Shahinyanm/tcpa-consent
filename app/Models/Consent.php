<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int      $id
 * @property int      $company_id
 * @property string   $first_name
 * @property string   $last_name
 * @property string   $phone_number
 * @property string   $language
 * @property string   $verification_code
 * @property string   $status
 * @property Company  $company
 * @property SmsLog[] $smsLogs
 */
class Consent extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'phone_number',
        'language',
        'verification_code',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class);
    }
}
