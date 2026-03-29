<?php

namespace App\Modules\v0\User\Enums;

enum ProfileType: string
{
    case PLATFORM_OPERATOR = 'PLATFORM_OPERATOR';
    case CLIENT_OPERATOR = 'CLIENT_OPERATOR';
    case PASSENGER = 'PASSENGER';
    case DRIVER = 'DRIVER';

    /**
     * Get the label for the profile type.
     */
    public function label(): string
    {
        return match ($this) {
            self::PLATFORM_OPERATOR => 'Platform Operator',
            self::CLIENT_OPERATOR => 'Client Operator',
            self::PASSENGER => 'Passenger',
            self::DRIVER => 'Driver',
        };
    }

    /**
     * Check if this profile type has operator privileges.
     */
    public function isOperator(): bool
    {
        return in_array($this, [self::PLATFORM_OPERATOR, self::CLIENT_OPERATOR]);
    }

    /**
     * Check if this profile type is a driver.
     */
    public function isDriver(): bool
    {
        return $this === self::DRIVER;
    }

    /**
     * Check if this profile type is a passenger.
     */
    public function isPassenger(): bool
    {
        return $this === self::PASSENGER;
    }
}