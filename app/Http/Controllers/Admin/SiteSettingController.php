<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\WebsiteContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

// Website › Pengaturan Website. Owner ke atas (SiteSettingPolicy).
class SiteSettingController extends Controller
{
    public const GROUPS = [
        'hero' => 'Hero (beranda)',
        'general' => 'Umum',
        'about' => 'Tentang Kami',
        'seo' => 'SEO',
        'contact' => 'Kontak',
        'social' => 'Sosial media',
    ];

    public function edit(): View
    {
        Gate::authorize('viewAny', SiteSetting::class);

        $settings = SiteSetting::orderBy('order')->get();

        return view('admin.site-settings.edit', [
            'groups' => collect(self::GROUPS)->map(fn ($label, $group) => [
                'label' => $label,
                'settings' => $settings->where('group', $group)->values(),
            ])->filter(fn ($group) => $group['settings']->isNotEmpty()),
            'translatable' => $settings->where('is_translatable', true)->values(),
        ]);
    }

    public function update(SiteSettingRequest $request, WebsiteContentService $content): RedirectResponse
    {
        Gate::authorize('viewAny', SiteSetting::class);

        $content->updateSettings($request->settingValues());

        return redirect()->route('admin.site-settings.edit')->with('sukses', 'Pengaturan website tersimpan.');
    }
}
