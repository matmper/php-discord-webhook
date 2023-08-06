<?php

namespace Matmper\Contracts;

interface Webhook
{
    /**
     * Set message type
     *
     * @param string $type
     * @return self
     */
    public function type(string $type): self;

    /**
     * Set message
     *
     * @param mixed $message
     * @return self
     */
    public function message($message): self;

    /**
     * Send a new webhook message request
     *
     * @return object
     */
    public function send(): object;
}
