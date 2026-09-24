<?php

namespace App\Filament\Admin\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

// Form tetap dari baris site_settings. Key tidak bisa ditambah/dihapus dari UI,
// hanya lewat SiteSettingSeeder, supaya halaman publik tidak rusak diam-diam.
class WebsiteSettings extends Page
{
    protected string $view = 'filament.admin.pages.website-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Pengaturan Website';

    protected static ?string $title = 'Pengaturan Website';

    protected static ?int $navigationSort = 1;

    private const GROUPS = [
        'hero' => 'Hero',
        'general' => 'Umum',
        'about' => 'Tentang',
        'contact' => 'Kontak',
        'social' => 'Sosial Media',
    ];

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->isOwnerOrAbove();
    }

    public function mount(): void
    {
        $this->form->fill(SiteSetting::pluck('value', 'key')->all());
    }

    public function form(Schema $schema): Schema
    {
        $settings = SiteSetting::orderBy('order')->get()->groupBy('group');

        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('groups')->tabs(
                    collect(self::GROUPS)
                        ->filter(fn ($label, $group) => $settings->has($group))
                        ->map(fn ($label, $group) => Tab::make($label)->schema(
                            $settings[$group]->map(fn (SiteSetting $s) => $this->field($s))->all()
                        ))
                        ->values()
                        ->all()
                ),
            ]);
    }

    private function field(SiteSetting $setting)
    {
        $field = match ($setting->type) {
            'textarea' => Textarea::make($setting->key)->rows(4),
            'image' => FileUpload::make($setting->key)->image()->disk('public')->directory('settings')->maxSize(5120),
            'url' => TextInput::make($setting->key)->url(),
            'email' => TextInput::make($setting->key)->email(),
            'phone' => TextInput::make($setting->key)->tel(),
            default => TextInput::make($setting->key),
        };

        return $field->label($setting->label)->helperText($setting->description);
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            // save() per baris supaya event "saved" mereset cache SiteSetting::get().
            $setting = SiteSetting::where('key', $key)->first();
            if ($setting && $setting->value !== $value) {
                $setting->value = $value;
                $setting->save();
            }
        }

        Notification::make()->success()->title('Pengaturan website tersimpan')->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('viewWebsite')
                ->label('Lihat Website')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(url('/'))
                ->openUrlInNewTab(),
        ];
    }
}
