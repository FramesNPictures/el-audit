<?php

namespace Fnp\Audit\Listeners;

use Fnp\Audit\Contracts\Auditable;
use Fnp\Audit\Events\NewAuditEntry;
use Fnp\Audit\Registry\AuditEventRegistry;
use Fnp\Dto\Common\Flags\DtoToArrayFlags;
use Fnp\Dto\Common\Helper\Iof;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

class AuditEventListener
{
    /**
     * Default handler for Subscribed Events
     *
     * @param object|null $event
     *
     * @throws \ReflectionException
     */
    public function handle($event = NULL)
    {
        if (is_array($event))
            $event = $event[0];

        if (!is_object($event))
            return;

        $eventClass  = get_class($event);
        $eventHandle = AuditEventRegistry::getHandle($eventClass);

        if (!AuditEventRegistry::hasClass($eventClass))
            return;

        if ($event instanceof Auditable)
            $payload = new Collection($event->auditPayload());
        else
            $payload = $this->extractPayloadFromEvent($event);

        NewAuditEntry::dispatch($eventHandle, $payload);
    }

    /**
     * Extract Payload From the Event Object
     *
     * @param $event
     *
     * @return Collection
     * @throws \ReflectionException
     */
    private function extractPayloadFromEvent($event)
    {
        $reflectionClass      = new ReflectionClass($event);
        $reflectionProperties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        /*
         * If event is serializable then just take the data.
         */
        if (Iof::arrayable($event))
            /** @noinspection PhpUndefinedMethodInspection */
            return new Collection($event->toArray());

        $payload = new Collection();

        /*
         * Otherwise get all the public properties
         */
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
                $payload->put($propertyName, $propertyValue->__toString());
            } elseif (is_object($propertyValue) && Iof::arrayable($propertyValue)) {
                $payload->put($propertyName, $propertyValue->toArray(
                    DtoToArrayFlags::EXCLUDE_NULLS +
                    DtoToArrayFlags::SERIALIZE_STRING_PROVIDERS +
                    DtoToArrayFlags::PREFER_STRING_PROVIDERS
                ));
            } elseif (is_object($propertyValue)) {
                $payload->put($propertyName, get_object_vars($propertyValue));
            } else {
                $payload->put($propertyName, $propertyValue);
            }
        }

        return $payload;
    }
}