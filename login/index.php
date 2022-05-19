<?php 
declare(strict_types=1);

require_once("../templates/components.php");
require_once("../templates/auth.php");
require_once("../templates/metadata.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(title: "Login", description: "Login page for XauFome."),
        scripts: ["components/form.js", "components/textfield.js"]
    ); ?>
    <body class="centered small-spacing small single column layout">
        <a href="/" class="homepage-link"><h1 class="h3 color logo"></h1></a>

        <form 
            action="/actions/login.php"
            method="post"
            class="form"
            data-empower
        >
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
            processAuthErrors();
            ?>
            <input type="hidden" name="referer" value="<?=$_SERVER["HTTP_REFERER"]?>">
        </form>

        <div class="form-support">
            <span>
                Don't have an account?
                <a class="link" href="/register/">Register</a>
            </span>
            <?php createColorSchemeToggle() ?>
        </div>
    </body>
</html>
