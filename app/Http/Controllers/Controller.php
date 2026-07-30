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
    name: "Auth",
    description: "Authentication and session endpoints"
)]
#[OA\Tag(
    name: "Assets",
    description: "Asset wallets, quotes, pricing, and withdrawal endpoints"
)]
#[OA\Tag(
    name: "Customer",
    description: "Customer profile and account management"
)]
abstract class Controller extends BaseController
{
    //
}
