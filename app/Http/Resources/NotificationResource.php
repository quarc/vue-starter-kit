<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin \Illuminate\Notifications\DatabaseNotification */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->data ?? [];
        $meta = data_get($data, 'meta', []);

        $title = data_get($data, 'title', '');
        $body = data_get($data, 'body', '');

        return [
            'id' => $this->id,
            'icon' => data_get($data, 'icon', 'Bell'),
            'title' => __($title),
            'body' => $this->translatedBody($body, $meta),
            'level' => data_get($data, 'level', 'info'),
            'readAt' => optional($this->read_at)->toISOString(),
            'archivedAt' => $this->archived_at ? \Illuminate\Support\Carbon::parse($this->archived_at)->toISOString() : null,
            'createdAt' => optional($this->created_at)->toISOString(),
            'meta' => $this->translatedMeta($meta),
        ];
    }

    protected function translatedBody(string $template, array $meta): string
    {
        $replacements = [];

        if ($template === 'We noticed a login from :ip using :agent') {
            $replacements['ip'] = data_get($meta, 'ip_address');
            $replacements['agent'] = $this->agentDescription($meta);
        }

        if (Str::contains($template, ':fields')) {
            $fields = array_map(fn ($field) => __($field), data_get($meta, 'fields', []));
            $replacements['fields'] = implode(', ', $fields);
        }

        if (Str::contains($template, '|')) {
            $count = (int) ($meta['count'] ?? 1);

            return trans_choice($template, $count, array_merge($replacements, ['count' => $count]));
        }

        return __($template, $replacements);
    }

    protected function translatedMeta(array $meta): array
    {
        if (! empty($meta['fields']) && is_array($meta['fields'])) {
            $meta['fields'] = array_map(fn ($field) => __($field), $meta['fields']);
        }

        return $meta;
    }

    protected function agentDescription(array $meta): string
    {
        $agent = $meta['agent'] ?? [];
        $browser = $agent['browser'] ?? null;
        $version = $agent['browser_version'] ?? null;

        if ($browser && $version) {
            return __(':browser :version', [
                'browser' => $browser,
                'version' => $version,
            ]);
        }

        if ($browser) {
            return __($browser);
        }

        return __('an unknown device');
    }
}
