<?php
    session_start();

    if (isset($_SESSION["user"]))
        unset($_SESSION["user"]);
        
    if (isset($_SESSION["cart"]))
        unset($_SESSION["cart"]);

    header('Location: /');
?>