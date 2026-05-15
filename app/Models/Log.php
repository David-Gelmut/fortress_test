<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
    protected $casts = [
        'date' => 'datetime',
    ];

    public function scopeFilterByPeriod(Builder $query, ?string $from, ?string $to): Builder
    {
        return $query
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to,   fn($q) => $q->whereDate('date', '<=', $to));
    }

    public function scopeFilterByOs(Builder $query, ?string $os): Builder
    {
        return $query->when($os, fn($q) => $q->where('os', $os));
    }

    public function scopeFilterByArch(Builder $query, ?string $arch): Builder
    {
        return $query->when($arch, fn($q) => $q->where('architecture', $arch));
    }

    public function scopeApplySorting(Builder $query, ?string $sortBy, ?string $sortDir): Builder
    {
        return $query->when($sortBy, function ($q) use ($sortBy, $sortDir) {
            return $q->orderBy($sortBy, $sortDir ?? 'desc');
        });
    }
}
