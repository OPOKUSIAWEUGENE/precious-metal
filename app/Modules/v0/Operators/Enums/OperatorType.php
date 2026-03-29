<?php

namespace App\Modules\v0\Operators\Enums;

enum OperatorType: string
{
    case CLIENT = 'CLIENT';
    case PLATFORM = 'PLATFORM';

    /**
     * Get the label for the operator type.
     */
    public function label(): string
    {
        return match ($this) {
            self::CLIENT => 'Client',
            self::PLATFORM => 'Platform',
        };
    }

    /**
     * Check if this is a client operator type.
     */
    public function isClient(): bool
    {
        return $this === self::CLIENT;
    }

    /**
     * Check if this is a platform operator type.
     */
    public function isPlatform(): bool
    {
        return $this === self::PLATFORM;
    }
}
