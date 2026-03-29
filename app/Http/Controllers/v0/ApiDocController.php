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
 * Common definitions used across all actors
 */
abstract class BaseApiController extends Controller
{
    // Common response schemas
}

/**
 * ================== COMMON DEFINITIONS ==================
 * These definitions are shared across all user types
 */

/**
 * @OA\Definition(
 *     definition="Error",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Error occurred"),
 *     @OA\Property(property="errors", type="object", example={})
 * )
 * 
 * @OA\Definition(
 *     definition="SuccessResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="data", type="object")
 * )
 * 
 * @OA\Definition(
 *     definition="PaginatedResponse",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="data", type="array"),
 *         @OA\Property(property="current_page", type="integer"),
 *         @OA\Property(property="total", type="integer")
 *     )
 * )
 */

/**
 * ================== PLATFORM OPERATOR DEFINITIONS ==================
 * Platform Operators manage the entire system
 */

/**
 * @OA\Schema(schema="PlatformOperator", 
 *     required={"id", "email", "first_name", "last_name", "phone_number", "profiles"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="profiles", type="array", 
 *         @OA\Items(ref="#/components/schemas/PlatformProfile")
 *     )
 * )
 * 
 * @OA\Schema(schema="PlatformProfile",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="profile_type", type="string", enum={"PLATFORM_OPERATOR", "CLIENT_OPERATOR", "PASSENGER", "DRIVER"}),
 *     @OA\Property(property="role", ref="#/components/schemas/Role"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="is_current", type="boolean")
 * )
 * 
 * @OA\Schema(schema="Role",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string")
 * )
 * 
 * @OA\Schema(schema="Operator",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="code", type="string"),
 *     @OA\Property(property="type", type="string", enum={"PLATFORM", "CLIENT"}),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="active", type="boolean")
 * )
 * 
 * @OA\Schema(schema="Vehicle",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="registration_number", type="string"),
 *     @OA\Property(property="model", type="string"),
 *     @OA\Property(property="make", type="string"),
 *     @OA\Property(property="year", type="integer"),
 *     @OA\Property(property="capacity", type="integer"),
 *     @OA\Property(property="fleet_identifier", type="string"),
 *     @OA\Property(property="status", type="string", enum={"ACTIVE", "MAINTENANCE", "INACTIVE"}),
 *     @OA\Property(property="operator_id", type="string", format="uuid")
 * )
 * 
 * @OA\Schema(schema="Route",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="code", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="distance_km", type="number"),
 *     @OA\Property(property="estimated_duration_minutes", type="integer"),
 *     @OA\Property(property="base_price", type="number"),
 *     @OA\Property(property="status", type="string", enum={"ACTIVE", "INACTIVE"}),
 *     @OA\Property(property="operator_id", type="string", format="uuid")
 * )
 * 
 * @OA\Schema(schema="Trip",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="route_id", type="string", format="uuid"),
 *     @OA\Property(property="vehicle_id", type="string", format="uuid"),
 *     @OA\Property(property="driver_profile_id", type="string", format="uuid"),
 *     @OA\Property(property="operator_id", type="string", format="uuid"),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time"),
 *     @OA\Property(property="status", type="string", enum={"SCHEDULED", "IN_PROGRESS", "COMPLETED", "CANCELLED"}),
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="available_seats", type="integer"),
 *     @OA\Property(property="booked_seats", type="integer")
 * )
 * 
 * @OA\Schema(schema="Settlement",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="operator_id", type="string", format="uuid"),
 *     @OA\Property(property="driver_profile_id", type="string", format="uuid"),
 *     @OA\Property(property="period_start", type="string", format="date"),
 *     @OA\Property(property="period_end", type="string", format="date"),
 *     @OA\Property(property="total_amount", type="number"),
 *     @OA\Property(property="commission_amount", type="number"),
 *     @OA\Property(property="net_amount", type="number"),
 *     @OA\Property(property="status", type="string", enum={"PENDING", "PROCESSING", "COMPLETED", "FAILED"})
 * )
 * 
 * @OA\Schema(schema="Commission",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="trip_id", type="string", format="uuid"),
 *     @OA\Property(property="driver_profile_id", type="string", format="uuid"),
 *     @OA\Property(property="amount", type="number"),
 *     @OA\Property(property="rate", type="number"),
 *     @OA\Property(property="status", type="string", enum={"PENDING", "PAID", "FAILED"}),
 *     @OA\Property(property="settlement_id", type="string", format="uuid", nullable=true)
 * )
 */

/**
 * ================== CLIENT OPERATOR DEFINITIONS ==================
 * Client Operators manage their own fleet and operations
 */

/**
 * @OA\Schema(schema="ClientOperator", 
 *     required={"id", "email", "first_name", "last_name", "phone_number", "profiles"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="profiles", type="array", 
 *         @OA\Items(ref="#/components/schemas/ClientProfile")
 *     )
 * )
 * 
 * @OA\Schema(schema="ClientProfile",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="profile_type", type="string", enum={"CLIENT_OPERATOR"}),
 *     @OA\Property(property="role", ref="#/components/schemas/Role"),
 *     @OA\Property(property="operator", ref="#/components/schemas/Operator"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="is_current", type="boolean")
 * )
 */

/**
 * ================== DRIVER DEFINITIONS ==================
 * Drivers operate vehicles and manage trips
 */

/**
 * @OA\Schema(schema="Driver", 
 *     required={"id", "email", "first_name", "last_name", "phone_number", "profiles"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="profiles", type="array", 
 *         @OA\Items(ref="#/components/schemas/DriverProfile")
 *     )
 * )
 * 
 * @OA\Schema(schema="DriverProfile",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="profile_type", type="string", enum={"DRIVER"}),
 *     @OA\Property(property="role", ref="#/components/schemas/Role"),
 *     @OA\Property(property="operator", ref="#/components/schemas/Operator"),
 *     @OA\Property(property="license", type="string"),
 *     @OA\Property(property="driver_status", type="string", enum={"AVAILABLE", "ON_TRIP", "OFF_DUTY", "SUSPENDED"}),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="is_current", type="boolean")
 * )
 * 
 * @OA\Schema(schema="DriverVehicle",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="driver_profile_id", type="string", format="uuid"),
 *     @OA\Property(property="vehicle_id", type="string", format="uuid"),
 *     @OA\Property(property="assigned_at", type="string", format="date-time"),
 *     @OA\Property(property="is_active", type="boolean")
 * )
 */

/**
 * ================== PASSENGER DEFINITIONS ==================
 * Passengers book and travel on trips
 */

/**
 * @OA\Schema(schema="Passenger", 
 *     required={"id", "email", "first_name", "last_name", "phone_number", "profiles"},
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="phone_number", type="string"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="profiles", type="array", 
 *         @OA\Items(ref="#/components/schemas/PassengerProfile")
 *     )
 * )
 * 
 * @OA\Schema(schema="PassengerProfile",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="profile_type", type="string", enum={"PASSENGER"}),
 *     @OA\Property(property="role", ref="#/components/schemas/Role"),
 *     @OA\Property(property="active", type="boolean"),
 *     @OA\Property(property="is_current", type="boolean")
 * )
 * 
 * @OA\Schema(schema="Booking",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="trip_id", type="string", format="uuid"),
 *     @OA\Property(property="passenger_profile_id", type="string", format="uuid"),
 *     @OA\Property(property="seat_count", type="integer"),
 *     @OA\Property(property="status", type="string", enum={"PENDING", "CONFIRMED", "CANCELLED", "COMPLETED"}),
 *     @OA\Property(property="total_price", type="number"),
 *     @OA\Property(property="pickup_location", type="string"),
 *     @OA\Property(property="dropoff_location", type="string")
 * )
 * 
 * @OA\Schema(schema="Payment",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="booking_id", type="string", format="uuid"),
 *     @OA\Property(property="passenger_profile_id", type="string", format="uuid"),
 *     @OA\Property(property="amount", type="number"),
 *     @OA\Property(property="currency", type="string"),
 *     @OA\Property(property="payment_method", type="string", enum={"CASH", "CARD", "MOBILE_MONEY", "BANK_TRANSFER", "WALLET", "CREDIT"}),
 *     @OA\Property(property="status", type="string", enum={"PENDING", "PROCESSING", "COMPLETED", "FAILED", "REFUNDED"}),
 *     @OA\Property(property="transaction_id", type="string"),
 *     @OA\Property(property="gateway", type="string"),
 *     @OA\Property(property="paid_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(schema="RouteStop",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="latitude", type="number"),
 *     @OA\Property(property="longitude", type="number"),
 *     @OA\Property(property="city", type="string")
 * )
 */

/**
 * ================== AUTHENTICATION DEFINITIONS ==================
 */

/**
 * @OA\Schema(schema="LoginRequest",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password")
 * )
 * 
 * @OA\Schema(schema="RegisterRequest",
 *     required={"email", "password", "first_name", "last_name", "phone_number"},
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string", format="password"),
 *     @OA\Property(property="password_confirmation", type="string", format="password"),
 *     @OA\Property(property="first_name", type="string"),
 *     @OA\Property(property="last_name", type="string"),
 *     @OA\Property(property="phone_number", type="string")
 * )
 * 
 * @OA\Schema(schema="AuthResponse",
 *     @OA\Property(property="success", type="boolean"),
 *     @OA\Property(property="message", type="string"),
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="user", ref="#/components/schemas/PlatformOperator"),
 *         @OA\Property(property="access_token", type="string"),
 *         @OA\Property(property="token_type", type="string", example="Bearer"),
 *         @OA\Property(property="expires_in", type="integer")
 *     )
 * )
 */

/**
 * ================== CALLBACK DEFINITIONS ==================
 */

/**
 * @OA\Schema(schema="Callback",
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="type", type="string", enum={"INCOMING", "OUTGOING"}),
 *     @OA\Property(property="status", type="string", enum={"PENDING", "SENT", "FAILED", "DELIVERED"}),
 *     @OA\Property(property="endpoint", type="string"),
 *     @OA\Property(property="method", type="string"),
 *     @OA\Property(property="payload", type="object"),
 *     @OA\Property(property="response", type="object"),
 *     @OA\Property(property="retry_count", type="integer"),
 *     @OA\Property(property="scheduled_at", type="string", format="date-time"),
 *     @OA\Property(property="sent_at", type="string", format="date-time"),
 *     @OA\Property(property="delivered_at", type="string", format="date-time"),
 *     @OA\Property(property="error_message", type="string")
 * )
 */
class ApiDocController extends BaseApiController
{
    //
}