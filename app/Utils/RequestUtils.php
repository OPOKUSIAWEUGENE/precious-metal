<?php

namespace App\Utils;

use Jenssegers\Agent\Agent;

class RequestUtils
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Capture all relevant request details
     *
     * @return array
     */
    public function captureRequestDetails()
    {
        $platform = $this->agent->platform();
        $device = $this->agent->device();
        $browser = $this->agent->browser();
        $browserVersion = $this->agent->version($browser);
        $userAgent = $this->agent->getUserAgent();

        // Grouping device type under a single field
        $deviceType = 'Desktop';  // Default to Desktop
        if ($this->agent->isMobile()) {
            $deviceType = 'Mobile';
        } elseif ($this->agent->isTablet()) {
            $deviceType = 'Tablet';
        }

        return [
            'platform' => $platform,
            'device' => $device,
            'browser' => $browser,
            'browser_version' => $browserVersion,
            'user_agent' => $userAgent,
            'device_type' => $deviceType,  // Single field for device type
            'ip_address' => request()->ips(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now(),
        ];
    }
}