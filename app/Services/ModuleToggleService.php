<?php

namespace App\Services;

class ModuleToggleService
{
    /**
     * Applique une bascule de module avec les règles de dépendance :
     * - activer un sous-module active aussi son module parent
     *   (ex: activer "Actualités" active "Site Public")
     * - désactiver un module désactive aussi ses sous-modules
     *   (ex: désactiver "Site Public" coupe "Actualités" et "Événements")
     *
     * @return array{modules: array, cascaded: array} nouveau tableau modules_enabled + modules touchés en cascade
     */
    public static function toggle(array $enabledModules, string $module, bool $enable): array
    {
        $availableModules = config('modules', []);

        if (!array_key_exists($module, $availableModules)) {
            return ['modules' => $enabledModules, 'cascaded' => []];
        }

        $cascaded = [];

        if ($enable) {
            if (!in_array($module, $enabledModules)) {
                $enabledModules[] = $module;
            }

            // Active aussi le module parent si nécessaire
            $parent = $availableModules[$module]['parent'] ?? null;

            if ($parent && array_key_exists($parent, $availableModules) && !in_array($parent, $enabledModules)) {
                $enabledModules[] = $parent;
                $cascaded[] = $parent;
            }
        } else {
            $enabledModules = array_values(array_diff($enabledModules, [$module]));

            // Désactive aussi les sous-modules qui dépendent de ce module
            $children = array_keys(array_filter(
                $availableModules,
                fn($config) => ($config['parent'] ?? null) === $module
            ));

            $dependentChildren = array_values(array_intersect($children, $enabledModules));

            if (!empty($dependentChildren)) {
                $enabledModules = array_values(array_diff($enabledModules, $dependentChildren));
                $cascaded = array_merge($cascaded, $dependentChildren);
            }
        }

        return ['modules' => $enabledModules, 'cascaded' => $cascaded];
    }
}
