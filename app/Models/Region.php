<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_uz',
        'name_oz',
        'name_ru',
    ];
    public function district(): HasMany
    {
        return $this->hasMany(District::class);
    }

}
