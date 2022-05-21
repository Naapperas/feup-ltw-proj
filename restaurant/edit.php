<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');
    
    session_start();
    
    if (!isset($_SESSION['user'])) {
        header("Location: /profile/");
        die;
    }

    list('id' => $id) = parseParams(get_params: [
        'id' => new IntParam(
            optional: true
        ),
    ]);
    
    if (!isset($id)) {
        header("Location: /");
        die();
    }
    
    require_once('../database/models/user.php');
    require_once('../database/models/restaurant.php');
    
    $user = User::get($_SESSION['user']);

    if ($user === null) {
        header("Location: /profile/");
        die;
    }

    $restaurant = Restaurant::get($id);

    if($restaurant->owner !== $user) {
        header("Location: /restaurant?id=$id");
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
                action="../actions/edit_restaurant.php"
                method="post"
                class="form sectioned"
                data-empower
            >
                <fieldset class="section" data-section>
                    <?php
                    createTextField(
                        name: "name", label: "Name", 
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
