<?php 
declare(strict_types=1);

require_once("../templates/components.php");
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        title: "Register", description: "Register page for XauFome.", 
        scripts: ["components/form.js", "components/textfield.js"]
    ); ?>
    <body class="centered small-spacing small single column layout">
        <a href="/mainpage/" class="homepage-link"><h1 class="h3 color logo"></h1></a>

        <form action="../actions/register.php" method="post" class="form sectioned" empower>
            <fieldset class="section" section>
                <?php
                createTextField(
                    name: "email", label: "Email", 
                    type: "email", autocomplete: "email",
                    errors: ["typeMismatch" => "Error: invalid email address"]
                );
                createTextField(
                    name: "username", label: "Username", autocomplete: "username"
                );
                createTextField(
                    name: "password", label: "Password", 
                    type: "password", autocomplete: "current-password",
                    minlength: 8, toggleVisibility: true, characterCounter: true,
                    errors: ["tooShort" => "Error: at least 8 characters"]
                );
                createButton(text: "Next", next: true);
                ?>
            </fieldset>
            
            <fieldset class="section" section>
                <?php
                createTextField(
                    name: "fname", label: "First name", autocomplete: "fname"
                );
                createTextField(
                    name: "lname", label: "Last name", autocomplete: "lname"
                );
                createTextField(
                    name: "address", label: "Address", autocomplete: "street-address"
                );
                createTextField(
                    name: "phone", label: "Phone number", 
                    type: "tel", autocomplete: "tel", pattern: "\\d{9}",
                    errors: ["patternMismatch" => "Error: invalid phone number"]
                );
                createButton(text: "Back", back: true, type: ButtonType::OUTLINED);
                createButton(text: "Register", submit: true) 
                ?>
            </fieldset>
            <input type="hidden" name="referer" value="<?=$_SERVER["HTTP_REFERER"]?>"> <!-- This is kept out of the fieldsets due to being hidden -->
        </form>

        <span>
            Already have an account?
            <a href="../login/">Login</a>
        </span>
    </body>
</html>
