<?php

namespace Spatie\DiscordAlerts;

class DiscordAlert
{
    protected string $webhookUrlName = 'default';

    protected ?string $username = null;

    protected ?string $avatarUrl = null;

    public function to(string $webhookUrlName): self
    {
        $this->webhookUrlName = $webhookUrlName;

        return $this;
    }

    public function from(?string $username = null, ?string $avatarUrl = null): self
    {
        $this->username = $username;
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function message(string $text, array $embeds = []): void
    {
        $webhookUrl = Config::getWebhookUrl($this->webhookUrlName);

        $text = $this->parseNewline($text);

        foreach ($embeds as $key => $embed) {
            if (array_key_exists('description', $embed)) {
                $embeds[$key]['description'] = $this->parseNewline($embeds[$key]['description']);
            }

            if (array_key_exists('color', $embed)) {
                $embeds[$key]['color'] = hexdec(str_replace('#', '', $embed['color'])) ;
            }
        }

        $jobArguments = [
            'text' => $text,
            'webhookUrl' => $webhookUrl,
            'embeds' => $embeds,
            'username' => $this->username,
            'avatar_url' => $this->avatarUrl,
        ];

        $job = Config::getJob($jobArguments);

        dispatch($job);
    }

    private function parseNewline(string $text): string
    {
        return str_replace('\n', PHP_EOL, $text);
    }
}
