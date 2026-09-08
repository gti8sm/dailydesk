<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'general' => Setting::getGroup('general'),
            'garderie' => Setting::getGroup('garderie'),
            'cantine' => Setting::getGroup('cantine'),
            'smtp' => Setting::getGroup('smtp'),
            'notifications' => Setting::getGroup('notifications'),
        ];
        
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:50',
            'garderie_morning_start' => 'nullable|date_format:H:i',
            'garderie_morning_end' => 'nullable|date_format:H:i',
            'garderie_evening_start' => 'nullable|date_format:H:i',
            'garderie_evening_end' => 'nullable|date_format:H:i',
            'smtp_host' => 'nullable|string',
            'smtp_port' => 'nullable|integer',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'smtp_from_address' => 'nullable|email',
            'smtp_from_name' => 'nullable|string',
            'cantine_enable_snack' => 'nullable|boolean',
            'notify_arrival' => 'nullable|boolean',
            'notify_departure' => 'nullable|boolean',
            'notify_absence' => 'nullable|boolean',
            'notify_event' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                $group = $this->getGroupFromKey($key);
                $type = $this->getTypeFromKey($key);
                Setting::set($key, $value, $type, $group);
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès !');
    }

    protected function getGroupFromKey($key)
    {
        if (str_starts_with($key, 'app_')) return 'general';
        if (str_starts_with($key, 'garderie_')) return 'garderie';
        if (str_starts_with($key, 'cantine_')) return 'cantine';
        if (str_starts_with($key, 'smtp_')) return 'smtp';
        if (str_starts_with($key, 'notify_')) return 'notifications';
        return 'general';
    }

    protected function getTypeFromKey($key)
    {
        if (str_starts_with($key, 'notify_')) return 'boolean';
        if (str_starts_with($key, 'cantine_enable')) return 'boolean';
        if (in_array($key, ['smtp_port'])) return 'integer';
        return 'string';
    }
}
