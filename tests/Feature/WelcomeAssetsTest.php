<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeAssetsTest extends TestCase
{
    public function test_welcome_page_generates_https_asset_urls_behind_render_proxy(): void
    {
        $response = $this
            ->withServerVariables([
                'HTTP_X_FORWARDED_HOST' => 'krosty-sado.onrender.com',
                'HTTP_X_FORWARDED_PROTO' => 'https',
            ])
            ->get('/');

        $response
            ->assertOk()
            ->assertSee('https://krosty-sado.onrender.com/build/assets/app-', false)
            ->assertSee('https://krosty-sado.onrender.com/build/assets/welcome-', false);
    }
}
