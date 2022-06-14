<?php

    require_once("../lib/session.php");
    require_once("../lib/params.php");
    require_once("../lib/page.php");

    $params = parseParams(query: [
        'csrf'
    ]);

    $session = new Session();
    
    if ($session->get('csrf') !== $params['csrf'])
        pageError(HTTPStatusCode::BAD_REQUEST);

    if ($session->isAuthenticated())
        $session->set('user', null);
        
    if ($session->get('cart') !== null)
        $session->unset('cart');
        
    $session->set('easter-egg', false);
    header('Location: /');
?>