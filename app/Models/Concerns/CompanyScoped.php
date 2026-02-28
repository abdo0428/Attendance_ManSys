<?php

namespace App\Models\Concerns;

use App\Models\Scopes\CompanyScope;

trait CompanyScoped
{
    protected static function bootCompanyScoped(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (!empty($model->company_id)) {
                return;
            }

            $user = auth()->user();
            if ($user && $user->company_id) {
                $model->company_id = $user->company_id;
            }
        });
    }
}
