<?php

declare(strict_types=1);

namespace App\Presentation\Livewire\Admin;

use App\Domain\Audit\Services\AuditLogger;
use App\Domain\Settings\Models\Setting;
use App\Domain\Settings\SettingDefinitions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class SettingsForm extends Component
{
    use WithFileUploads;

    public string $siteName = '';

    public string $defaultLanguage = 'ar';

    public string $timezone = 'Asia/Baghdad';

    public string $currencyDisplay = 'IQD';

    public string $drawAnimationSpeed = 'normal';

    public bool $soundEnabled = false;

    public bool $maintenanceMode = false;

    public mixed $logoUpload = null;

    public mixed $faviconUpload = null;

    public ?string $currentLogoUrl = null;

    public ?string $currentFaviconUrl = null;

    public ?string $successMessage = null;

    public function mount(): void
    {
        Gate::authorize('settings.manage');

        $this->siteName = (string) Setting::get('site_name', 'Yanasib');
        $this->defaultLanguage = (string) Setting::get('default_language', 'ar');
        $this->timezone = (string) Setting::get('timezone', 'Asia/Baghdad');
        $this->currencyDisplay = (string) Setting::get('currency_display', 'IQD');
        $this->drawAnimationSpeed = (string) Setting::get('draw_animation_speed', 'normal');
        $this->soundEnabled = (bool) Setting::get('sound_enabled', false);
        $this->maintenanceMode = (bool) Setting::get('maintenance_mode', false);

        $logoPath = Setting::get('logo');
        $this->currentLogoUrl = $logoPath ? Storage::disk('public')->url($logoPath) : null;

        $faviconPath = Setting::get('favicon');
        $this->currentFaviconUrl = $faviconPath ? Storage::disk('public')->url($faviconPath) : null;
    }

    public function save(AuditLogger $auditLogger): void
    {
        Gate::authorize('settings.manage');

        $data = $this->validate([
            'siteName' => ['required', 'string', 'max:255'],
            'defaultLanguage' => ['required', 'in:ar,en'],
            'timezone' => ['required', 'timezone'],
            'currencyDisplay' => ['required', 'string', 'max:10'],
            'drawAnimationSpeed' => ['required', 'in:slow,normal,fast'],
            'soundEnabled' => ['boolean'],
            'maintenanceMode' => ['boolean'],
            'logoUpload' => ['nullable', 'image', 'max:1024'],
            'faviconUpload' => ['nullable', 'image', 'max:512'],
        ]);

        Setting::set('site_name', $data['siteName'], 'string', 'general');
        Setting::set('default_language', $data['defaultLanguage'], 'string', 'general');
        Setting::set('timezone', $data['timezone'], 'string', 'general');
        Setting::set('currency_display', $data['currencyDisplay'], 'string', 'general');
        Setting::set('draw_animation_speed', $data['drawAnimationSpeed'], 'string', 'draw');
        Setting::set('sound_enabled', $data['soundEnabled'], 'boolean', 'draw');
        Setting::set('maintenance_mode', $data['maintenanceMode'], 'boolean', 'general');

        if ($this->logoUpload) {
            $path = $this->logoUpload->store('settings', 'public');
            Setting::set('logo', $path, 'string', 'general');
            $this->currentLogoUrl = Storage::disk('public')->url($path);
            $this->logoUpload = null;
        }

        if ($this->faviconUpload) {
            $path = $this->faviconUpload->store('settings', 'public');
            Setting::set('favicon', $path, 'string', 'general');
            $this->currentFaviconUrl = Storage::disk('public')->url($path);
            $this->faviconUpload = null;
        }

        $auditLogger->log('Settings Updated', null, [], array_merge($data, [
            'logo_changed' => (bool) $this->logoUpload,
            'favicon_changed' => (bool) $this->faviconUpload,
        ]));

        $this->successMessage = __('Settings saved.');
    }

    public function render()
    {
        return view('livewire.admin.settings-form', [
            'definitions' => SettingDefinitions::all(),
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }
}
