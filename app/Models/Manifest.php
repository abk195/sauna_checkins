<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Manifest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'manifest_id',
        'name',
        'sauna_id',
    ];

    /**
     * The sauna this manifest belongs to (nullable until assigned).
     */
    public function sauna(): BelongsTo
    {
        return $this->belongsTo(Sauna::class);
    }
}
