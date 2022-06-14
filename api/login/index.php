<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/params.php");
    require_once("../../lib/session.php");

    require_once("../../database/models/user.php");
    require_once('../../database/models/user.php');
    require_once('../../database/models/query.php');

    APIRoute(
        post: function () {
            $session = new Session();
            
            if ($session->isAuthenticated())
                APIError(HTTPStatusCode::FORBIDDEN, 'Can\'t login if already logged in');

            $params = parseParams(body: [
                'username' => new StringParam(min_len: 1),
                'password' => new StringParam(min_len: 1),
            ]);
        
            $candidateUser = User::getWithFilters([new Equals('name', $params['username'])]);
        
            $user = (count($candidateUser) > 0) ? $candidateUser[0] : null;
        
            if ($user === null || !$user->validatePassword($params['password'])) {
                APIError(HTTPStatusCode::FORBIDDEN, 'Invalid credentials');
            }
        
            $session->set('user', $user->id);

            return ['user' => $user];
        },
        delete: function() {
            $session = new Session();
            
            if ($session->isAuthenticated())
                $session->set('user', null);
                
            if ($session->get('cart') !== null)
                $session->unset('cart');

            return [];
        }
    );
?>
