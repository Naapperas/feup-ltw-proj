<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');
    
    require_once('../lib/params.php');
    
    session_start();

    list('id' => $id) = parseParams(get_params: [
        'id' => new IntParam(
            default: $_SESSION['user'], 
            optional: true
        ),
    ]);
    
    if (!isset($_SESSION['user'])) {
        header("Location: /profile/");
        die;
    }
    
    require_once('../database/models/user.php');
    
    $user = User::get($_SESSION['user']);

    if ($user === null) {
        header("Location: /profile/");
        die;
    }

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(metadata: baseMetadata()); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>
        <main>
            <form
                action="../actions/edit_profile.php"
                method="post"
                class="form sectioned"
                data-empower
            >
                <fieldset class="section" data-section>
                    <?php
                    createTextField(
                        name: "email", label: "Email", 
                        type: "email", autocomplete: "email",
                        errors: ["type-mismatch" => "Error: invalid email address"],
                        value: $user->email
                    );
                    createTextField(
                        name: "name", label: "Full name", autocomplete: "name", value: $user->full_name
                    );
                    createTextField(
                        name: "address", label: "Address", autocomplete: "street-address", value: $user->address
                    );
                    createTextField(
                        name: "phone", label: "Phone number", 
                        type: "tel", autocomplete: "tel", pattern: "\\d{9}",
                        errors: ["pattern-mismatch" => "Error: invalid phone number"],
                        value: $user->phone_number
                    );
                    createButton(text: "Edit", submit: true);
                    ?>
                </fieldset>
                <input type="hidden" name="id" value="<?=$user->id?>"> <!-- This is kept out of the fieldset due to being hidden -->
            </form>
        </main>
    </body>
</html>