<?php

namespace Fnp\Audit\Services;

use Fnp\Audit\Listeners\AuditEventListener;
use Fnp\Audit\Registry\AuditEventRegistry;
use Illuminate\Support\Facades\Event;

class AuditService
{
    protected static $registered = FALSE;
    protected static $sessionId  = NULL;

    /**
     * @param string $defaultListener
     */
    public static function register($defaultListener = AuditEventListener::class)
    {
        Event::listen('*', $defaultListener);
        self::$registered = TRUE;
    }

    /**
     * @param $className
     * @param $handle
     */
    public static function audit($className, $handle)
    {
        if (!self::$registered)
            self::register();

        AuditEventRegistry::extend($handle, $className);
    }
}