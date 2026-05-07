<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sauna extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Manifests assigned to this sauna.
     */
    public function manifests(): HasMany
    {
        return $this->hasMany(Manifest::class);
    }

    /**
     * Use the slug for public URLs (e.g. /sauna/sauna-a).
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
