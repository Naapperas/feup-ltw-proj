<?php 
declare(strict_types=1);

session_start();

function processAuthErrors() {

    if (!isset($_SESSION['auth-error'])) return;

    $error = $_SESSION['auth-error'];
    unset($_SESSION['auth-error']);
?>
    <span><?= $error ?></span>
<?php } ?>