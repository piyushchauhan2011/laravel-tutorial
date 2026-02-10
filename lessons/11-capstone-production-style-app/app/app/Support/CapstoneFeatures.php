<?php

namespace App\Support;

final class CapstoneFeatures
{
    public const ANALYTICS_V2_DASHBOARDS = 'analytics_v2_dashboards';
    public const AI_TRIAGE_ASSISTANT = 'ai_triage_assistant';
    public const CANARY_RELEASE_BANNER = 'canary_release_banner';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::ANALYTICS_V2_DASHBOARDS,
            self::AI_TRIAGE_ASSISTANT,
            self::CANARY_RELEASE_BANNER,
        ];
    }

    public static function isValid(string $feature): bool
    {
        return in_array($feature, self::all(), true);
    }

    public static function defaultValue(string $feature): bool
    {
        return match ($feature) {
            self::CANARY_RELEASE_BANNER => true,
            default => false,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::ANALYTICS_V2_DASHBOARDS => 'Analytics v2 Dashboards',
            self::AI_TRIAGE_ASSISTANT => 'AI Triage Assistant',
            self::CANARY_RELEASE_BANNER => 'Canary Release Banner',
        ];
    }
}
