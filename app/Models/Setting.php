<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Concerns\CompanyScoped;
use App\Models\Scopes\CompanyScope;

class Setting extends Model
{
    use CompanyScoped;

    protected $fillable = ['key', 'value', 'company_id'];

    public $timestamps = true;

    public static function getValue(string $key, $default = null, ?int $companyId = null): ?string
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        $companyKey = $companyId ?? 'global';

        return Cache::remember("settings.{$companyKey}.{$key}", 3600, function () use ($key, $default, $companyId) {
            $query = static::withoutGlobalScope(CompanyScope::class)->where('key', $key);
            if ($companyId) {
                $query->where('company_id', $companyId);
            } else {
                $query->whereNull('company_id');
            }

            $value = $query->value('value');

            if ($value === null && $companyId) {
                $value = static::withoutGlobalScope(CompanyScope::class)
                    ->whereNull('company_id')
                    ->where('key', $key)
                    ->value('value');
            }

            return $value ?? $default;
        });
    }

    public static function setValue(string $key, $value, ?int $companyId = null): void
    {
        $companyId = $companyId ?? auth()->user()?->company_id;
        $companyKey = $companyId ?? 'global';

        static::withoutGlobalScope(CompanyScope::class)->updateOrCreate(
            ['key' => $key, 'company_id' => $companyId],
            ['value' => $value]
        );

        Cache::forget("settings.{$companyKey}.{$key}");
    }
}
