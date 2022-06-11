<?php
declare(strict_types=1);

require_once(__DIR__."/item.php");
require_once(dirname(__DIR__)."/database/models/restaurant.php");
require_once(dirname(__DIR__)."/database/models/dish.php");
require_once(dirname(__DIR__)."/database/models/menu.php");
?>

<?php function createRestaurantList(
    ?array $restaurants, string $h = 'h3', string $vh = 'h4',
    string $title = 'Restaurants'
) { 
    if (!$restaurants) return;
    ?>
    <section class="restaurant-list">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
        </header>

        <?php 
        foreach($restaurants as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createDishList(
    ?array $dishes, string $h = 'h3', string $vh = 'h4',
    string $title = 'Dishes', bool $show_restaurant = false, bool $edit = false
) { 
    if (!$dishes && !$edit) return;
    ?>
    <section class="dish-list">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
        </header>

        <?php 
        foreach($dishes as $dish) {
            createDishCard($dish, $show_restaurant, $edit);
        }

        if ($edit) { ?>
            <article class="card">
                <?php createButton(
                    type: ButtonType::ICON,
                    text: "New dish",
                    icon: "add",
                    class: "card-link",
                    attributes: "data-new-dish-button"
                ) ?>
            </article>
        <?php } ?>
    </section>
<?php } ?>

<?php function createMenuDishList(array $dishes, bool $edit = false, string $dialog = 'dishes') {
    if ($edit) { ?>
        <a class="fullwidth" href="#" data-open-dialog="#<?= $dialog ?>">
            <p>Dishes</p>
            <ul>
                <?php foreach($dishes as $dish) { ?>
                    <li data-dish-id="<?= $dish->id ?>">
                        <?= $dish->name ?>
                    </li>
                <?php } ?>
            </ul>
        </a>
        <?php } else if ($dishes) { ?>
            <ul>
                <?php foreach($dishes as $dish) { ?>
                    <li data-dish-id="<?= $dish->id ?>">
                        <?= $dish->name ?>
                    </li>
                <?php } ?>
            </ul>
        <?php }
} ?> 

<?php function createMenuList(
    ?array $menus, string $h = 'h3', string $vh = 'h4',
    string $title = 'Menus', bool $show_restaurant = false, bool $edit = false
) { 
    if (!$menus && !$edit) return;
    ?>
    <section class="menu-list">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
        </header>

        <?php 
        foreach($menus as $menu) {
            createMenuCard($menu, $show_restaurant, $edit);
        }

        if ($edit) { ?>
            <article class="card">
                <?php createButton(
                    type: ButtonType::ICON,
                    text: "New menu",
                    icon: "add",
                    class: "card-link",
                    attributes: "data-new-menu-button"
                ) ?>
            </article>
        <?php } ?>
    </section>
<?php } ?>

<?php function createUserList(
    ?array $users, string $h = 'h3', string $vh = 'h4',
    string $title = 'Users'
) { 
    if (!$users) return;
    ?>
    <section class="user-list">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
        </header>

        <?php 
        foreach($users as $user) {
            createProfileCard($user);
        }
        ?>
    </section>
<?php } ?>

<?php function createReviewList(
    ?array $reviews, Restaurant $restaurant, string $h = 'h3', string $vh = 'h4',
    string $title = 'Reviews'
) {
    if (!$reviews || $restaurant === null) return;
    ?>
    <section class="restaurant-reviews" data-restaurant-id="<?= $restaurant->id ?>">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
            <div class="select subtitle">
                <select name="options" id="options"> <!-- TODO: STYLES!!!! -->
                    <option value="score-desc">Score - Desc</option>
                    <option value="score-asc">Score - Asc</option>
                    <option value="date-desc">Date - Desc</option>
                    <option value="date-asc">Date - Asc</option>
                </select>
                <label for="options">Sort by:</label>
            </div>
        </header>
        <div id="review-list">
        <?php foreach($reviews as $review) {
            showReview($review);
        } ?>
        </div>
        <!-- TODO: maybe have different outputs when there already is a response ? -->
        <dialog class="dialog confirmation" id="review-response" <?php if($_SESSION['user'] === $restaurant->owner) { echo "data-owner-logged-in"; } ?>>
            <header><h2 class="h4">Respond to review...</h2></header>
            <div class="content">
                <section id="response-review"> <!-- FIXME: needs better id -->
                </section>
                <section id="response-text"></section> <!-- TODO: STYLES!!!! -->
                <?php createForm(
                    'POST', 
                    'review-response-form', 
                    '/actions/create_review_response.php', 
                    'review-response-form',
                    function() use ($restaurant) { 
                        createTextField(name: 'reviewResponse', label: 'Write a response...', type: 'multiline');
                    ?>
                    <input type="hidden" name="reviewId"></input>
                    <input type="hidden" name="restaurantId" value="<?= $restaurant->id ?>"></input>
                <?php }); ?>
            </div>
            <div class="actions">
                <?php createButton(type: ButtonType::TEXT, text: 'Cancel', attributes: 'data-close-dialog="#review-response"') ?>
                <?php createButton(type: ButtonType::TEXT, text: 'Post', submit: true, attributes: 'form="review-response-form"') ?>
            </div>
        </dialog>
    </section>
<?php } ?>

<?php function createOrderList(
    ?array $orders, string $h = 'h3', string $vh = 'h4',
    string $title = 'Orders', bool $show_restaurant = true
) {
    if (!$orders) return;
    ?>
    <section class="restaurant-orders">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
        </header>
        <div id="review-list">
        <?php foreach($orders as $order) {
            showOrder($order, $show_restaurant);
        } ?>
        </div>
    </section>
<?php } ?>

<?php function createCategoryList(?array $categories, bool $edit = false, string $dialog = 'categories') {
    if ($edit) { ?>
    <a class="fullwidth chip-list-edit" href="#" data-open-dialog="#<?= $dialog ?>">
        <p>Categories</p>
        <ul class="chip-list wrap">
            <?php foreach($categories as $category) { ?>
                <li class="chip" data-category-id="<?= $category->id ?>">
                    <?= $category->name ?>
                </li>
            <?php } ?>
        </ul>
    </a>
    <?php } else if ($categories) { ?>
        <ul class="chip-list wrap">
            <?php foreach($categories as $category) { ?>
                <li data-category-id="<?= $category->id ?>">
                    <a class="chip" href="/search/?q=<?= rawurlencode($category->name) ?>">
                        <?= $category->name ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php }
} ?>

<?php function createCartList(array $cart) {

    $dishes = Dish::getById(array_keys($cart['dishes'] ?? []));
    $menus = Menu::getById(array_keys($cart['menus'] ?? []));

    $orders_by_restaurant = [];
    foreach($dishes as $dish) {
        $orders_by_restaurant[$dish->restaurant]['dishes'][] = $dish ;
    }
    foreach($menus as $menu) {
        $orders_by_restaurant[$menu->restaurant]['menus'][] = $menu ;
    }

    foreach($orders_by_restaurant as $restaurantId => $orders) {

        $restaurant = Restaurant::getById($restaurantId);
        if ($restaurant === null) continue;

        list('dishes' => $order_dishes, 'menus' => $order_menus) = $orders;

        $total = array_reduce($order_dishes ?? [], fn($d1, $d2) => $d1 + $d2->price * $cart['dishes'][$d2->id], 0)
               + array_reduce($order_menus ?? [], fn($m1, $m2) => $m1 + $m2->price * $cart['menus'][$m2->id], 0);

        createForm(
            "POST",
            "place_order_for_{$restaurant->id}",
            "/actions/place_order.php",
            "place_order_restaurant_{$restaurant->id}",
            function() use ($restaurant, $cart, $order_dishes, $order_menus, $total) { ?>
                <header class="header">
                    <a href="/restaurant/?id=<?= $restaurant->id ?>"><h2 class="title h4">
                        <?= $restaurant->name ?>
                    </h2></a>
                </header>
                <section class="product-list">
                    <?php
                        foreach ($order_dishes as $dish) {
                            createCartDishCard($dish, $cart['dishes'][$dish->id]);
                        }
                        foreach ($order_menus as $menu) {
                            createCartMenuCard($menu, $cart['menus'][$menu->id]);
                        }
                    ?>
                </section>
                <section class="info">
                    <span>
                        Total &CenterDot;
                        <span class="cart-total"><?= sprintf('%.2f', $total) ?></span>€
                    </span>
                    <?php 
                    createButton(
                        type: ButtonType::CONTAINED,
                        text: 'Order',
                        icon: "shopping_bag",
                        submit: true
                    );
                    ?>
                </section>
                <input type="hidden" name="restaurantId" value="<?= $restaurant->id ?>">
            <?php }
        );
    }?>
<?php } ?>