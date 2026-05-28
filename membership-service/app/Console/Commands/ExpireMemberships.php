<?php

namespace App\Console\Commands;

use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpireMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'memberships:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for active memberships that have passed their end_date and marks them as expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();
        
        $expiredMemberships = Membership::where('status', 'active')
            ->where('end_date', '<', $today)
            ->get();

        $count = $expiredMemberships->count();
        $this->info("Found {$count} memberships to expire.");

        foreach ($expiredMemberships as $membership) {
            $membership->update(['status' => 'expired']);
            
            // Sync with CRM Service (best effort async)
            // Note: Since this runs in CLI, there is no auth token. 
            // We'll rely on an internal M2M token or skip auth for internal network requests if configured,
            // but for now, we'll log it. In a real system, you'd dispatch an event `MembershipExpired` to RabbitMQ.
            // Let's use RabbitMQ since we have it!
            
            try {
                event(new \App\Events\MembershipExpired(
                    $membership->member_id,
                    $membership->id,
                    $membership->end_date
                ));
                Log::info("Expired membership {$membership->id} for member {$membership->member_id} and dispatched event");
            } catch (\Exception $e) {
                Log::error("Failed to process expiration for membership {$membership->id}: " . $e->getMessage());
            }
        }

        $this->info('Expiration process completed.');
    }
}
