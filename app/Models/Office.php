<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use HasFactory, SoftDeletes;

    const APPROVAL_APPROVED = 1;
    const APPROVAL_PENDING = 2;
    const APPROVAL_CANCELED = 3;
    const NOT_VISIBLE = true;
    const VISIBLE = false;

    protected $casts = [
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'approval_status' => 'integer',
        'hidden' => 'bool',
        'price_per_day' => 'integer',
        'monthly_discount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class,'resource');
    }

    public function scopeNearestTo(Builder $builder, $lat, $lng): Builder
    {
        return $builder->select()
            ->selectRaw(
            'SQRT(POW(69.1 * (lat - ?), 2) + POW(69.1 * (? - lng) * COS(lat / 57.3), 2)) AS distance',
            [$lat, $lng]
        )->orderBy('distance');
    }

    public function scopeApproved(Builder $builder): Builder
    {
        return $builder->where('approval_status','=',self::APPROVAL_APPROVED);
    }

    public function scopePending(Builder $builder): Builder
    {
        return $builder->where('approval_status','=',self::APPROVAL_PENDING);
    }

    public function scopeCanceled(Builder $builder): Builder
    {
        return $builder->where('approval_status','=',self::APPROVAL_CANCELED);
    }

    public function scopeVisible(Builder $builder): Builder
    {
        return $builder->where('hidden','=',self::VISIBLE);
    }

    public function scopeNotVisible(Builder $builder): Builder
    {
        return $builder->where('hidden','=',self::NOT_VISIBLE);
    }
}
