<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MembershipExpired
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $memberId;
    public int $membershipId;
    public string $endDate;

    /**
     * Create a new event instance.
     */
    public function __construct(int $memberId, int $membershipId, string $endDate)
    {
        $this->memberId = $memberId;
        $this->membershipId = $membershipId;
        $this->endDate = $endDate;
    }
}
