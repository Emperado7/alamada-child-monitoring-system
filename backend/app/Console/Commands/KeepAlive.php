<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class KeepAlive extends Command
{
    protected $signature = 'app:keep-alive';
    protected $description = 'Ping the /api/health endpoint to prevent Render free-tier cold starts.';

    public function handle(): int
    {
        $url = rtrim(config('app.url'), '/') . '/api/health';

        try {
            $response = Http::timeout(10)->get($url);
            $this->info("[keep-alive] Pinged {$url} → HTTP {$response->status()}");
        } catch (\Throwable $e) {
            // Log but do not fail — a network hiccup shouldn't abort the scheduler.
            $this->warn("[keep-alive] Ping failed: {$e->getMessage()}");
        }

        return Command::SUCCESS;
    }
}
