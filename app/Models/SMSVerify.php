<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SMSVerify extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'phone',
        'verification_code',
        'attempts'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
