<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public $timestamps = true;

    public static function getValue(string $key, $default = null): ?string
    {
        return Cache::remember("settings.{$key}", 3600, function () use ($key, $default) {
            return static::query()->where('key', $key)->value('value') ?? $default;
        });
    }

    public static function setValue(string $key, $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("settings.{$key}");
    }
}

