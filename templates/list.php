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
    ?array $reviews, string $h = 'h3', string $vh = 'h4',
    string $title = 'Reviews'
) { 
    if (!$reviews) return;
    ?>
    <section class="restaurant-reviews">
        <header class="header">
            <<?= $h ?> class="title <?= $vh ?>"><?= $title ?></<?= $h ?>>
            <!-- to be dealt with in JavaScript + AJAX -->
            <!-- <div class="select right">
                <select name="options" id="options"> 
                    <option value="score-asc">Score - Ascending</option>
                    <option value="score-desc">Score - Descending</option>
                    <option value="date-asc">Date - Ascending</option>
                    <option value="date-desc">Date - Descending</option>
                </select>
                <label for="options">Sort</label>
            </div> -->
        </header>
        <?php foreach($reviews as $review) {
            showReview($review);
        } ?>
    </section>
<?php } ?>

<?php function createCategoryList(?array $categories, bool $edit = false) {
    if ($edit) { ?>
    <a class="fullwidth chip-list-edit" href="#" data-open-dialog="#categories">
        <p>Categories</p>
    <?php } ?>
        <?php if ($categories || $edit) { ?>
        <ul class="chip-list wrap">
            <?php foreach($categories as $category) { ?>
                <li class="chip" data-category-id="<?= $category->id ?>">
                    <a href="/search/?q=<?= rawurlencode($category->name) ?>">
                        <?= $category->name ?>
                    </a>
                </li>
            <?php } ?>

        </ul>
        <?php } ?>
    <?php if ($edit) { ?>
    </a>
    <?php }
} ?>
