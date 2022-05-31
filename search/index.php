<?php 

    declare(strict_types=1);

    session_start();    

    require_once("../templates/components.php");
    require_once("../templates/metadata.php");

    require_once("../lib/params.php");

    $params = parseParams(get_params: ['q']);

    require_once("../database/models/user.php");
    require_once("../database/models/restaurant.php");
    require_once("../database/models/dish.php");
    require_once("../database/models/menu.php");
    require_once("../database/models/query.php");

    $users = User::getWithFilters([new Like('name', $params['q'])], limit: 10);
    $restaurants = Restaurant::getWithFilters([new Like('name', $params['q'])], limit: 10);
    $dishes = Dish::getWithFilters([new Like('name', $params['q'])], limit: 10);
    $menus = Menu::getWithFilters([new Like('name', $params['q'])], limit: 10);
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
            baseMetadata(description: "Search Page"),
            styles: ["/style/pages/search.css", "/style/components/slider.css"],
            scripts: ["components/dialog.js", "components/slider.js"]); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(value: $params['q']); ?>

        <main class="large medium-spacing column layout">
            <header class="header">
                <h2 class="title h3">Search results for '<?= $params['q'] ?>'</h2>
            </header>
            <?php 
            
            if ($users !== []) createSearchUserProfiles($users);
            if ($restaurants !== []) createSearchRestaurants($restaurants);
            if ($menus !== []) createSearchMenus($menus);
            if ($dishes !== []) createSearchDishes($dishes);
            ?>
        </main>
    </body>
</html>