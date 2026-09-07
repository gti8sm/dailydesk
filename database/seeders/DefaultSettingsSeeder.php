<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Paramètres généraux
            ['key' => 'app_name', 'value' => 'Communeo', 'type' => 'string', 'group' => 'general', 'description' => 'Nom de l\'application'],
            
            // Paramètres garderie
            ['key' => 'garderie_morning_start', 'value' => '07:00', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de début de la garderie du matin'],
            ['key' => 'garderie_morning_end', 'value' => '08:30', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de fin de la garderie du matin'],
            ['key' => 'garderie_evening_start', 'value' => '16:30', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de début de la garderie du soir'],
            ['key' => 'garderie_evening_end', 'value' => '18:30', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de fin de la garderie du soir'],
            
            ['key' => 'smtp_host', 'value' => '', 'type' => 'string', 'group' => 'smtp', 'description' => 'Serveur SMTP'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'group' => 'smtp', 'description' => 'Port SMTP'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string', 'group' => 'smtp', 'description' => 'Nom d\'utilisateur SMTP'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string', 'group' => 'smtp', 'description' => 'Mot de passe SMTP'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'smtp', 'description' => 'Type de chiffrement'],
            ['key' => 'smtp_from_address', 'value' => '', 'type' => 'string', 'group' => 'smtp', 'description' => 'Adresse email d\'expédition'],
            ['key' => 'smtp_from_name', 'value' => 'Communeo', 'type' => 'string', 'group' => 'smtp', 'description' => 'Nom d\'expédition'],
            
            ['key' => 'notify_arrival', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier les parents lors d\'une arrivée'],
            ['key' => 'notify_departure', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier les parents lors d\'un départ'],
            ['key' => 'notify_absence', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier en cas d\'absence'],
            ['key' => 'notify_event', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier en cas d\'événement'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
