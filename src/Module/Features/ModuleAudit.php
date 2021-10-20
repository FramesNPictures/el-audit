<?php

namespace Fnp\Audit\Module\Features;

use Fnp\Audit\Listeners\AuditEventListener;
use Illuminate\Events\Dispatcher;

trait ModuleAudit
{
    /**
     * Return array of Event classes to be audited.
     *
     * @return array
     */
    abstract public function defineEventsToBeAudited(): array;

    public function bootModuleAuditFeature(Dispatcher $events)
    {
        foreach ($this->defineEventsToBeAudited() as $event) {
            $events->listen($event, AuditEventListener::class);
        }
    }
}