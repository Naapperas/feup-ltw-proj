<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');
    
    require_once('../database/models/user.php');
    
    session_start();
    
    if (!isset($_SESSION['user']) 
    || ($user = User::get($_SESSION['user'])) === null) {
        header("Location: /profile/");
        die;
    }

?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(metadata: baseMetadata()); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>
        <main class="centered small-spacing small single column layout">
            <form
                action="../actions/edit_profile.php"
                method="post"
                class="form"
                enctype="multipart/form-data"
                data-empower
            >
                <input type="file" name="profile_picture" accept="image/*">
                <?php
                createTextField(
                    name: "username", label: "Username", autocomplete: "username", value: $user->name
                );
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
                ?>
                <?php createButton(text: "Edit", submit: true); ?>
                <!-- This is kept out of the fieldset due to being hidden -->
                <input type="hidden" name="id" value="<?=$user->id?>">
            </form>
        </main>
    </body>
</html>