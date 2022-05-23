<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');

    require_once('../lib/params.php');
    
    require_once('../database/models/user.php');
    require_once('../database/models/restaurant.php');
    
    session_start();

    list('id' => $id) = parseParams(get_params: [
        'id' => new IntParam(
            optional: true
        ),
    ]);
    
    if (!isset($id) || ($restaurant = Restaurant::get($id)) === null) {
        header("Location: /");
        die();
    }
    
    if ($restaurant->owner !== $_SESSION['user']) {
        header("Location: /restaurant/?id=$id");
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
                action="../actions/edit_restaurant.php"
                method="post"
                class="form"
                data-empower
            >
                <?php
                createTextField(
                    name: "name", label: "Name", 
                );
                createTextField(
                    name: "address", label: "Address"
                );
                createButton(text: "Apply", submit: true);
                ?>
                <input type="hidden" name="referer" value="<?=$_SERVER["HTTP_REFERER"]?>"> <!-- This is kept out of the fieldsets due to being hidden -->
            </form>
        </main>
    </body>
</html>
