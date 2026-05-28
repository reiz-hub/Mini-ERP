<?php

namespace App\Jobs;

use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleMembershipExpired implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $eventData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $memberId = $this->eventData['memberId'] ?? null;

        if (!$memberId) {
            Log::error("CRM: Missing memberId in HandleMembershipExpired event");
            return;
        }

        $member = Member::find($memberId);
        if ($member) {
            $member->update(['status' => 'inactive']);
            Log::info("CRM: Member {$memberId} marked inactive via expiration event");
        } else {
            Log::warning("CRM: Member {$memberId} not found for expiration event");
        }
    }
}
