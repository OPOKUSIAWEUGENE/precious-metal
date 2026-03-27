<?php

namespace App\Traits;

trait Errors
{
    /**
     * bad requests
     *
     * @var string
     */
    protected static $badRequest = 'The given data is invalid';

    /**
     *  server error
     *
     * @var string
     */
    protected static $serverError = 'An error occurred while processing your request';

    /**
     * not found
     *
     * @var string
     */
    protected static $notFound = 'The resource you are looking for is not found';

    /**
     * unauthorized access
     *
     * @var string
     */
    protected static $unauthorized = 'You are not authorized to perform this action';

    /**
     * forbidden access
     *
     * @var string
     */
    protected static $forbidden = 'You are forbidden to perform this action';

    /**
     * service unavailable
     *
     * @var string
     */
    protected static $serviceUnavailable = 'The service you are looking for is unavailable';


    /**
     *  unable to process request
     *
     * @var string
     */
    protected static $processError = 'Unable to process your request';

    /**
     * bad gateway error
     *
     * @var string
     */
    protected static $badGateway = 'An error occurred while communicating with an upstream service';

    /**
     * resource deleted
     *
     * @param string $resource
     * @return string
     */
    protected static function deleted(string $resource = 'Resource'): string
    {
        return 'The ' . $resource . ' has been deleted successfully';
    }
}
