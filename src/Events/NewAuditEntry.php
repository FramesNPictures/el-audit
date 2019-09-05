<?php

namespace Fnp\Audit\Events;

use Carbon\Carbon;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

class NewAuditEntry
{
    use Dispatchable;

    public $handle;

    /**
     * @var Collection
     */
    public $payload;

    /**
     * @var Carbon
     */
    public $eventTime;

    public function __construct(string $handle, Collection $payload)
    {
        $this->eventTime = Carbon::now();
        $this->handle    = $handle;
        $this->payload   = $payload;
    }
}