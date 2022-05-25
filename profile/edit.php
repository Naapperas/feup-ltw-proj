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
        <main class="centered small single column layout">
            <?php createForm(
                'POST', 'profile', '/actions/edit_profile.php',
                function() use ($user) {
                    ?><input type="file" name="profile_picture" accept="image/*"><?php
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
                    createButton(text: "Edit", submit: true); ?>
                    ?><input type="hidden" name="id" value="<?=$user->id?>"><?php
                }
            ) ?>
        </main>
    </body>
</html>