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
            ['key' => 'app_name', 'value' => 'Communeo', 'type' => 'string', 'group' => 'general', 'description' => 'Nom de l\'application', 'tenant_id' => null],
            
            // Paramètres garderie
            ['key' => 'garderie_morning_start', 'value' => '07:00', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de début de la garderie du matin', 'tenant_id' => null],
            ['key' => 'garderie_morning_end', 'value' => '08:30', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de fin de la garderie du matin', 'tenant_id' => null],
            ['key' => 'garderie_evening_start', 'value' => '16:30', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de début de la garderie du soir', 'tenant_id' => null],
            ['key' => 'garderie_evening_end', 'value' => '18:30', 'type' => 'string', 'group' => 'garderie', 'description' => 'Heure de fin de la garderie du soir', 'tenant_id' => null],
            
            ['key' => 'smtp_host', 'value' => 'mur.o2switch.net', 'type' => 'string', 'group' => 'smtp', 'description' => 'Serveur SMTP', 'tenant_id' => null],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'group' => 'smtp', 'description' => 'Port SMTP', 'tenant_id' => null],
            ['key' => 'smtp_username', 'value' => 'smtp@dailydesk.fr', 'type' => 'string', 'group' => 'smtp', 'description' => 'Nom d\'utilisateur SMTP', 'tenant_id' => null],
            ['key' => 'smtp_password', 'value' => 'Occupy-Shoplift9-Exposable', 'type' => 'string', 'group' => 'smtp', 'description' => 'Mot de passe SMTP', 'tenant_id' => null],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'smtp', 'description' => 'Type de chiffrement', 'tenant_id' => null],
            ['key' => 'smtp_from_address', 'value' => 'contact@dailydesk.fr', 'type' => 'string', 'group' => 'smtp', 'description' => 'Adresse email d\'expédition', 'tenant_id' => null],
            ['key' => 'smtp_from_name', 'value' => 'DailyDesk', 'type' => 'string', 'group' => 'smtp', 'description' => 'Nom d\'expédition', 'tenant_id' => null],
            
            ['key' => 'notify_arrival', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier les parents lors d\'une arrivée', 'tenant_id' => null],
            ['key' => 'notify_departure', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier les parents lors d\'un départ', 'tenant_id' => null],
            ['key' => 'notify_absence', 'value' => '0', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier en cas d\'absence', 'tenant_id' => null],
            ['key' => 'notify_event', 'value' => '1', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notifier en cas d\'événement', 'tenant_id' => null],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key'], 'tenant_id' => null],
                $setting
            );
        }
    }
}
