<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_id',
        'district_id'
    ];
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
    public function images()
    {
        return $this->morphMany(Attachment::class, 'attachment');
    }
}
