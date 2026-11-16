<?php

namespace Tests\Feature;

use Tests\TestCase;

class GuideTest extends TestCase
{
    public function test_guide_page_is_accessible(): void
    {
        $this->get('/guide')
            ->assertOk()
            ->assertSee('Guide utilisateur', false);
    }

    public function test_guide_pdf_downloads(): void
    {
        $this->get('/guide/download')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
