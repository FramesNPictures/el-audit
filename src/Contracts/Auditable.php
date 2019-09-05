<?php

namespace Fnp\Audit\Contracts;

interface Auditable
{
    /**
     * Return a payload to be attached to the
     * audit event.
     * @return array
     */
    public function auditPayload(): array;
}