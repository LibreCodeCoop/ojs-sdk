<?php

use PHPUnit\Framework\TestCase;
use OjsSdk\Services\OJSService\Users\OJSUserService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;

class OJSUserServiceTest extends TestCase
{
    public function testUserLogin()
    {
        $mock = new MockHandler([
            new Response(200, ['Set-cookie' => ['OJSSID=91a6b30db3337105fefa2bc2c2520421; path=/; domain=nginx-ojs']])
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $OjsClient = new OJSUserService($client);
        $loginResponse = $OjsClient->login('lyseontech', '123');

        $this->assertNotEmpty($loginResponse);
    }

    public function testCreateUserReturnTrue()
    {
        $mock = new MockHandler([
            new Response(200, ['Set-cookie' => ['OJSSID=91a6b30db3337105fefa2bc2c2520421; path=/; domain=nginx-ojs']])
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $OjsClient = new OJSUserService($client);
//         $ojsBasePath = getenv('OJS_BASE_PATH');
//         $this->_client = new Client([
//             'base_uri' => $ojsBasePath
//         ]);
//         $OjsClient = new OJSUserService($this->_client);
        $return = $OjsClient->createUser('Fulano', 'Souza', 'fulano@teste.com', 'fulano', '123', '7777777777', 'Rua dos Bobos');
        $this->assertTrue($return);
    }
}
