<?php

namespace Thruway\Message;

use Thruway\Message\Traits\ArgumentsTrait;
use Thruway\Message\Traits\DetailsTrait;

/**
 * Class EventMessage
 * Event dispatched by Broker to Subscribers for subscription the event was matching.
 * <code>[EVENT, SUBSCRIBED.Subscription|id, PUBLISHED.Publication|id, Details|dict]</code>
 * <code>[EVENT, SUBSCRIBED.Subscription|id, PUBLISHED.Publication|id, Details|dict, PUBLISH.Arguments|list]</code>
 * <code>[EVENT, SUBSCRIBED.Subscription|id, PUBLISHED.Publication|id, Details|dict, PUBLISH.Arguments|list, PUBLISH.ArgumentsKw|dict]</code>
 *
 * @package Thruway\Message
 */

class EventMessage extends Message
{

    use DetailsTrait;

    /**
     * using arguments trait
     * @see \Thruway\Message\ArgumentsTrait
     */
    use ArgumentsTrait;

    /**
     * @var int
     */
    private $subscriptionId;
    /**
     * @var int
     */
    private $publicationId;

    /**
     * Constructor
     *
     * @param int $subscriptionId
     * @param int $publicationId
     * @param \stdClass $details
     * @param mixed $arguments
     * @param mixed $argumentsKw
     */
    public function __construct($subscriptionId, $publicationId, $details, $arguments = null, $argumentsKw = null)
    {
        $this->setArguments($arguments);
        $this->setArgumentsKw($argumentsKw);
        $this->setDetails($details);
        $this->setPublicationId($publicationId);
        $this->setSubscriptionId($subscriptionId);
    }

    /**
     * Get message code
     *
     * @return int
     */
    public function getMsgCode()
    {
        return static::MSG_EVENT;
    }

    /**
     * This is used by get message parts to get the parts of the message beyond
     * the message code
     *
     * @return array
     */
    public function getAdditionalMsgFields()
    {
        $details = $this->getDetails();
        if ($details === null) {
            $details = new \stdClass();
        }

        $details = (object)$details;

        $a = [
            $this->getSubscriptionId(),
            $this->getPublicationId(),
            $details
        ];

        $a = array_merge($a, $this->getArgumentsForSerialization());

        return $a;
    }

    /**
     * Create event message from publish message
     *
     * @param \Thruway\Message\PublishMessage $msg
     * @param int $subscriptionId
     * @return \Thruway\Message\EventMessage
     */
    public static function createFromPublishMessage(PublishMessage $msg, $subscriptionId)
    {
        return new static(
            $subscriptionId,
            $msg->getRequestId(),
            $msg->getOptions(),
            $msg->getArguments(),
            $msg->getArgumentsKw()
        );
    }

    /**
     * Set publication ID
     *
     * @param int $publicationId
     */
    public function setPublicationId($publicationId)
    {
        $this->publicationId = $publicationId;
    }

    /**
     * Get publication ID
     *
     * @return int
     */
    public function getPublicationId()
    {
        return $this->publicationId;
    }

    /**
     * Set subscription ID
     *
     * @param int $subscriptionId
     */
    public function setSubscriptionId($subscriptionId)
    {
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * Get subscription ID
     *
     * @return int
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

} 