<?php

namespace Fnp\Audit\Listeners;

use Carbon\Carbon;
use Fnp\Audit\Jobs\StoreAuditJob;
use Fnp\Audit\Registry\AuditEventRegistry;
use Fnp\Audit\Services\AuditService;
use Fnp\Dto\Common\Flags\DtoToArrayFlags;
use Fnp\Dto\Common\Helper\Iof;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AuditEventListener
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Create the event listener.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param string $handle
     * @param object $event
     *
     * @return void
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function handle($handle, $event = NULL)
    {
        if (is_array($event))
            $event = $event[0];

        if (!is_object($event))
            return;

        $eventClass = get_class($event);

        if (!AuditEventRegistry::hasClass($eventClass))
            return;

        $payload              = new Collection();
        $reflectionClass      = new \ReflectionClass($event);
        $reflectionProperties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);
        $eventHandle          = AuditEventRegistry::getHandle($eventClass);

        /*
         * If event is serializable then just take the data.
         * Property serialization otherwise
         */
        if (Iof::arrayable($event)) {
            $payload = new Collection($event->toArray());
        } else {
            foreach ($reflectionProperties as $reflectionProperty) {

                $propertyName  = $reflectionProperty->getName();
                $propertyValue = $reflectionProperty->getValue($event);

                /*
                 * Ignore properties with no value
                 */
                if (is_null($propertyValue))
                    continue;

                /*
                 * Serialize the properties
                 */
                if (is_object($propertyValue) && Iof::stringable($propertyValue)) {
                    $payload->put('_' . $propertyName, $propertyValue->__toString());
                } elseif (is_object($propertyValue) && Iof::arrayable($propertyValue)) {
                    $payload->put('&' . $propertyName, $propertyValue->toArray(
                        DtoToArrayFlags::EXCLUDE_NULLS +
                        DtoToArrayFlags::SERIALIZE_STRING_PROVIDERS +
                        DtoToArrayFlags::PREFER_STRING_PROVIDERS
                    ));
                } elseif (is_object($propertyValue)) {
                    $payload->put('@' . $propertyName, get_object_vars($propertyValue));
                } else {
                    $payload->put('$' . $propertyName, $propertyValue);
                }
            }
        }

        $ip   = AuditService::requestIpAddress($this->request);
        $when = Carbon::now();
        Auth::check();

        StoreAuditJob::dispatch(
            $eventHandle,
            $when,
            AuditService::session(),
            $ip,
            $payload->toArray(),
            AuditService::requestDetails($this->request),
            optional(Auth::getUser())->id
        )->onQueue(Config::get('audit.queue'));
    }
}
