<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/params.php");
    require_once("../../lib/session.php");

    require_once("../../database/models/user.php");

    APIRoute(
        post: function () {
            $session = new Session();
            
            $params = parseParams(body: [
                'username' => new StringParam(min_len: 1),
                'password' => new StringParam(min_len: 1),
            ]);

            require_once('../database/models/user.php');
            require_once('../database/models/query.php');
        
            $candidateUser = User::getWithFilters([new Equals('name', $params['username'])]);
        
            $user = (count($candidateUser) > 0) ? $candidateUser[0] : null;
        
            if ($user === null || !$user->validatePassword($params['password'])) {
                APIError(HTTPStatusCode::FORBIDDEN, 'Invalid credentials');
            }
        
            $session->set('user', $user->id);

            return ['user' => $user];
        },
    );
?>
