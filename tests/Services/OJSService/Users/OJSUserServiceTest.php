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

        HookRegistry::clear('userdao::_insertobject');
        HookRegistry::register('userdao::_insertobject', function($hookName, $args) {
            $args[2] = true;
            return true;
        });

        HookRegistry::clear('dao::_updatedataobjectsettings');
        HookRegistry::register('dao::_updatedataobjectsettings', function($hookName, $args) {
            $args[2] = true;
            return true;
        });

        HookRegistry::clear('usergroupdao::_getbyid');
        HookRegistry::register('usergroupdao::_getbyid', function($hookName, $args) {
            $args[2] = new ADORecordSet_array();
            $args[2]->_numOfRows = 1;
            $args[2]->_currentRow= 0;
            $args[2]->fields = $args[2]->bind = [
                'user_group_id' => 1,
                'role_id' => 1,
                'context_id' => 1,
                'is_default' => 1,
                'show_title' => 1,
                'permit_self_registration' => 1,
                'permit_metadata_edit' => 1
            ];
            return true;
        });

        HookRegistry::clear('UserGroupDAO::_returnFromRow');
        HookRegistry::register('UserGroupDAO::_returnFromRow', function($hookName, $args) {
            return true;
        });

        HookRegistry::clear('usergroupdao::_useringroup');
        HookRegistry::register('usergroupdao::_useringroup', function($hookName, $args) {
            $args[2] = new ADORecordSet_array();
            $args[2]->fields[0] = 1;
            return true;
        });
        $return = $OjsClient->createUpdateUser([
            'username' => 'jhon',
            'password' => 'test',
            'email' => 'jhon.doe@test.coop',
            'mailingAddress' => 'Street 55',
            'locales' => ['en_US'],
            'groups' => [1],// administrator
            'givenName'     => ['en_US' => 'Jhon'],
            'familyName'    => ['en_US' => 'Doe'],
            'phone'         => '+123456789',
            'lattes'        => 'https://lattes.com'
        ]);
        $this->assertTrue($return);
    }
}
