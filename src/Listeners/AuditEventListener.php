<?php

namespace Fnp\Audit\Listeners;

use Fnp\Audit\Contracts\Auditable;
use Fnp\Audit\Events\NewAuditEntry;
use Fnp\Audit\Exception\NotAuditableEvent;
use ReflectionException;

class AuditEventListener
{
    /**
     * Default handler for Subscribed Events
     *
     * @param               $eventName
     * @param  object|null  $event
     *
     * @throws ReflectionException
     */
    public function handle($eventName, $event = NULL)
    {
        if (is_array($event)) {
            $event = $event[0];
        }

        if (!is_object($event)) {
            return;
        }

        if (!$event instanceof Auditable) {
            throw NotAuditableEvent::make($eventName);
        }

        $eventHandle = $event->getAuditHandle();
        $payload     = $event->getAuditPayload();

        NewAuditEntry::dispatch($eventHandle, $payload);
    }
}