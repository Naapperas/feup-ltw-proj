<?php
declare(strict_types=1);

enum ButtonType: string {
    case CONTAINED = "contained";
    case OUTLINED = "outlined";
    case TEXT = "text";
    case ICON = "icon";
    case FAB = "icon fab";
    case MINI_FAB = "icon fab mini";
}

require_once(dirname(__DIR__)."/database/models/user.php");
require_once(dirname(__DIR__)."/database/models/restaurant.php");
require_once(dirname(__DIR__)."/database/models/dish.php");
require_once(dirname(__DIR__)."/database/models/menu.php");
require_once(dirname(__DIR__)."/database/models/review.php");

?>

<?php function createHead(
    callable $metadata,
    array $styles = [], array $scripts = []
) { ?>
    <head>
        <?php $metadata() ?>
        
        <script src="/scripts/colorscheme.js"></script>

        <link rel="stylesheet" href="/style/index.css" />

        <?php foreach ($styles as $style) { ?>
            <link rel="stylesheet" href="<?= $style ?>" />
        <?php } ?>

        <?php foreach ($scripts as $script) { ?>
            <script src="/scripts/<?= $script ?>" defer type="module"></script>
        <?php } ?>
    </head>
<?php } ?>

<?php function createButton(
    ButtonType $type = ButtonType::CONTAINED, string $text = "",
    string $icon = "", string $class = "", string $href = "",
    bool $submit = false, bool $next = false, bool $back = false,
    string $attributes = ""
) {
    $component = $href === '' ? 'button' : 'a';
    $is_icon = $type === ButtonType::ICON || $type === ButtonType::FAB || $type === ButtonType::MINI_FAB;
    ?>

    <<?= $component ?>
        class="button <?= $type->value ?> <?= $class ?>"
        <?= $attributes ?>
        <?php if ($component === "button") { ?>
            type="<?= $submit ? "submit" : "button" ?>"
            <?php 
            if ($next && !$back)
                echo "data-next ";
            if ($back && !$next)
                echo "data-back ";
            ?>
        <?php } elseif ($component === "a") { ?>
            href="<?= $href ?>"
        <?php } ?>
        <?php if ($is_icon && $text) { ?>
            aria-label="<?= $text ?>"
        <?php } ?>
    >
        <?php if ($is_icon) { ?>
            <?= $icon ?>
        <?php } else { ?>
            <?php if ($icon) createIcon($icon) ?>
            <?= $text ?>
        <?php } ?>
    </<?= $component ?>>
<?php } ?>

<?php function createIcon(string $icon, string $component = "span") { ?>
    <<?= $component ?> class="icon"><?= $icon ?></<?= $component ?>>
<?php } ?>

<?php function createTextField(
    string $name, string $label, string $helperText = "",
    string $type = "text", string $autocomplete = "", string $pattern = "",
    string $class = "",
    int $maxlength = -1, int $minlength = -1,
    ?float $min = null, ?float $max = null, ?float $step = null,
    bool $optional = false,  bool $toggleVisibility = false,
    bool $characterCounter = false, bool $errorText = true,
    array $errors = [], string $value = ""
) { 
    $describedby = [];
    if ($helperText !== "") $describedby[] = "$name-helper-text";
    if ($errors !== [] || $errorText) $describedby[] = "$name-error-text";
    $describedby = implode(" ", $describedby);
    ?>
    <div class="textfield <?= $class ?>">
        <<?php if ($type === 'multiline') { ?>textarea<?php } else { ?>input
            type="<?= $type ?>" <?php } ?>
            placeholder=" "
            id="<?= $name ?>"
            name="<?= $name ?>"
            <?php if ($autocomplete !== "") { ?>
            autocomplete="<?= $autocomplete ?>"
            <?php } ?>
            <?php if ($pattern !== "") { ?>
            pattern="<?= $pattern ?>"
            <?php } ?>
            <?php if ($minlength !== -1) { ?>
            minlength="<?= $minlength ?>"
            <?php } ?>
            <?php if ($maxlength !== -1) { ?>
            maxlength="<?= $maxlength ?>"
            <?php } ?>
            <?php if ($value !== "") { ?>
            value="<?= $value ?>"    
            <?php } ?> 
            <?php if ($min !== null) { ?>
            min="<?= $min ?>"    
            <?php } ?> 
            <?php if ($max !== null) { ?>
            max="<?= $max ?>"    
            <?php } ?> 
            <?php if ($step !== null) { ?>
            step="<?= $step ?>"    
            <?php } ?> 
            <?php 
            if (!$optional)
                echo "required\n";

            if ($describedby !== "") 
                echo "aria-describedby=\"$describedby\"\n";
                
            if ($errors !== [] || $errorText) 
                echo "data-error-text=\"$name-error-text\"\n";
            ?>
        /><?php if ($type === 'multiline') { ?></textarea><?php } ?>
        <label for="<?= $name ?>"><?= $label ?></label>
        <?php if ($toggleVisibility) {
            createButton(
                type: ButtonType::ICON,
                text: "Toggle $name visibility",
                class: "toggle-visible"
            ); 
        } ?>
        <?php if ($helperText !== "") { ?>
        <span 
            class="error-text" 
            id="<?= $name ?>-helper-text"
        ><?= $helperText ?></span>
        <?php } ?>
        <?php if ($errors !== [] || $errorText) { ?>
        <span 
            class="error-text"
            aria-live="assertive"
            id="<?= $name ?>-error-text"
            <?php 
            foreach ($errors as $key => $value)
                echo "data-$key=\"$value\""
            ?>
        ></span>
        <?php } ?>
        <?php if ($characterCounter) { ?>
        <span class="character-counter" aria-hidden="true"></span>
        <?php } ?>
    </div>
<?php } ?>

<?php function createForm(
    string $method, string $name, string $action,
    callable ...$sections
) { ?>
    <form
        action="<?= $action ?>"
        method="<?= $method ?>"
        enctype="multipart/form-data"
        class="form<?php if (count($sections) > 1) echo ' sectioned' ?>"
        data-empower
    >
        <?php foreach (array_slice($sections, 1) as $section) { ?>
        <fieldset class="section" data-section>
            <?php $section() ?>
        </fieldset>
        <?php }

        $sections[0]();
        
        session_start();

        $error = $_SESSION["$name-error"];
        unset($_SESSION["$name-error"]);

        if (isset($error)) { ?>
        <span class="form-error"><?= $error ?></span>
        <?php } ?>
    </form>
<?php } ?>

<?php function createUserButtons() {
    session_start();

    if (isset($_SESSION['user'])) {    
        createButton(
            type: ButtonType::ICON,
            text: "Shopping cart",
            icon: "shopping_cart");
        createButton(
            type: ButtonType::ICON, 
            text: "Profile",
            icon: "account_circle",
            href: "/profile/");
        createButton(
            type: ButtonType::ICON,
            text: "Logout",
            icon: "logout",
            href: "/actions/logout.php");
    } else {
        createButton(
            type: ButtonType::TEXT,
            text: "Login",
            href: "/login/");
        createButton(
            type: ButtonType::TEXT,
            text: "Register",
            href: "/register/");
    }
} ?>

<?php function createColorSchemeToggle() {
    createButton(
        type: ButtonType::ICON, 
        text: "Toggle color scheme", 
        class: "color-scheme-toggle"
    );
} ?>

<?php function createRestaurantCard(Restaurant $restaurant) { ?>
    <article
        class="card responsive interactive"
        data-card-type="restaurant"
        data-restaurant-id="<?= $restaurant->id ?>"
    >
        <img
            src="<?= $restaurant->getThumbnail() ?>"
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
            createRestaurantCategories($categories);
        }
        
        session_start();

        if (isset($_SESSION['user'])) {
            if ($restaurant->owner === $_SESSION['user']) {
                createButton(
                    type: ButtonType::ICON,
                    text: "Edit",
                    icon: "edit",
                    class: "top-right",
                    href: "/restaurant/edit.php?id=$restaurant->id");
            } else {
                $currentUser = User::get($_SESSION['user']);

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

<?php function createAppBar(?string $value = null) { ?>
    <header class="appbar elevated fixed">
        <a href="/" class="title homepage-link"><h1 class="h6 color logo"></h1></a>

        <form class="search" action="/search/" method="GET">
            <input
                type="search"
                placeholder="Search"
                id="search"
                name="q"
                <?php if ($value !== null) { echo "value=\"$value\""; } ?>
            />
            <label for="search">search</label>
            <?php createButton(
                type: ButtonType::ICON,
                icon: "search",
                text: "Search",
                submit: true
            ) ?>
        </form>

        <?php
        createColorSchemeToggle();
        createUserButtons();
        ?>

    </header>
<?php } ?>

<?php function createRestaurantCategories(array $categories, bool $edit = false) {
    if ($edit) { ?>
    <a class="fullwidth chip-list-edit" href="#" data-open-dialog="#categories">
        <span>Categories</span>
    <?php } ?>
        <ul class="chip-list wrap">
            <?php foreach($categories as $category) { ?>
                <li class="chip"><?= $category->name ?></li>
            <?php } ?>
        </ul>
    <?php if ($edit) { ?>
    </a>
    <?php }
} ?>

<?php function createFavoriteRestaurants(User $user) {
    $favorites = $user->getFavoriteRestaurants();

    ?>
    <section class="restaurant-list">
        <header class="header">
            <h2 class="title h6">Your favorites</h2>
            <?php createButton(
                type: ButtonType::TEXT, text: "See all",
                class: "right",
                href: "/restaurants/"
            ) ?>
        </header>

        <?php 
        foreach($favorites as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
    <hr class="divider">
<?php } ?>

<?php function createProfileOwnedRestaurants(User $user) {
    $owned = $user->getOwnedRestaurants();

    if (!$owned)
        return;

    ?>
    <hr class="divider">
    <section class="restaurant-list">
        <header class="header">
            <h3 class="title h6">Owned Restaurants</h3>
        </header>

        <?php 
        foreach($owned as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createProfileFavoriteRestaurants(User $user) {
    $favorites = $user->getFavoriteRestaurants();

    if ($favorites === [])
        return;

    ?>
    <hr class="divider">
    <section class="restaurant-list">
        <header class="header">
            <h3 class="title h6">Favorite Restaurants</h3>
        </header>

        <?php 
        foreach($favorites as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createDishCard(Dish $dish, bool $show_restaurant = false, bool $edit = false) { 
    if ($show_restaurant && ($restaurant = $dish->getRestaurant()) === null) return;

    if ($edit) { ?>
        <article class="card responsive" >
            <label class="image-input full media thumbnail">
                <img
                    class="thumbnail"
                    src="<?= $dish->getThumbnail() ?>"
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
            );
            ?>
        </article>
    <?php } else { ?>
    <article
        class="card responsive interactive"
        data-card-type="dish"
        data-dish-id="<?= $dish->id ?>"
    >
        <img
            src="<?= $dish->getThumbnail() ?>"
            width="1920"
            height="1080"
            alt="Dish picture for <?= $dish->name ?>"
            class="full media thumbnail"
        />
        <header class="header">
            <h3 class="title h6">
                <a 
                    href="/dish/?id=<?= $dish->id ?>"
                    class="card-link"
                >
                    <?= $dish->name ?>
                </a>
            </h3>
            <span class="subtitle subtitle2 secondary">
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

            $currentUser = User::get($_SESSION['user']);

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

<?php function createProfileFavoriteDishes(User $user) {
    $favorites = $user->getFavoriteDishes();

    if (!$favorites)
        return;

    ?>
    <hr class="divider">
    <section class="dish-list">
        <header class="header">
            <h3 class="title h6">Favorite Dishes</h3>
        </header>

        <?php 
        foreach($favorites as $dish) {
            createDishCard($dish, true);
        }
        ?>
    </section>
<?php } ?>

<?php function createRestaurantOwnedDishes(Restaurant $restaurant, bool $edit = false) {
    $owned = $restaurant->getOwnedDishes();

    ?>
    <section class="dish-list">
        <header class="header">
            <h3 class="title h4">Dishes</h3>
        </header>

        <?php 
        foreach($owned as $dish) {
            createDishCard($dish, edit: $edit);
        }

        if ($edit) { ?>
            <article class="card">
                <?php createButton(
                    type: ButtonType::ICON,
                    text: "New dish",
                    icon: "add",
                    class: "card-link"
                ) ?>
            </article>
        <?php } ?>
    </section>
<?php } ?>

<?php function createMenuCard(Menu $menu, bool $show_restaurant = false) { 
    if ($show_restaurant && ($restaurant = $menu->getRestaurant()) === null) return;
?>
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
<?php } ?>

<?php function createRestaurantOwnedMenus(Restaurant $restaurant) {
    $owned = $restaurant->getOwnedMenus();

    ?>
    <section class="menu-list">
        <header class="header">
            <h2 class="title h4">Menus</h2>
        </header>

        <?php 
        foreach($owned as $menu) {
            createMenuCard($menu);
        }
        ?>
    </section>
<?php } ?>

<?php function createCheckBox(
    string $label, string $name, int $value, bool $checked
    ) { ?>

    <label>
        <input 
            type="checkbox"
            name="<?= $name ?>"
            value="<?= $value ?>" 
            class="checkbox" 
            <?php if($checked) echo "checked"; ?>
        >
        <?= $label ?>
    </label>

<?php } ?>

<?php function createCheckBoxList(array $values, string $title, string $class = "") {?>

    <fieldset class="selection-list <?= $class ?>">
        <?php if ($title) { ?>
        <legend class="h6"><?= $title ?></legend>
        <?php } ?>

        <?php foreach($values as $key) {
            createCheckBox(
                $key['label'],
                $key['name'],
                $key['value'],
                $key['checked']
            );
        } ?>
    </fieldset>

<?php } ?>

<?php function printReview(Review $review) { 

    $user = User::get($review->client);

    if ($user == null) return;
?>
    <article class="review">
        <a href="/profile/?id=<?= $user->id ?>">
            <header class="header">
                <img 
                    src=<?= $user->getProfilePic() ?>
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

<?php function createRestaurantReviewList(Restaurant $restaurant) { 

    $reviews = $restaurant->getReviews(7);
?>
    <section class="restaurant-reviews">
        <header class="header">
            <h4 class="title h4">Reviews</h4>
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
            printReview($review);
        } ?>
    </section>
<?php } ?>

<?php function createProfileCard(User $user) { ?>
    <article 
        class="card responsive interactive"
        data-card-type="user" 
        data-user-id="<?= $user->id ?>"
    >
        <img
            src="<?= $user->getProfilePic() ?>"
            width="320"
            height="180"
            alt="Profile picture for <?= $user->name ?>"
            class="full media"
        />
        <header class="header">
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

<?php function createSearchUserProfiles(array $users) { ?>
    <section class="user-list">
        <header class="header">
            <h2 class="title h4">Profiles</h2>
        </header>

        <?php 
        foreach($users as $user) {
            createProfileCard($user);
        }
        ?>
    </section>
<?php } ?>

<?php function createSearchMenus(array $menus) { ?>
    <section class="menu-list">
        <header class="header">
            <h2 class="title h4">Menus</h2>
        </header>

        <?php 
        foreach($menus as $menu) {
            createMenuCard($menu);
        }
        ?>
    </section>
<?php } ?>

<?php function createSearchRestaurants(array $restaurants) { ?>
    <section class="restaurant-list">
        <header class="header">
            <h3 class="title h4">Restaurants</h3>
        </header>

        <?php 
        foreach($restaurants as $restaurant) {
            createRestaurantCard($restaurant);
        }
        ?>
    </section>
<?php } ?>

<?php function createSearchDishes(array $dishes) { ?>
    <section class="dish-list">
        <header class="header">
            <h3 class="title h4">Dishes</h3>
        </header>

        <?php 
        foreach($dishes as $dish) {
            createDishCard($dish);
        }
        ?>
    </section>
<?php } ?>
