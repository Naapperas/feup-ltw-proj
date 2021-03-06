<?php 
    declare(strict_types = 1);
    
    require_once('../templates/common.php');
    require_once('../templates/form.php');
    require_once('../templates/list.php');
    require_once('../templates/metadata.php');

    require_once('../lib/params.php');
    require_once('../lib/page.php');
    require_once('../lib/session.php');
    
    require_once('../database/models/user.php');
    require_once('../database/models/restaurant.php');
    
    $session = new Session();

    list('id' => $id) = parseParams(query: [
        'id' => new IntParam(
            optional: true
        ),
    ]);
    
    if ($id && ($restaurant = Restaurant::getById($id)) === null) {
        header("Location: /");
        die();
    }
    
    if (isset($restaurant) && $restaurant->owner !== $session->get('user')) {
        header("Location: /restaurant/?id=$id");
        die;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php createHead(
        metadata: baseMetadata(title: "Edit $restaurant->name"),
        scripts: [
            'components/form.js',
            'components/textfield.js',
            'components/imageinput.js',
            'components/dialog.js',
            'components/card.js',
            'components/snackbar.js',

            'pages/editrestaurant.js'
        ]
    ); ?>
    <body class="top-app-bar layout edit-restaurant">

        <template id="categories-template">
            <?php createCategoriesDialog(null, id: ''); ?>
        </template>
        <template id="dishes-template">
            <?php createDishesDialog(null, id: '', restaurant: $restaurant?->id); ?>
        </template>

        <?php createAppBar(); ?>
        <?php createForm(
            'POST', 'restaurant', '/actions/edit_restaurant.php', 'edit-restaurant-form',
            function() use ($restaurant) { ?>
                <div class="edit-restaurant-sidebar">
                    <label class="image-input thumbnail rounded fullwidth">
                        <img
                            src="<?= $restaurant?->getImagePath() ?? '/assets/pictures/restaurant/default.svg' ?>"
                            alt=""
                        >
                        <input
                            class="visually-hidden"
                            type="file"
                            name="thumbnail"
                            accept="image/*"
                        >
                    </label>
                    <?php
                    createTextField(
                        name: "name", label: "Name", value: $restaurant?->name ?? ''
                    );
                    createTextField(
                        name: "address", label: "Address", value: $restaurant?->address ?? ''
                    );
                    createTextField(
                        name: "phone", label: "Phone number", 
                        type: "tel", pattern: "\\d{9}",
                        errors: ["pattern-mismatch" => "Error: invalid phone number"],
                        value: $restaurant?->phone_number ?? ''
                    );
                    createTextField(
                        name: "website", label: "Website", type: 'url',
                        pattern: '^https?://.+',
                        errors: ["type-mismatch" => "Error: invalid website"],
                        value: $restaurant?->website ?? ''
                    );
                    createTextField(
                        name: "opening_time", label: "Opening time",
                        type: 'time', value: $restaurant?->opening_time ?? '',
                        class: "inline"
                    );
                    createTextField(
                        name: "closing_time", label: "Closing time",
                        type: 'time', value: $restaurant->closing_time ?? '',
                        class: "inline"
                    );

                    createCategoryList($restaurant?->getCategories() ?? [], true);
                    createCategoriesDialog($restaurant);
                    ?>
                </div>

                <div class="edit-restaurant-main">
                    <?php
                    createDishList($restaurant?->getOwnedDishes() ?? [], edit: true);
                    createMenuList($restaurant?->getOwnedMenus() ?? [], edit: true);
                    ?>
                </div>
                
                <?php createButton(text: "Apply", submit: true); ?>
                <input type="hidden" name="id" value="<?= $restaurant->id ?>">
            <?php }); ?>
    </body>
</html>
