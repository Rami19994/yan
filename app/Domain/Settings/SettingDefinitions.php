<?php

declare(strict_types=1);

namespace App\Domain\Settings;

/**
 * Central registry of every configurable setting: its storage type (for
 * Setting::get()'s cast), group (for UI sectioning), and default — so the
 * admin Settings screen and Setting::get() callers agree on one source of
 * truth instead of magic strings scattered across the app.
 */
final class SettingDefinitions
{
    /**
     * @return array<string, array{type: string, group: string, default: mixed, label: string}>
     */
    public static function all(): array
    {
        return [
            'site_name' => ['type' => 'string', 'group' => 'general', 'default' => 'Yanasib', 'label' => 'Site Name'],
            'logo' => ['type' => 'string', 'group' => 'general', 'default' => null, 'label' => 'Logo'],
            'favicon' => ['type' => 'string', 'group' => 'general', 'default' => null, 'label' => 'Favicon'],
            'default_language' => ['type' => 'string', 'group' => 'general', 'default' => 'ar', 'label' => 'Default Language'],
            'timezone' => ['type' => 'string', 'group' => 'general', 'default' => 'Asia/Baghdad', 'label' => 'Timezone'],
            'currency_display' => ['type' => 'string', 'group' => 'general', 'default' => 'IQD', 'label' => 'Currency Display'],
            'theme' => ['type' => 'string', 'group' => 'general', 'default' => 'dark', 'label' => 'Theme'],
            'draw_animation_speed' => ['type' => 'string', 'group' => 'draw', 'default' => 'normal', 'label' => 'Draw Animation Speed'],
            'sound_enabled' => ['type' => 'boolean', 'group' => 'draw', 'default' => false, 'label' => 'Sound Enabled by Default'],
            'maintenance_mode' => ['type' => 'boolean', 'group' => 'general', 'default' => false, 'label' => 'Maintenance Mode'],
        ];
    }

    public static function definition(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }
}
