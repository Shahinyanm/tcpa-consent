<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'consent_id',
        'message',
        'direction',
    ];

    public function consent(): BelongsTo
    {
        return $this->belongsTo(Consent::class);
    }
}
