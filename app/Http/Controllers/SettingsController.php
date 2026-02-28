<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            'company_name' => Setting::getValue('company_name', 'Attendance Lite'),
            'default_work_start' => Setting::getValue('default_work_start', '09:00'),
            'default_work_end' => Setting::getValue('default_work_end', '17:00'),
            'grace_minutes' => Setting::getValue('grace_minutes', '10'),
            'default_locale' => Setting::getValue('default_locale', config('app.locale')),
        ];

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'default_work_start' => ['required', 'date_format:H:i'],
            'default_work_end' => ['required', 'date_format:H:i'],
            'grace_minutes' => ['required', 'integer', 'min:0', 'max:180'],
            'default_locale' => ['required', 'in:en,ar,tr'],
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue($key, (string) $value);
        }

        $company = $request->user()?->company;
        if ($company && $company->name !== $validated['company_name']) {
            $company->name = $validated['company_name'];
            $company->save();
        }

        session(['locale' => $validated['default_locale']]);

        return redirect()->route('settings.edit')->with('toast', ['type' => 'success', 'message' => __('app.settings_saved')]);
    }
}
