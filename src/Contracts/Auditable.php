<?php

namespace Fnp\Audit\Contracts;

interface Auditable
{
    /**
     * Returns the audit event name.
     *
     * @return string
     */
    public function getAuditHandle(): string;

    /**
     * Returns a payload to be attached to the audit event.
     *
     * @return array
     */
    public function getAuditPayload(): array;
}