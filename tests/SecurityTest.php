<?php
namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;



class SecurityTest extends ApiTestCase
{
    protected function setUp() : void {
        $kernel = self::bootKernel();

    }

    public function testInvalidToken():void
    {
        $response = static::createClient()->request('GET','/mainCategories',['headers' => ['x-api-token' => 'fake-token']]);
        $this->assertResponseIsSuccessful();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
    }
    
}
