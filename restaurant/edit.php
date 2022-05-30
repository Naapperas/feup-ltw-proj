<?php 
    declare(strict_types = 1);
    
    require_once('../templates/components.php');
    require_once('../templates/metadata.php');

    require_once('../lib/params.php');
    
    require_once('../database/models/user.php');
    require_once('../database/models/restaurant.php');
    
    session_start();

    list('id' => $id) = parseParams(get_params: [
        'id' => new IntParam(
            optional: true
        ),
    ]);
    
    if (!isset($id) || ($restaurant = Restaurant::get($id)) === null) {
        header("Location: /");
        die();
    }
    
    if ($restaurant->owner !== $_SESSION['user']) {
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
            'components/dialog.js'
        ]
    ); ?>
    <body class="top-app-bar layout edit-restaurant">
        <?php createAppBar(); ?>
        <?php createForm(
            'POST', 'restaurant', '/actions/edit_restaurant.php',
            function() use ($restaurant) { ?>
                <div class="edit-restaurant-sidebar">
                    <label class="image-input thumbnail rounded">
                        <img
                            class="thumbnail"
                            src="<?= $restaurant->getThumbnail() ?>"
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
                        name: "name", label: "Name", value: $restaurant->name
                    );
                    createTextField(
                        name: "address", label: "Address", value: $restaurant->address
                    );
                    createTextField(
                        name: "phone", label: "Phone number", 
                        type: "tel", pattern: "\\d{9}",
                        errors: ["pattern-mismatch" => "Error: invalid phone number"],
                        value: $restaurant->phone_number
                    );
                    createTextField(
                        name: "website", label: "Website", type: 'url',
                        pattern: '^https?://.+',
                        errors: ["type-mismatch" => "Error: invalid website"],
                        value: $restaurant->website
                    );
                    createTextField(
                        name: "opening_time", label: "Opening time",
                        type: 'time', value: $restaurant->opening_time,
                        class: "inline"
                    );
                    createTextField(
                        name: "closing_time", label: "Closing time",
                        type: 'time', value: $restaurant->closing_time,
                        class: "inline"
                    );

                    createRestaurantCategories($restaurant->getCategories(), true);

                    ?><dialog class="dialog confirmation" id="categories">
                        <header><h2 class="h5">Categories</h2></header>
                        <?php createCheckBoxList(array_map(fn(Category $category) => [
                            'label' => $category->name,
                            'value' => $category->id,
                            'name' => 'categories[]',
                            'checked' => $restaurant->hasCategory($category->id)
                        ], Category::get()), '', 'content'); ?>
                        <div class="actions">
                            <button class="button text" type="button" data-close-dialog="#categories">Done</button>
                        </div>
                    </dialog>
                </div>

                <div class="edit-restaurant-main">
                    <?php
                    createRestaurantOwnedDishes($restaurant, true);
                    ?>
                </div>
                
                <?php createButton(text: "Apply", submit: true); ?>
                <input type="hidden" name="id" value="<?= $restaurant->id ?>">
            <?php }); ?>
    </body>
</html>
