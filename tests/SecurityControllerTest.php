<?php

namespace App\Tests;

//use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class SecurityControllerTest extends ApiTestCase
{
    public function testAjou(): void
    {
        $response = static::createClient()->request('POST', '/add/{id}/{quantite}');

        $this->assertResponseIsSuccessful();
        
    }
}
