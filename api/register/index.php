<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/params.php");
    require_once("../../lib/session.php");
    require_once('../../lib/password.php');

    
    require_once('../../database/models/query.php');
    require_once("../../database/models/user.php");

    APIRoute(
        post: function () {
            $session = new Session();
            
            if ($session->isAuthenticated())
                APIError(HTTPStatusCode::FORBIDDEN, 'Can\'t register a new user if already logged in');
            
            $params = parseParams(body: [
                'email' => new StringParam(
                    pattern: '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/',
                    case_insensitive: true
                ),
                'username' => new StringParam(min_len: 1),
                'password' => new StringParam(min_len: 8),
                'name' => new StringParam(min_len: 1),
                'address' => new StringParam(min_len: 1),
                'phone' => new StringParam(pattern: '/^\d{9}$/'),
            ]);

            $registrationError = function(string $errorMsg): void {
                APIError(HTTPStatusCode::UNPROCESSABLE_ENTITY, $errorMsg);
            };

            $userNameExists = count(User::getWithFilters([new Equals('name', $params['username'])])) > 0;
            if ($userNameExists)
                $registrationError('User with the same name already registered.');

            $userEmailExists = count(User::getWithFilters([new Equals('email', $params['email'])])) > 0;
            if ($userEmailExists)
                $registrationError('User with the same email already registered.');
                
            $userPhoneExists = count(User::getWithFilters([new Equals('phone_number', $params['phone'])])) > 0;
            if ($userPhoneExists) {
                $registrationError('User with the same phone number already registered.');
            }

            $user = User::create([
                'name' => $params['username'],
                'password' => hashPassword($params['password']),
                'email' => $params['email'],
                'address' => $params['address'],
                'phone_number' => $params['phone'],
                'full_name' => $params["name"]
            ]);

            if ($user === null) {
                $registrationError('Error registering user');
            }

            return ['user' => $user];
        },
    );
?>
