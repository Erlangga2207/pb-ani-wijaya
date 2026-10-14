<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// PRD-09 (deploy Git Hostinger): repo di-clone ke public_html, jadi root .htaccess yang melindungi kode.
// Aturan RewriteRule dievaluasi berurutan seperti Apache (aturan pertama yang cocok menentukan hasil).
class RootHtaccessTest extends TestCase
{
    public static function paths(): array
    {
        return [
            // path (tanpa "/" awal) => hasil yang diharapkan
            '.git' => ['.git/config', 'deny'],
            '.git/HEAD' => ['.git/HEAD', 'deny'],
            '.env' => ['.env', 'deny'],
            '.env.production.example' => ['.env.production.example', 'deny'],
            '.gitignore' => ['.gitignore', 'deny'],
            'storage/logs' => ['storage/logs/laravel.log', 'deny'],
            'storage/framework' => ['storage/framework/sessions/abc', 'deny'],
            'storage/app' => ['storage/app/private/x.pdf', 'deny'],
            'vendor' => ['vendor/autoload.php', 'deny'],
            'app' => ['app/Models/User.php', 'deny'],
            'bootstrap' => ['bootstrap/app.php', 'deny'],
            'config' => ['config/database.php', 'deny'],
            'database' => ['database/database.sqlite', 'deny'],
            'deploy' => ['deploy/deploy.sh', 'deny'],
            'docs' => ['docs/prd/PRD-09-deploy-hostinger.md', 'deny'],
            'composer.json' => ['composer.json', 'deny'],
            'composer.lock' => ['composer.lock', 'deny'],
            'package-lock.json' => ['package-lock.json', 'deny'],
            'artisan' => ['artisan', 'deny'],
            'phpunit.xml' => ['phpunit.xml', 'deny'],
            'vite.config.js' => ['vite.config.js', 'deny'],
            'README.md' => ['README.md', 'deny'],
            'CLAUDE.md' => ['CLAUDE.md', 'deny'],
            // Diteruskan ke public/
            'foto galeri' => ['storage/gallery/foto-abc123.jpg', 'public'],
            'css admin' => ['css/admin.css', 'public'],
            'beranda' => ['', 'public'],
            'halaman bahasa' => ['en/produk', 'public'],
            'sitemap' => ['sitemap.xml', 'public'],
            'robots' => ['robots.txt', 'public'],
            'panel' => ['panel/login', 'public'],
            // Verifikasi SSL hPanel dilayani langsung dari root
            '.well-known' => ['.well-known/acme-challenge/token', 'pass'],
        ];
    }

    #[DataProvider('paths')]
    public function test_root_htaccess_rules(string $path, string $expected): void
    {
        $this->assertSame($expected, $this->evaluate($path), "Path \"/{$path}\"");
    }

    public function test_no_https_or_www_redirect_and_second_layer_exists(): void
    {
        $htaccess = file_get_contents(base_path('.htaccess'));

        // HTTPS dipaksa dari hPanel (CDN Hostinger) — redirect di sini bisa loop.
        $this->assertDoesNotMatchRegularExpression('/^\s*RewriteCond\s+%\{(HTTPS|HTTP_HOST)\}/mi', $htaccess);
        $this->assertDoesNotMatchRegularExpression('/^\s*RewriteRule\s.*\bR=30[12]\b/mi', $htaccess);
        $this->assertStringContainsString('Options -Indexes', $htaccess);

        foreach (['storage/.htaccess', 'bootstrap/.htaccess'] as $file) {
            $this->assertMatchesRegularExpression('/^Require all denied$/m', file_get_contents(base_path($file)), $file);
        }
    }

    /** Hasil: 'deny' (403), 'pass' (dilayani apa adanya), 'public' (diteruskan ke public/), 'none'. */
    private function evaluate(string $path): string
    {
        preg_match_all('/^\s*RewriteRule\s+(\S+)\s+(\S+)(?:\s+\[([^\]]*)\])?/m', file_get_contents(base_path('.htaccess')), $rules, PREG_SET_ORDER);
        $this->assertNotEmpty($rules);

        foreach ($rules as $rule) {
            [, $pattern, $target] = $rule;
            $flags = array_map('trim', explode(',', $rule[3] ?? ''));
            $regex = '#'.$pattern.'#'.(in_array('NC', $flags, true) ? 'i' : '');

            if (! preg_match($regex, $path)) {
                continue;
            }

            if (in_array('F', $flags, true)) {
                return 'deny';
            }

            return str_starts_with($target, 'public/') ? 'public' : 'pass';
        }

        return 'none';
    }
}
