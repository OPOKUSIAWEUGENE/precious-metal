<?php

namespace App\Modules\v0\User\Enums;

enum DriverStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case ON_TRIP = 'ON_TRIP';
    case OFF_DUTY = 'OFF_DUTY';
    case ON_BREAK = 'ON_BREAK';
    case SUSPENDED = 'SUSPENDED';

    /**
     * Get the label for the driver status.
     */
    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::ON_TRIP => 'On Trip',
            self::OFF_DUTY => 'Off Duty',
            self::ON_BREAK => 'On Break',
            self::SUSPENDED => 'Suspended',
        };
    }

    /**
     * Check if the driver is available for trips.
     */
    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }

    /**
     * Check if the driver is active (not suspended).
     */
    public function isActive(): bool
    {
        return $this !== self::SUSPENDED;
    }

    /**
     * Get all available statuses for a driver to be assigned.
     */
    public static function assignableStatuses(): array
    {
        return [
            self::AVAILABLE,
            self::ON_TRIP,
            self::OFF_DUTY,
            self::ON_BREAK,
        ];
    }
}