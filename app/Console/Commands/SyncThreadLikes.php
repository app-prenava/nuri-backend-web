<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class SyncThreadLikes extends Command
{
    protected $signature = 'threads:sync-likes';
    protected $description = 'Synchronize thread like data from Redis to Database';

    public function handle()
    {
        $redis = Redis::connection('likes');
        $keys = $redis->keys('thread:likes:*');

        if (empty($keys)) {
            Log::info('SyncThreadLikes: No like data found in Redis.');
            $this->info('No like data found in Redis.');
            return SymfonyCommand::SUCCESS;
        }

        foreach ($keys as $key) {
            $realKey = str_replace('laravel_cache:', '', $key);
            $threadId = (int) str_replace('thread:likes:', '', $realKey);
            $redisLikes = (int) $redis->get($realKey);

            $dbLikes = (int) DB::table('threads')
                ->where('thread_id', $threadId)
                ->value('likes_count');

            if ($redisLikes !== $dbLikes) {
                DB::table('threads')
                    ->where('thread_id', $threadId)
                    ->update(['likes_count' => $redisLikes]);

                Log::info("SyncThreadLikes: Thread ID {$threadId} synchronized ({$dbLikes} → {$redisLikes}).");
                $this->info("Thread ID {$threadId} synchronized ({$dbLikes} → {$redisLikes}).");
            }
        }

        Log::info('SyncThreadLikes: Synchronization completed at ' . now());
        $this->info('Synchronization completed.');

        return SymfonyCommand::SUCCESS;
    }
}
