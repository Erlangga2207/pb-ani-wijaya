<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use Tests\TestCase;

// PRD-09 A1: folder publik bisa dipindah (public_html di Hostinger) lewat APP_PUBLIC_PATH → config('app.public_path').
class PublicPathTest extends TestCase
{
    public function test_configured_public_path_is_used(): void
    {
        $dir = storage_path('framework/testing/public_html');
        config(['app.public_path' => $dir]);

        (new AppServiceProvider($this->app))->register();

        $this->assertSame($dir.DIRECTORY_SEPARATOR.'x', public_path('x'));
    }

    public function test_default_public_path_without_config(): void
    {
        $this->assertNull(config('app.public_path'));
        $this->assertSame(base_path('public').DIRECTORY_SEPARATOR.'x', public_path('x'));
    }
}
