<?php

namespace App\Http\Controllers\v0;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="RushHour API",
 *     version="1.0.0",
 *     description="RushHour Transportation Management System API",
 *     @OA\Contact(
 *         email="api@rushour.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8002",
 *     description="Local Development Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\PathItem(
 * )
 * 
 * Common definitions used across all actors
 */
class ApiDocController extends BaseApiController
{
    /**
     * Display API documentation
     * 
     * @OA\Get(
     *     path="/api-docs",
     *     summary="API Documentation",
     *     tags={"Documentation"},
     *     @OA\Response(
     *         response=200,
     *         description="API documentation loaded successfully"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'name' => 'RushHour API',
            'version' => '1.0.0',
            'description' => 'RushHour Transportation Management System API',
            'documentation' => '/api/documentation',
        ]);
    }

    /**
     * Get OpenAPI JSON specification
     * 
     * @OA\Get(
     *     path="/api-docs.json",
     *     summary="OpenAPI JSON Specification",
     *     tags={"Documentation"},
     *     @OA\Response(
     *         response=200,
     *         description="OpenAPI JSON specification"
     *     )
     * )
     */
    public function json(): JsonResponse
    {
        // This would typically serve the generated OpenAPI JSON file
        return response()->json([
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'RushHour API',
                'version' => '1.0.0',
            ],
            'paths' => [],
        ]);
    }
}