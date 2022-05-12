<?php
    session_start();

    if (isset($_SESSION["user"]))
        session_unset();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>