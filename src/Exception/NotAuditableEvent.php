<?php

namespace Fnp\Audit\Exception;

use Fnp\Audit\Contracts\Auditable;
use RuntimeException;

class NotAuditableEvent extends RuntimeException
{
    public static function make(string $eventClassName)
    {
        return new static(
            sprintf(
                'Event %s should implement %s to be audited.',
                $eventClassName,
                Auditable::class
            )
        );
    }
}