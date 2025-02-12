<?php

namespace Spatie\DiscordAlerts\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendToDiscordChannelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public int $maxExceptions = 3;

    public function __construct(
        public string $text,
        public string $webhookUrl,
        public array|null $embeds = null,
        public string|null $username = null,
        public string|null $avatar_url = null

    ) {
    }

    public function handle(): void
    {
        $payload = [
            'content' => $this->text,
        ];

        if (! blank($this->embeds)) {
            $payload['embeds'] = $this->embeds;
        }

        if (!is_null($this->username)) {
            $payload['username'] = $this->username;
        }

        if (!is_null($this->avatar_url)) {
            $payload['avatar_url'] = $this->avatar_url;
        }

        Http::post($this->webhookUrl, $payload);
    }
}

