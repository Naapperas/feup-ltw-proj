<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');
    
    require_once('../database/models/user.php');
    
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
    <?php createHead(metadata: userMetadata($user)); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(); ?>

        <main class="centered medium medium-spacing single column layout">
            <header>
                <img
                    class="avatar big"
                    src="https://picsum.photos/240"
                    alt="<?= $user->name ?>'s profile picture"
                    width="240px"
                    height="240px"
                />
                <h2 class="h4"><?=$user->name?>'s profile</h2>
            </header>

            <section>
                <h3 class="h5">Personal information</h3>

                <p><span>Email: </span><span><?=$user->email?></span></p>
                <p><span>Full name: </span><span><?=$user->full_name?></span></p>
                <p><span>Phone number: </span><span><?=$user->phone_number?></span></p>
            </section>
        </main>

    </body>
</html>
