<?php 
declare(strict_types=1);

require_once("../templates/common.php");
require_once("../templates/form.php");
require_once("../templates/metadata.php");

session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(title: "Login", description: "Login page for XauFome."),
        scripts: ["components/form.js", "components/textfield.js"]
    ); ?>
    <body class="small-spacing small column layout">
        <a href="/" class="homepage-link"><h1 class="h3 color logo"></h1></a>

        <?php createForm(
            'POST', 'login', '/actions/login.php', 'login-form',
            function() {
                createTextField(
                    name: "username", label: "Username", autocomplete: "username"
                );
                createTextField(
                    name: "password", label: "Password", 
                    type: "password", autocomplete: "current-password",
                    toggleVisibility: true
                );
                createButton(text: "Login", submit: true); ?>
                <input type="hidden" name="referer" value="<?=$_SESSION['referer'] ?? $_SERVER["HTTP_REFERER"]?>"><?php
            }
        ) ?>

        <div class="form-support">
            <span>
                Don't have an account?
                <a class="link" href="/register/">Register</a>
            </span>
            <?php createColorSchemeToggle() ?>
        </div>
    </body>
</html>
