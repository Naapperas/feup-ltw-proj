<?php 

    declare(strict_types=1);

    require_once("../templates/components.php");
    require_once("../templates/metadata.php");

    require_once("../lib/params.php");

    $params = parseParams(get_params: ['q']);

    require_once("../database/models/user.php");
    require_once("../database/models/restaurant.php");
    require_once("../database/models/dish.php");
    require_once("../database/models/menu.php");

    $queryData = ['name' => $params['q']];

    $users = User::get($queryData, 10, false);
    $restaurants = Restaurant::get($queryData, 10, false);
    $dishes = Dish::get($queryData, 10, false);
    $menus = Menu::get($queryData, 10, false);
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
            baseMetadata(description: "Search Page"),
            styles: ["/style/pages/search.css", "/style/components/slider.css"],
            scripts: ["components/dialog.js", "components/slider.js", "pages/search.js"]); ?>
    <body class="top-app-bar layout">
        <?php createAppBar(value: $params['q']); ?>

        <main class="large medium-spacing column layout">
            <header class="header">
                <h2 class="title h3">Search results for '<?= $params['q'] ?>'</h2>
                <?php createButton(
                    ButtonType::ICON, 
                    text: "Select filters", 
                    icon: "sort",
                    class: "right",
                    href: "#",
                    attributes: "data-open-dialog=\"#filters\""
                ); ?>
            </header>
            <dialog class="dialog confirmation" id="filters">
                <header><h2 class="h4">Filters</h2></header>
                <div class="content">
                    <section>
                        <h2 class="title h5">Restaurants</h2>
                        <div class="slider">
                            <label for="min_score_slider">
                                Minimum Score: <span id="score">2.5<!-- this is the default value --></span>
                            </label>
                            <input
                                class="slider"
                                type="range"
                                name="min_score"
                                id="min_score_slider"
                                min="0"
                                max="50"
                                value="25"
                                step="1"
                            /> <!-- the javascript to handle the slider svg does not like fractions -->
                        </div>
                    </section>
                    <section>
                        <h2 class="title h5">Dishes</h2>
                        <div class="slider">
                            <label for="min_price_slider">
                                Minimum price: <span id="price">N/A<!-- this is the default value --></span>
                            </label>
                            <input
                                class="slider"
                                type="range"
                                name="min_price"
                                id="min_price_slider"
                                min="0"
                                max="20"
                                value="10"
                            />
                        </div>
                    </section>
                </div>
                <div class="actions">
                    <button class="button text" type="button" data-close-dialog="#filters">Done</button>
                </div>
            </dialog>
            <?php 
            
            if ($users !== []) createSearchUserProfiles($users);
            if ($restaurants !== []) createSearchRestaurants($restaurants);
            if ($menus !== []) createSearchMenus($menus);
            if ($dishes !== []) createSearchDishes($dishes);
            ?>
        </main>
    </body>
</html>