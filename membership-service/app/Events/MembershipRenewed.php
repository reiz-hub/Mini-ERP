<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MembershipRenewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $memberId;
    public int $membershipId;
    public int $planId;
    public string $newEndDate;
    public float $amountPaid;

    /**
     * Create a new event instance.
     */
    public function __construct(int $memberId, int $membershipId, int $planId, string $newEndDate, float $amountPaid)
    {
        $this->memberId = $memberId;
        $this->membershipId = $membershipId;
        $this->planId = $planId;
        $this->newEndDate = $newEndDate;
        $this->amountPaid = $amountPaid;
    }
}
