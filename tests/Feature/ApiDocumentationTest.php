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
                'Assets',
            ])
            ->assertSee('API testing', false);
    }

    public function test_platform_docs_page_shows_auth_first(): void
    {
        $this->get('/developer/docs/platform')
            ->assertOk()
            ->assertSeeInOrder([
                'Platform Operator API Documentation',
                'Platform Operator Auth',
                'Platform Assets',
            ]);
    }
}
