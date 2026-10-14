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
            // Asset statis diteruskan ke public/
            'gambar' => ['images/logo.png', 'public'],
            'foto galeri' => ['storage/gallery/foto-abc123.jpg', 'public'],
            'css admin' => ['css/admin.css', 'public'],
            // Halaman → index.php di root
            'beranda' => ['', 'app'],
            'halaman bahasa' => ['en/produk', 'app'],
            'sitemap' => ['sitemap.xml', 'app'],
            'panel' => ['panel/login', 'app'],
            // Dilayani langsung dari root
            'robots' => ['robots.txt', 'pass'],
            'favicon' => ['favicon.ico', 'pass'],
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
        $this->assertDoesNotMatchRegularExpression('/^\s*RewriteRule\s+\S+\s+https?:/mi', $htaccess);
        $this->assertStringContainsString('Options -Indexes', $htaccess);

        foreach (['storage/.htaccess', 'bootstrap/.htaccess'] as $file) {
            $this->assertMatchesRegularExpression('/^Require all denied$/m', file_get_contents(base_path($file)), $file);
        }
    }

    /** Hasil: 'deny' (403), 'pass' (dilayani apa adanya), 'public' (diteruskan ke public/), 'app' (index.php), 'none'.
     *  Aturan header (E=) dan redirect trailing slash (R=) bergantung RewriteCond, jadi dilewati. */
    private function evaluate(string $path): string
    {
        preg_match_all('/^\s*RewriteRule\s+(\S+)\s+(\S+)(?:\s+\[([^\]]*)\])?/m', file_get_contents(base_path('.htaccess')), $rules, PREG_SET_ORDER);
        $this->assertNotEmpty($rules);

        foreach ($rules as $rule) {
            [, $pattern, $target] = $rule;
            $flags = array_map('trim', explode(',', $rule[3] ?? ''));
            if (preg_grep('/^[ER]=/', $flags)) {
                continue;
            }
            $regex = '#'.$pattern.'#'.(in_array('NC', $flags, true) ? 'i' : '');

            if (! preg_match($regex, $path)) {
                continue;
            }

            if (in_array('F', $flags, true)) {
                return 'deny';
            }

            return match (true) {
                str_starts_with($target, 'public/') => 'public',
                $target === 'index.php' => 'app',
                default => 'pass',
            };
        }

        return 'none';
    }
}
