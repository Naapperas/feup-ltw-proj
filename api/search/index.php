<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/session.php");
    require_once("../../lib/params.php");

    require_once("../../database/models/user.php");
    require_once("../../database/models/restaurant.php");
    require_once("../../database/models/dish.php");
    require_once("../../database/models/menu.php");
    require_once("../../database/models/category.php");
    require_once("../../database/models/query.php");

    APIRoute(
        get: function() {

            $params = parseParams(query: [
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
                ),
                'min_menu_price' => new FloatParam(
                    min: 0,
                    max: 50,
                    optional: true,
                ),
                'max_menu_price' => new FloatParam(
                    min: 0,
                    max: 500,
                    optional: true,
                )
            ]);
        
            $nameContainsSearchTermFilter = new Like('name', $params['q']);
        
            $restaurantScoreFilter = new AndClause([
                isset($params['min_restaurant_score']) ? new GreaterThanOrEqual('score', $params['min_restaurant_score']) : null,
                isset($params['max_restaurant_score']) ? new LessThanOrEqual('score', $params['max_restaurant_score']) : null
            ]);
        
            $dishPriceFilter = new AndClause([
                isset($params['min_dish_price']) ? new GreaterThanOrEqual('price', $params['min_dish_price']) : null,
                isset($params['max_dish_price']) ? new LessThanOrEqual('price', $params['max_dish_price']) : null
            ]);
            
            $menuPriceFilter = new AndClause([
                isset($params['min_menu_price']) ? new GreaterThanOrEqual('price', $params['min_menu_price']) : null,
                isset($params['max_menu_price']) ? new LessThanOrEqual('price', $params['max_menu_price']) : null
            ]);
        
            $categoryIds = array_map(fn (Category $category) => $category->id, Category::getWithFilters([$nameContainsSearchTermFilter]));
        
            $users = User::getWithFilters([$nameContainsSearchTermFilter], limit: 10);
        
            $restaurants = array_unique(array_merge(
                Restaurant::getWithFilters([$nameContainsSearchTermFilter, $restaurantScoreFilter], limit: 10),
                Restaurant::getByCategoryIds($categoryIds)
            ));
        
            $dishes = array_unique(array_merge(
                Dish::getWithFilters([$nameContainsSearchTermFilter, $dishPriceFilter], limit: 10),
                Dish::getByCategoryIds($categoryIds)
            ));
        
            $menus = Menu::getWithFilters([$nameContainsSearchTermFilter, $menuPriceFilter], limit: 10);

            return [
                'users' => $users,
                'restaurants' => $restaurants,
                'dishes' => $dishes,
                'menus' => $menus
            ];
        }
    );
?>
