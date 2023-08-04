<?php

declare(strict_types=1);

namespace Matmper\Enums;

/**
 * @phpstan-type TMessageType = success | danger | warning | default
 */
enum MessageType
{
    const SUCCESS = 'success';
    const DANGER = 'danger';
    const WARNING = 'warning';
    const DEFAULT = 'default';
}
