<?php

namespace App\Console\Commands;

use App\Domain\Cms\EnquiryManager;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('enquiries:escalate-overdue')]
#[Description('Notify department staff about open enquiries past their SLA due date')]
class EscalateOverdueEnquiries extends Command
{
    public function handle(EnquiryManager $manager): int
    {
        $count = $manager->escalateOverdue();
        $this->info("Escalated {$count} overdue enquir".($count === 1 ? 'y' : 'ies').'.');

        return self::SUCCESS;
    }
}
