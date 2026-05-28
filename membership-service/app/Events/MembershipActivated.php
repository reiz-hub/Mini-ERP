<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MembershipActivated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $memberId;
    public int $planId;
    public string $startDate;
    public string $endDate;
    public float $amountPaid;

    /**
     * Create a new event instance.
     */
    public function __construct(int $memberId, int $planId, string $startDate, string $endDate, float $amountPaid)
    {
        $this->memberId = $memberId;
        $this->planId = $planId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->amountPaid = $amountPaid;
    }
}
