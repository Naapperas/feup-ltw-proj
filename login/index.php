<?php 
declare(strict_types=1);

require_once("../templates/components.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "Login", description: "Login page for XauFome.", 
        scripts: ["components/form.js", "components/textfield.js"]
    ); ?>
    <body class="centered small-spacing small single column layout">
        <a href="/" class="homepage-link"><h1 class="h3 color logo"></h1></a>

        <form action="/actions/login.php" method="post" class="form" empower>
            <?php 
            createTextField(
                name: "username", label: "Username", autocomplete: "username"
            );
            createTextField(
                name: "password", label: "Password", 
                type: "password", autocomplete: "current-password",
                toggleVisibility: true
            );
            createButton(text: "Login", submit: true);
            ?>
            <input type="hidden" name="referer" value="<?=$_SERVER["HTTP_REFERER"]?>">
        </form>

        <span>
            Don't have an account?
            <a href="/register/">Register</a>
        </span>
    </body>
</html>
