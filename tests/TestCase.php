<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crée l'application de test : la connexion centrale (MySQL en prod)
     * est remplacée par une base sqlite en mémoire. Toutes les connexions
     * explicites (`Schema::connection('central')`, stancl) pointent dessus.
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $app->config->set('database.default', 'central');
        $app->config->set('database.connections.central', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        return $app;
    }
}
