<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncThreadViewsToWallets extends Command
{
    protected $signature = 'wallets:sync-views';
    protected $description = 'Sync thread views to wallets based on PRICE_PER_VIEW parameter.';

    public function handle()
    {
        try {
            $price = DB::table('parameterized')
                ->where('key', 'PRICE_PER_VIEW')
                ->value('value');

            if (! $price) {
                Log::error('SyncThreadViewsToWallets: PRICE_PER_VIEW not found in parameterized table.');
                $this->error('PRICE_PER_VIEW parameter not found.');
                return SymfonyCommand::FAILURE;
            }

            $price = (float) $price;

            $threads = DB::table('threads')
                ->select('user_id', DB::raw('SUM(views) as total_views'))
                ->groupBy('user_id')
                ->get();

            if ($threads->isEmpty()) {
                Log::info('SyncThreadViewsToWallets: No thread data found.');
                $this->info('No thread data found.');
                return SymfonyCommand::SUCCESS;
            }

            foreach ($threads as $thread) {
                $userId = $thread->user_id;
                $totalViews = (int) $thread->total_views;
                $walletAdAmount = $totalViews * $price;

                $wallet = DB::table('wallets')->where('user_id', $userId)->first();

                if (! $wallet) {
                    DB::table('wallets')->insert([
                        'user_id' => $userId,
                        'wallet_ad' => $walletAdAmount,
                        'wallet_dinkes' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info("SyncThreadViewsToWallets: Created wallet for user {$userId} ({$walletAdAmount}).");
                } else {
                    DB::table('wallets')
                        ->where('user_id', $userId)
                        ->update([
                            'wallet_ad' => $walletAdAmount,
                            'updated_at' => now(),
                        ]);

                    Log::info("SyncThreadViewsToWallets: Updated wallet_ad for user {$userId} ({$walletAdAmount}).");
                }
            }

            Log::info('SyncThreadViewsToWallets: Synchronization completed at ' . now());
            $this->info('Synchronization completed.');
            return SymfonyCommand::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('SyncThreadViewsToWallets: Exception occurred - ' . $e->getMessage());
            $this->error('An error occurred: ' . $e->getMessage());
            return SymfonyCommand::FAILURE;
        }
    }
}
