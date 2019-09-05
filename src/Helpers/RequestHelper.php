<?php

namespace Fnp\Audit\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RequestHelper
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * RequestHelper constructor.
     *
     * @param Request|null $request
     */
    public function __construct(Request $request = NULL)
    {
        $this->request = $request;
    }

    public function sessionId()
    {
        return Session::getId();
    }

    public function clientIp(): string
    {
        $ip = NULL;

        if ($this->request)
            $ip = $this->request->getClientIp();

        if (!$ip)
            $ip = gethostname();

        return $ip;
    }

    public function requestUri(): string
    {
        return $this->request->getRequestUri();
    }

    public function userAgent(): string
    {
        return $this->request->header('User-Agent');
    }
}