<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Precious Metal API",
    version: "1.0.0",
    description: "API documentation for the Precious Metal platform, including Platform Operator and Customer modules.",
    contact: new OA\Contact(email: "support@neutek.local")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Primary API Server"
)]
#[OA\Tag(
    name: "Customer API",
    description: "Customer-facing endpoints and authentication"
)]
#[OA\Tag(
    name: "Platform Operator API",
    description: "Internal platform management, user administration, and customer oversight"
)]
abstract class Controller extends BaseController
{
    //
}
