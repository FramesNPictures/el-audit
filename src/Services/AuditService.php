<?php

namespace Fnp\Audit\Services;

use Fnp\Audit\Listeners\AuditEventListener;
use Fnp\Audit\Registry\AuditEventRegistry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

class AuditService
{
    protected static $registered = FALSE;
    protected static $sessionId  = NULL;

    /**
     * @return string|null
     */
    public static function session()
    {
        if (self::$sessionId)
            return self::$sessionId;

        self::$sessionId =
            Session::getId();

        return self::$sessionId;
    }

    public static function requestIpAddress(Request $request = NULL)
    {
        return $request
            ? $request->header('x-forwarded-for') ?: 'x:' . gethostname()
            : 't:' . gethostname();
    }

    public static function requestDetails(Request $request = NULL)
    {
        if (!$request)
            return [];

        return [
            'ua'  => $request->header('User-Agent'),
            'sh'  => gethostname(),
            'url' => $request->getRequestUri(),
            'ip'  => $request->getClientIp(),
            'h'   => $request->getHttpHost(),
        ];
    }

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