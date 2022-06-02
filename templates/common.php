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

require_once(__DIR__.'/form.php');
?>

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

<?php function createUserButtons() {
    if (isset($_SESSION['user'])) {    
        $cartItemCount = array_sum($_SESSION['cart']['dishes'] ?? []) 
                       + array_sum($_SESSION['cart']['menus'] ?? []);
    
        createButton(
            type: ButtonType::ICON,
            text: "Shopping cart",
            icon: "shopping_cart",
            class: $cartItemCount ? "badge" : '',
            attributes: 
                ($cartItemCount ? "data-badge-content=\"$cartItemCount\"\n" : '').
                "data-cart",
            href: "/cart/");
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

<?php function createSearchBar(?string $query) { ?>
    <form class="search" action="/search/" method="GET">
        <input
            type="search"
            placeholder="Search"
            id="search"
            name="q"
            <?php if ($query !== null) { echo "value=\"$query\""; } ?>
        />
        <label for="search">search</label>
        <?php createButton(
            type: ButtonType::ICON,
            icon: "filter_list",
            text: "Filter",
            attributes: 
                "data-open-dialog=\"#filters\"\n".
                "data-filter-button"
        );
        createButton(
            type: ButtonType::ICON,
            icon: "search",
            text: "Search",
            submit: true,
            attributes: "data-search-button"
        ); ?>
        <dialog class="dialog confirmation" id="filters">
            <header><h2 class="h4">Search Filters</h2></header>
            <div class="content">
                <section>
                    <h3 class="title h5">Restaurants</h3>
                    <?php createSlider("min_restaurant_score", "Minimum Score:", "20", 0, 5, 2, 0.1); ?>
                    <?php createSlider("max_restaurant_score", "Maximum Score:", "40", 0, 5, 4, 0.1); ?>
                </section>
                <section>
                    <h3 class="title h5">Dishes</h3>
                    <?php createSlider("min_dish_price", "Minimum Price:", "0.0€", 0, 20, 0, 0.01); ?>
                    <?php createSlider("max_dish_price", "Maximum Price:", "20€", 0, 20, 20, 0.01); ?>
                </section>
            </div>
            <div class="actions">
                <?php createButton(type: ButtonType::TEXT, text: 'Done', attributes: 'data-close-dialog="#filters"') ?>
            </div>
        </dialog>
    </form>
<?php } ?>

<?php function createAppBar(?string $query = null) { ?>
    <header class="appbar elevated fixed">
        <a href="/" class="title homepage-link"><h1 class="h6 color logo"></h1></a>

        <?php
        createSearchBar($query);
        createColorSchemeToggle();
        createUserButtons();
        ?>
    </header>
<?php } ?>
