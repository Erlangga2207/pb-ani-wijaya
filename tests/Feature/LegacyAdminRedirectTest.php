<?php

namespace Tests\Feature;

use Tests\TestCase;

// PRD-08 Fase 7: /admin (Filament) sudah dihapus; semua URL lama diarahkan permanen ke /panel.
class LegacyAdminRedirectTest extends TestCase
{
    public function test_old_admin_urls_redirect_permanently_to_panel(): void
    {
        foreach (['/admin', '/admin/', '/admin/login', '/admin/items/1/edit', '/admin/reports?tab=pnl'] as $url) {
            $this->get($url)->assertStatus(301)->assertRedirect('/panel');
        }
    }
}
