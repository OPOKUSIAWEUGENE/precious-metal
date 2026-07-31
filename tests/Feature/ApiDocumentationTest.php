<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    public function test_docs_landing_page_is_available(): void
    {
        $this->get('/docs')
            ->assertOk()
            ->assertSee('Interactive API docs built from the OpenAPI specs', false)
            ->assertSee('Customer API Documentation', false)
            ->assertSee('Platform Operator API Documentation', false);
    }

    public function test_customer_docs_page_is_available(): void
    {
        $this->get('/developer/docs/customer')
            ->assertOk()
            ->assertSeeInOrder([
                'Customer API Documentation',
                'Auth',
                '/v0/customers/auth/login',
                '/v0/customers/auth/register',
            ])
            ->assertSee('customer@gmail.com', false)
            ->assertSee('12345', false)
            ->assertSee('1|customer_sanctum_token_example', false)
            ->assertSee('API testing', false);
    }

    public function test_platform_docs_page_shows_auth_first(): void
    {
        $this->get('/developer/docs/platform')
            ->assertOk()
            ->assertSeeInOrder([
                'Platform Operator API Documentation',
                'Platform Operator Auth',
                '/v0/auth/platform-managers/login',
                '/v0/auth/platform-managers/logout',
            ])
            ->assertSee('admin@gmail.com', false)
            ->assertSee('System', false)
            ->assertSee('a2636ba5-10c4-4c2c-87ef-51c6b526e55f|F7YA08MSz0P1TcRvJijUIP1Q6rOOEByUtmfX1jp26c4a5496', false);
    }
}
