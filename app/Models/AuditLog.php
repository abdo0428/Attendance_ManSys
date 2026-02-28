<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\CompanyScoped;

class AuditLog extends Model
{
    use CompanyScoped;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'meta',
        'company_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function record(string $action, $model = null, array $meta = []): void
    {
        $userId = auth()->id();
        $companyId = $model?->company_id ?? auth()->user()?->company_id;

        static::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'meta' => $meta,
            'company_id' => $companyId,
        ]);
    }
}
