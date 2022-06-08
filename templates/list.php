<?php
declare(strict_types=1);

require_once(__DIR__."/item.php");
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
    ?array $reviews, int $restaurantId, string $h = 'h3', string $vh = 'h4',
    string $title = 'Reviews'
) {

    $restaurant = Restaurant::getById($restaurantId);

    if (!$reviews || $restaurant === null) return;
    ?>
    <section class="restaurant-reviews" data-restaurant-id="<?= $restaurantId ?>">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
            <div class="select right">
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
                    function() use ($restaurantId) { 
                        createTextField(name: 'reviewResponse', label: 'Write a response...', type: 'multiline');
                    ?>
                    <input type="hidden" name="reviewId"></input>
                    <input type="hidden" name="restaurantId" value="<?= $restaurantId ?>"></input>
                <?php }); ?>
            </div>
            <div class="actions">
                <?php createButton(type: ButtonType::TEXT, text: 'Cancel', attributes: 'data-close-dialog="#review-response"') ?>
                <?php createButton(type: ButtonType::TEXT, text: 'Post', submit: true, attributes: 'form="review-response-form"') ?>
            </div>
        </dialog>
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
                <li class="chip" data-category-id="<?= $category->id ?>">
                    <a href="/search/?q=<?= rawurlencode($category->name) ?>">
                        <?= $category->name ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php }
} ?>
