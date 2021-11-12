<?php


namespace AvidBase;


class Client
{
    protected $_baseUrl;

    protected $_accountId;
    protected $_apiKey;

    protected $_client;

    protected $_machineAccessToken;

    public function __construct($account, $key, $isProduction)
    {
        if ($isProduction) {
            $this->_baseUrl = "https://api.avidbase.com/";
        } else {
            $this->_baseUrl = "https://dev-api.avidbase.com/";
        }
        $this->_accountId = $account;
        $this->_apiKey = $key;
        $this->_client = new \GuzzleHttp\Client([
            'base_uri' => $this->_baseUrl,
        ]);
    }

    // isValidMachineAccessToken Validates whether the machine access token is available or not
    // if not available generate a new machine access token
    private function isValidMachineAccessToken()
    {
        return $this->generateToken();
    }

    // Generate a new machine access token using api key
    private function generateToken()
    {
        if ($this->_accountId == null || $this->_apiKey == null) {
            return false;
        }
        $data = [
            "api_key" => $this->_apiKey,
        ];
        try {
            $response = $this->_client->request('POST', "v1/account/" . $this->_accountId . "/token", ['json' => $data]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return false;
        }

        // Set the machine access token if it exists
        if ($response->hasHeader('Access-Token')) {
            $this->_machineAccessToken = $response->getHeader('Access-Token')[0];
            return true;
        }

        return false;
    }

    // Authenticate the existing user using email and password
    public function Login($emailOrUsername, $password)
    {
        $data = [
            "password" => $password,
            "account_uuid" => $this->_accountId,
        ];
        if (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL)) {
            $data['email'] = $emailOrUsername;
        } else {
            $data['username'] = $emailOrUsername;
        }

        try {
            $response = $this->_client->request('POST', "v1/auth", ['json' => $data]);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return ["", []];
        }

        // Set the machine access token if it exists
        if ($response->hasHeader('Access-Token')) {
            $userAccessToken = $response->getHeader('Access-Token')[0];
            return [$userAccessToken, json_decode($response->getBody()->getContents(), true)];
        }

        return ["", []];
    }

    // List all the users using machine access token
    public function FindUser($emailOrUsername)
    {
        if ($this->isValidMachineAccessToken()) {
            try {
                $response = $this->_client->request('GET', "v1/user:find", [
                    "headers" => ["Access-Token" => $this->_machineAccessToken],
                    "query" => ["search_text" => $emailOrUsername]
                ]);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return [];
            }

            // See if the response is success or not
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        return [];
    }

    // List all the users using machine access token
    public function ListUsers()
    {
        if ($this->isValidMachineAccessToken()) {
            try {
                $response = $this->_client->request('GET', "v1/user", ["headers" => ["Access-Token" => $this->_machineAccessToken]]);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return [];
            }

            // See if the response is success or not
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        return [];
    }

    // Get a user using user id and machine access token
    public function GetUser($userId)
    {
        if ($this->isValidMachineAccessToken()) {
            try {
                $response = $this->_client->request('GET', "v1/user/" . $userId, ["headers" => ["Access-Token" => $this->_machineAccessToken]]);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return [];
            }

            // See if the response is success or not
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents(), true);
            }
        }

        return [];
    }

    // Create a new user using machine access token
    public function CreateUser(User $user)
    {
        if ($this->isValidMachineAccessToken()) {
            $data = [
                "first_name" => $user->FirstName,
                "last_name" => $user->LastName,
                "email" => $user->Email,
                "username" => $user->Username,
                "password" => $user->Password,
                "data" => $user->Data,
            ];

            try {
                $response = $this->_client->request('POST', "v1/user", ['json' => $data, "headers" => ["Access-Token" => $this->_machineAccessToken]]);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return false;
            }

            // See if the response is success or not
            if ($response->getStatusCode() == 200) {
                return true;
            }
        }

        return false;
    }

    // Update an existing user using user id and machine access token
    public function UpdateUser($userId, User $user)
    {
        if ($this->isValidMachineAccessToken()) {
            $data = [
                "first_name" => $user->FirstName,
                "last_name" => $user->LastName,
                "email" => $user->Email,
                "username" => $user->Username,
                "password" => $user->Password,
                "data" => $user->Data,
            ];

            try {
                $response = $this->_client->request('PUT', "v1/user/" . $userId, ['json' => $data, "headers" => ["Access-Token" => $this->_machineAccessToken]]);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return false;
            }

            // See if the response is success or not
            if ($response->getStatusCode() == 200) {
                return true;
            }
        }

        return false;
    }

    // Add role to an existing user using user id and role name
    public function AddRole($userId, $roleName)
    {
        if ($this->isValidMachineAccessToken()) {
            try {
                $response = $this->_client->request('PUT', "v1/user/" . $userId . "/role/" . $roleName, ["headers" => ["Access-Token" => $this->_machineAccessToken]]);
            } catch (\GuzzleHttp\Exception\GuzzleException $e) {
                return false;
            }

            // See if the response is success or not
            if ($response->getStatusCode() == 200) {
                return true;
            }
        }

        return false;
    }
}