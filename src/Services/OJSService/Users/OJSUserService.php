<?php

namespace OjsSdk\Services\OJSService\Users;

use GuzzleHttp\Cookie\CookieJar;
use \DAORegistry;
use OjsSdk\Providers\Ojs\OjsProvider;

class OJSUserService
{
    private $ojsBasePath;
    private $_client;

    public function __construct(\GuzzleHttp\Client $client = null)
    {
        $this->_client = $client;
    }

    /**
     * Login method logs in into OJS
     *
     * Login returns * a session id
     *
     * @param string $username User's username
     * @param string $password User's password
     * @return array [sessionId, userId]
     **/
    public function login($username, $password)
    {
        // This cookiejar is used to capture the OJSSID
        $cookieJar = new CookieJar();
        if (empty($username) || empty($password)) {
            throw new \Exception(__CLASS__ . ':' . __METHOD__ . ':: ' . 'Username and password are required.');
        }
        $requestUri = "/index.php/csp/login/signIn";

        $response = $this->_client->request('POST', $requestUri, [
            'cookies' => $cookieJar,
            'form_params' => [
                'username' => $username,
                'password' => $password
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            // Cookies from OJS
            $cookie = array_reduce($cookieJar->toArray(), function ($_, $item) {
                return $item['Name'] == 'OJSSID' ? $item['Value'] : false;
            });
            preg_match('/\$\.pkp\.currentUser = (?<json>[^;]+)/m', $response->getBody()->getContents(), $json);
            if ($json) {
                $json = $json['json'];
                $json = json_decode($json);
                if ($json) {
                    return ['sessionId' => $cookie, 'userId' => $json->id];
                }
            }
            return ['sessionId' => $cookie];
        }

        return [];
    }

    /**
     * Register a new user in OJS
     *
     * This method creates a valid XML
     * to import a new user to OJS database
     *
     * @param String $nome
     * @param String $sobrenome
     * @param String $email
     * @param String $login
     * @param String $senha
     * @return Boolean
     **/
    public function createUser(array $data)
    {
        try {
            OjsProvider::getApplication();
            $userDao = DAORegistry::getDAO('UserDAO');
            $user = new \User();
            $user->setAllData($data);
            $user->setPassword(\Validation::encryptCredentials($user->getUsername(),$data['password']));
            $userId = $userDao->insertObject($user);
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
            foreach ($data['groups'] as $groupId) {
                $userGroupDao->assignUserToGroup($userId, $groupId);
            }
        } catch (\Exception $e) {
            throw new \Exception("Error creating OJS user: $e");
        }
         return true;
    }
    
    public function changePassword(String $email, String $password) 
    {
        OjsProvider::getApplication();
        $userDao = DAORegistry::getDAO('UserDAO');
        $user = $userDao->getUserByEmail($email);
        if ($user) {
            $user->setPassword(\Validation::encryptCredentials($user->getUsername(), $password));
            $userDao->updateObject($user);
        }
    }
    public function getUniqueUsername(String $username)
    {
        OjsProvider::getApplication();
        $userDao = DAORegistry::getDAO('UserDAO');
        $i = '';
        while ($userDao->userExistsByUsername($username.$i)) {
            if ($i === '') {
                $i = 0;
            } else {
                $i++;
            }
        }
        return $username.$i;
    }
}
