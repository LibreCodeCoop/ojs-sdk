<?php

namespace OjsSdk\Services\OJSService\Users;

use GuzzleHttp\Cookie\CookieJar;
use \DAORegistry;
use OjsSdk\Providers\Ojs\OjsProvider;

class OJSUserService
{
    private $ojsBasePath;
    private $_client;

    public function __construct(\GuzzleHttp\Client $client)
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
    public function createUser(String $nome, String $sobrenome, String $email, String $login, String $password, string $telefone, string $endereco)
    {
        try {
            OjsProvider::getApplication();
            $userDao = DAORegistry::getDAO('UserDAO');
            $userToImport = new \User();
            $data = [
                'username' => $login,
                'password' => $password,
                'email' => $email,
                'phone' => $telefone,
                'mailingAddress' => $endereco
            ];
            $userToImport->setAllData($data);
            $userToImport->setPassword(\Validation::encryptCredentials($userToImport->getUsername(), $password));
            $userDao->insertObject($userToImport);
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
            $userGroupDao->assignUserToGroup($userToImport->getId(), 14);
        } catch (\Exception $e) {
            throw new \Exception("Error creating OJS user: $e");
        }
         return true;
    }
}
