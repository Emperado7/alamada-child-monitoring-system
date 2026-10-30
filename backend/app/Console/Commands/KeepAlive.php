<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KeepAlive extends Command
{
    protected $signature   = 'app:keep-alive';
    protected $description = 'Ping the /health endpoint to prevent Render free-tier cold starts.';

    public function handle(): int
    {
        $url = rtrim(config('app.url'), '/') . '/health';

        try {
            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $this->info("Keep-alive OK — {$url} responded {$response->status()}.");
                Log::info("KeepAlive: ping succeeded ({$response->status()})");
            } else {
                $this->warn("Keep-alive WARNING — {$url} responded {$response->status()}.");
                Log::warning("KeepAlive: unexpected status {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->error("Keep-alive FAILED — {$e->getMessage()}");
            Log::error("KeepAlive: ping failed — {$e->getMessage()}");
        }

        return self::SUCCESS;
    }
}
