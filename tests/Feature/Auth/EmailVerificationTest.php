<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_routes_are_not_enabled(): void
    {
        $response = $this->get('/verify-email');

        $response->assertNotFound();
    }
}
