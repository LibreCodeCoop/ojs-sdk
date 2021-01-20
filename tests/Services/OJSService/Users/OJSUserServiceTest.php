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

    public function testCreateUpdateUserReturnTrue()
    {
        $OjsClient = new OJSUserService();

        HookRegistry::clear('userdao::_getuserbyemail');
        HookRegistry::register('userdao::_getuserbyemail', function($hookName, $args) {
            $args[2] = new ADORecordSet_empty();
            return true;
        });
        $return = $OjsClient->createUpdateUser([
            'username' => 'test',
            'password' => 'test',
            'email' => 'test@test.coop',
            'mailingAddress' => 'Street 55',
            'locales' => ['en_US'],
            'groups' => [1],// administrator
            'givenName'     => ['en_US' => 'User'],
            'familyName'    => ['en_US' => 'Test'],
            'phone'         => '+123456789',
            'lattes'        => 'https://lattes.com'
        ]);
        $this->assertTrue($return);
    }
}
