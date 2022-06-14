<?php

    require_once("../lib/session.php");

    $session = new Session();
    session_start();

    if ($session->isAuthenticated())
        $session->set('user', null);
        
    if ($session->get('cart') !== null)
        $session->unset('cart');
        
    $session->set('easter-egg', false);
    header('Location: /');
?>