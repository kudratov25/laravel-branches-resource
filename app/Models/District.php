<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;
    protected $fillable = [
        'region_id',
        'name_uz',
        'name_oz',
        'name_ru',
    ];
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
