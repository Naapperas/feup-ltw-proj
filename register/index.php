<?php 
    declare(strict_types=1);

    require_once("../templates/common.php");
    require_once("../templates/form.php");
    require_once("../templates/metadata.php");

    require_once("../lib/session.php");

    $session = new Session();

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(title: "Register", description: "Register page for XauFome."),
        scripts: ["components/form.js", "components/textfield.js"]
    ); ?>
    <body class="small-spacing small column layout">
        <a href="/" class="homepage-link"><h1 class="h3 color logo"></h1></a>

        <?php createForm(
            'POST', 'register', '/actions/register.php', 'register-form',
            function() use ($session) { ?>
                <input type="hidden" name="referer" value="<?= $session->get('referer') ?? $_SERVER["HTTP_REFERER"]?>">
            <?php },
            function() {
                createTextField(
                    name: "email", label: "Email", 
                    type: "email", autocomplete: "email",
                    errors: ["type-mismatch" => "Error: invalid email address"]
                );
                createTextField(
                    name: "username", label: "Username", autocomplete: "username"
                );
                createTextField(
                    name: "password", label: "Password", 
                    type: "password", autocomplete: "current-password",
                    minlength: 8, toggleVisibility: true, characterCounter: true,
                    errors: ["too-short" => "Error: at least 8 characters"]
                );
                createButton(text: "Next", next: true);
            },
            function() {
                createTextField(
                    name: "name", label: "Full name", autocomplete: "name"
                );
                createTextField(
                    name: "address", label: "Address", autocomplete: "street-address"
                );
                createTextField(
                    name: "phone", label: "Phone number", 
                    type: "tel", autocomplete: "tel", pattern: "\\d{9}",
                    errors: ["pattern-mismatch" => "Error: invalid phone number"]
                );
                createButton(text: "Back", back: true, type: ButtonType::OUTLINED);
                createButton(text: "Register", submit: true);
            }
        ) ?>

        <div class="form-support">
            <span>
                Already have an account?
                <a class="link" href="/login/">Login</a>
            </span>
            <?php createColorSchemeToggle() ?>
        </div>
    </body>
</html>
