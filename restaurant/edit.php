<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');
    
    require_once('../database/models/user.php');
    require_once('../database/models/restaurant.php');
    
    require_once('../lib/params.php');

    session_start();

    list('id' => $id) = parseParams(get_params: [
        'id' => new IntParam(
            default: $_SESSION['user'], 
            optional: true
        ),
    ]);

    if (!isset($id)) {
        header("Location: /");
        die();
    }

    $user = User::get($id);

    if ($user === null) {
        http_response_code(404);
        require("../error.php");
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(metadata: baseMetadata()); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>
        <main>
            <form
                action="../actions/register.php"
                method="post"
                class="form sectioned"
                data-empower
            >
                <fieldset class="section" data-section>
                    <?php
                    createTextField(
                        name: "name", label: "Name", 
                        autocomplete: "restaurant name"
                    );
                    createTextField(
                        name: "address", label: "Address"
                    );
                    createButton(text: "Apply", submit: true);
                    ?>
                </fieldset>
                <input type="hidden" name="referer" value="<?=$_SERVER["HTTP_REFERER"]?>"> <!-- This is kept out of the fieldsets due to being hidden -->
            </form>
        </main>
    </body>
</html>
