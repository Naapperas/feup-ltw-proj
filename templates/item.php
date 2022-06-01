<?php 
declare(strict_types=1); 

require_once(dirname(__DIR__)."/database/models/restaurant.php");
require_once(dirname(__DIR__)."/database/models/dish.php");
require_once(dirname(__DIR__)."/database/models/menu.php");
require_once(dirname(__DIR__)."/database/models/user.php");
require_once(dirname(__DIR__)."/database/models/review.php");
?>

<?php function createRestaurantCard(Restaurant $restaurant) { ?>
    <article
        class="card responsive interactive"
        data-card-type="restaurant"
        data-restaurant-id="<?= $restaurant->id ?>"
    >
        <img
            src="<?= $restaurant->getImagePath() ?>"
            width="1920"
            height="1080"
            alt="Profile picture for <?= $restaurant->name ?>"
            class="full media thumbnail"
        />
        <header class="header">
            <h3 class="title h6">
                <a
                    href="/restaurant/?id=<?= $restaurant->id ?>"
                    class="card-link"
                >
                    <?= $restaurant->name ?>
                </a>
            </h3>
            <span class="subtitle subtitle2 secondary"><?= $restaurant->address ?></span>
            <?php if (($avgScore = $restaurant->getReviewScore()) !== null) { ?>
            <span class="chip right"><?php createIcon(icon: "star") ?><?= round($avgScore, 1) ?></span>
            <?php } ?>
        </header>
        <?php
        if (($categories = $restaurant->getCategories()) !== []) {
            createCategoryList($categories);
        }

        if (isset($_SESSION['user'])) {
            if ($restaurant->owner === $_SESSION['user']) {
                createButton(
                    type: ButtonType::ICON,
                    text: "Edit",
                    icon: "edit",
                    class: "top-right",
                    href: "/restaurant/edit.php?id=$restaurant->id");
            } else {
                $currentUser = User::getById($_SESSION['user']);

                if ($currentUser !== null && $restaurant->isLikedBy($currentUser)) {
                    $state = "on";
                    $text = "Unfavorite";
                } else {
                    $state = "off";
                    $text = "Favorite";
                }
    
                createButton(
                    type: ButtonType::ICON, text: $text, class: "top-right toggle",
                    attributes: 
                        "data-on-icon=\"favorite\"\n".
                        "data-off-icon=\"favorite_border\"\n".
                        "data-toggle-state=\"$state\"\n".
                        "data-favorite-button"
                );
            }
        } ?>
    </article>
<?php } ?>

<?php function createDishCard(Dish $dish, bool $show_restaurant = false, bool $edit = false) { 
    if ($show_restaurant && ($restaurant = $dish->getRestaurant()) === null) return;

    if ($edit) { ?>
        <article
            class="card responsive"
            data-card-type="edit-dish"
            data-dish-id="<?= $dish->id ?>"
        >
            <label class="image-input full media thumbnail">
                <img
                    class="thumbnail"
                    src="<?= $dish->getImagePath() ?>"
                    alt=""
                >
                <input
                    class="visually-hidden"
                    type="file"
                    name="dishes_to_edit[<?= $dish->id ?>]"
                    accept="image/*"
                >
            </label>

            <?php
            createTextField(
                name: "dishes_to_edit[$dish->id][name]",
                label: 'Name',
                value: $dish->name
            );
            createTextField(
                name: "dishes_to_edit[$dish->id][price]",
                label: 'Price',
                value: sprintf('%.2f', $dish->price),
                type: 'number',
                min: 0,
                step: .01
            );
            
            if (($categories = $dish->getCategories()) !== []) {
                echo '<hr class="divider" />';
                // createDishCategories($categories, 'h4');
            }
    
            createButton(
                type: ButtonType::ICON,
                text: 'Delete',
                icon: 'delete',
                class: "top-right",
                attributes: "data-delete-button"
            );
            ?>

            <input
                type="hidden"
                disabled
                name="dishes_to_delete[]"
                value="<?= $dish->id ?>"
            >
        </article>
    <?php } else { ?>
        <article
            class="card responsive interactive"
            data-card-type="dish"
            data-dish-id="<?= $dish->id ?>"
        >
            <img
                src="<?= $dish->getImagePath() ?>"
                width="1920"
                height="1080"
                alt="Dish picture for <?= $dish->name ?>"
                class="full media thumbnail"
            />
            <header class="header">
                <h3 class="title h6">
                    <?= $dish->name ?>
                </h3>
                <span class="subtitle subtitle2 secondary">
                    <!-- XXX: maybe add the price even when out of the restaurant page -->
                    <?= $show_restaurant ? $restaurant->name : sprintf('%.2f€', $dish->price) ?>
                </span>
            </header>
            <?php
            if (($categories = $dish->getCategories()) !== []) {
                echo '<hr class="divider" />';
                // createDishCategories($categories, 'h4');
            }
            
            session_start();

            if (isset($_SESSION['user'])) {

                $currentUser = User::getById($_SESSION['user']);

                if ($currentUser !== null && $dish->isLikedBy($currentUser)) {
                    $state = "on";
                    $text = "Unfavorite";
                } else {
                    $state = "off";
                    $text = "Favorite";
                }

                createButton(
                    type: ButtonType::ICON, text: $text, class: "top-right toggle",
                    attributes: 
                        "data-on-icon=\"favorite\"\n".
                        "data-off-icon=\"favorite_border\"\n".
                        "data-toggle-state=\"$state\"\n".
                        "data-dish-id=\"$dish->id\"\n".
                        "data-favorite-button"
                );
            } ?>
        </article>
<?php } } ?>

<?php function createMenuCard(Menu $menu, bool $show_restaurant = false, bool $edit = false) { 
    if ($show_restaurant && ($restaurant = $menu->getRestaurant()) === null) return;

    if ($edit) { ?>
        <article
            class="card responsive"
            data-card-type="edit-menu"
            data-menu-id="<?= $menu->id ?>"
        >
            <label class="image-input full media thumbnail">
                <img
                    class="thumbnail"
                    src="<?= $menu->getImagePath() ?>"
                    alt=""
                >
                <input
                    class="visually-hidden"
                    type="file"
                    name="menus_to_edit[<?= $menu->id ?>]"
                    accept="image/*"
                >
            </label>

            <?php
            createTextField(
                name: "menus_to_edit[$menu->id][name]",
                label: 'Name',
                value: $menu->name
            );
            createTextField(
                name: "menus_to_edit[$menu->id][price]",
                label: 'Price',
                value: sprintf('%.2f', $menu->price),
                type: 'number',
                min: 0,
                step: .01
            );
    
            createButton(
                type: ButtonType::ICON,
                text: 'Delete',
                icon: 'delete',
                class: "top-right",
                attributes: "data-delete-button"
            );
            ?>

            <input
                type="hidden"
                disabled
                name="menus_to_delete[]"
                value="<?= $menu->id ?>"
            >
        </article>
    <?php } else { ?>
    <article 
        class="card responsive interactive"
        data-card-type="menu" 
        data-menu-id="<?= $menu->id ?>"
    >
        <img
            src="https://picsum.photos/316/194"
            width="320"
            height="180"
            alt="Menu picture for <?= $menu->name ?>"
            class="full media"
        />
        <header class="header">
            <h3 class="title h6">
                <a 
                    href="/menu/?id=<?= $menu->id ?>"
                    class="card-link"
                >
                    <?= $menu->name ?>
                </a>
            </h3>
            <span class="subtitle subtitle2 secondary">
                <?= $show_restaurant ? $restaurant->name : sprintf('%.2f€', $menu->price) ?>
            </span>
        </header>
    </article>
<?php } } ?>

<?php function createProfileCard(User $user) { ?>
    <article 
        class="card responsive interactive"
        data-card-type="user" 
        data-user-id="<?= $user->id ?>"
    >
        <header class="header">
            <img
                src="<?= $user->getImagePath() ?>"
                width="512"
                height="512"
                alt="Profile picture for <?= $user->name ?>"
                class="avatar medium"
            />
            <h3 class="title h6">
                <a 
                    href="/profile/?id=<?= $user->id ?>"
                    class="card-link"
                >
                    <?= $user->name ?>
                </a>
            </h3>
            <span class="subtitle subtitle2 secondary">
                <?= $user->address ?>
            </span>
        </header>
    </article>
<?php } ?>

<?php function showReview(Review $review) { 
    $user = User::getById($review->client);

    if ($user == null) return;
    ?>
    <article class="review">
        <a href="/profile/?id=<?= $user->id ?>">
            <header class="header">
                <img 
                    src=<?= $user->getImagePath() ?>
                    alt="Review profile image for user <?=$user->id?>"
                    class="avatar small"
                >
                <span class="title"><?= $user->name ?></span>
                <span class="subtitle secondary"><?= $user->address ?></span>
                <span class="chip right"><?php createIcon("star"); ?><?= round($review->score, 1) ?></span>
            </header>
        </a>
        <p class="review-content"><?= $review->text ?></p>
    </article>
<?php } ?>