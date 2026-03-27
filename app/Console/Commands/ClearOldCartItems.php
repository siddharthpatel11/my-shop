<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearOldCartItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:clear-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove cart items that are older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $cutoff = Carbon::now()->subHours(24);
        $cutoff = Carbon::now()->subMinutes(1);

        $count = CartItem::where('updated_at', '<', $cutoff)->delete();

        if ($count > 0) {
            $this->info("Removed $count old cart items.");
            Log::info("Cart auto-cleanup: Removed $count old cart items older than $cutoff.");
        } else {
            $this->info("No old cart items to remove.");
        }

        return Command::SUCCESS;
    }
}
