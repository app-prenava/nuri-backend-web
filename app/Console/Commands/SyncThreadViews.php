<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class SyncThreadViews extends Command
{
    protected $signature = 'threads:sync-views';
    protected $description = 'Synchronize thread view data from Redis to Database';

    public function handle()
    {
        $redis = Redis::connection();
        $redis->select(1);

        $keys = $redis->keys('thread:views:*');
        if (empty($keys)) {
            Log::info('SyncThreadViews: No view data found in Redis.');
            $this->info('No view data found in Redis.');
            return SymfonyCommand::SUCCESS;
        }

        foreach ($keys as $key) {
            $threadId = (int) str_replace('thread:views:', '', $key);
            $redisViews = (int) $redis->get($key);
            $dbViews = (int) DB::table('threads')->where('thread_id', $threadId)->value('views');
            $delta = $redisViews - $dbViews;

            if ($delta > 0) {
                DB::table('threads')->where('thread_id', $threadId)->update(['views' => $redisViews]);
                Log::info("SyncThreadViews: Thread ID {$threadId} synchronized ({$dbViews} → {$redisViews}).");
                $this->info("Thread ID {$threadId} synchronized ({$dbViews} → {$redisViews}).");
            }
        }

        Log::info('SyncThreadViews: Synchronization completed at ' . now());
        $this->info('Synchronization completed.');
        return SymfonyCommand::SUCCESS;
    }
}
