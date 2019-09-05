<?php

namespace Fnp\Audit\Module\Features;

use Fnp\Audit\Services\AuditService;

trait ModuleAudit
{
    /**
     * Return array of handles and event classes to be audited.
     * Audit handle as a key and event class as a value.
     * @return array
     */
    abstract public function auditEvents(): array;

    public function bootModuleAuditFeature()
    {
        foreach ($this->auditEvents() as $handle => $event)
            AuditService::audit($event, $handle);
    }
}