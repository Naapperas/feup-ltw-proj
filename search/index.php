<?php 

    declare(strict_types=1);

    session_start();    

    require_once("../templates/components.php");
    require_once("../templates/metadata.php");

    require_once("../lib/params.php");
    require_once("../database/models/query.php");

    $params = parseParams(get_params: [
        'q',
        'min_restaurant_score' => new FloatParam(
            min: 0,
            max: 50,
            optional: true,
        ),
        'max_restaurant_score' => new FloatParam(
            min: 0,
            max: 50,
            optional: true
        ),
        'min_dish_price' => new FloatParam(
            min: 0,
            max: 50,
            optional: true,
        ),
        'max_dish_price' => new FloatParam(
            min: 0,
            max: 50,
            optional: true,
        )
    ]);

    $nameContainsSearchTermFilter = new Like('name', $params['q']);

    $restaurantScoreFilter = new AndClause([ /* FIXME: this does not work yet because it requires that we store the score on the restaurant model */
        isset($params['min_restaurant_score']) ? new GreaterThanOrEqual('score', ($params['min_restaurant_score'] / 10)) : null,
        isset($params['max_restaurant_score']) ? new LessThanOrEqual('score', ($params['max_restaurant_score'] / 10)) : null
    ]);

    $dishPriceFilter = new AndClause([
        isset($params['min_dish_price']) ? new GreaterThanOrEqual('price', $params['min_dish_price']) : null,
        isset($params['max_dish_price']) ? new LessThanOrEqual('price', $params['max_dish_price']) : null
    ]);

    require_once("../database/models/user.php");
    require_once("../database/models/restaurant.php");
    require_once("../database/models/dish.php");
    require_once("../database/models/menu.php");
    require_once("../database/models/category.php");
    require_once("../database/models/query.php");

    $categoryIds = array_map(fn (Category $category) => $category->id, Category::getWithFilters([$nameContainsSearchTermFilter]));

    $users = User::getWithFilters([$nameContainsSearchTermFilter], limit: 10);

    $restaurants = array_unique(array_merge(
        Restaurant::getWithFilters([$nameContainsSearchTermFilter], limit: 10),
        Restaurant::getForCategoryIds($categoryIds)
    ));

    $dishes = array_unique(array_merge(
        Dish::getWithFilters([$nameContainsSearchTermFilter, $dishPriceFilter], limit: 10),
        Dish::getForCategoryIds($categoryIds)
    ));

    $menus = Menu::getWithFilters([$nameContainsSearchTermFilter], limit: 10);
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
            baseMetadata(description: "Search Page"),
            styles: ["/style/pages/search.css", "/style/components/slider.css"],
            scripts: ["components/dialog.js", "components/slider.js", "components/card.js"]); ?>
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