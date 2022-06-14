// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { toggleDishLikedStatus } from "../api/dish.js";
import { updateCart } from "../api/cart.js";
import { createTextField } from "./textfield.js";
import { createImageInput } from "./imageinput.js";
import {
    empowerDialog,
    empowerOpenDialogButton,
    empowerCloseDialogButton,
} from "./dialog.js";
import { empowerEditCategoryList } from "./categorylist.js";
import { empowerEditDishList } from "./dishlist.js";
import { addSnackbar } from "./snackbar.js";

/**
 * "Empowers" a restaurant card using javascript.
 *
 * @param {HTMLElement} restaurantCard
 */
export const empowerRestaurantCard = (restaurantCard) => {
    const { restaurantId } = restaurantCard.dataset;

    /**
     * @param {MouseEvent} event
     * @returns {Promise<void>}
     */
    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        try {
            const favorite = await toggleRestaurantLikedStatus(
                parseInt(restaurantId ?? "")
            );

            if (favorite === undefined) return;

            /** @type {NodeListOf<HTMLButtonElement>} */
            const favoriteButtons = document.querySelectorAll(
                `[data-card-type="restaurant"][data-restaurant-id="${restaurantId}"] button[data-favorite-button]`
            );

            favoriteButtons.forEach((b) => {
                b.dataset.toggleState = favorite ? "on" : "off";
                b.ariaLabel = favorite ? "Unfavorite" : "Favorite";
            });
        } catch {
            return;
        }
    };

    /** @type {HTMLButtonElement?} */
    const favoriteButton = restaurantCard.querySelector(
        "button[data-favorite-button]"
    );
    favoriteButton?.addEventListener("click", toggleLikedStatus);
};

/**
 * "Empowers" a dish card using javascript.
 *
 * @param {HTMLElement} dishCard
 */
export const empowerDishCard = (dishCard) => {
    const { dishId } = dishCard.dataset;

    const toggleLikedStatus = async (event) => {
        event?.stopPropagation();

        try {
            const favorite = await toggleDishLikedStatus(
                parseInt(dishId ?? "")
            );

            if (favorite === undefined) return;

            /** @type {NodeListOf<HTMLButtonElement>} */
            const favoriteButtons = document.querySelectorAll(
                `[data-card-type="dish"][data-dish-id="${dishId}"] button[data-favorite-button]`
            );

            favoriteButtons.forEach((b) => {
                b.dataset.toggleState = favorite ? "on" : "off";
                b.ariaLabel = favorite ? "Unfavorite" : "Favorite";
            });
        } catch {
            return;
        }
    };

    const addDishToCart = async () => {
        event?.preventDefault();

        try {
            const newCart = await updateCart(parseInt(dishId ?? ""), "dish");

            if (newCart) {
                /** @type {HTMLElement?} */
                const cartBadge = document.querySelector("[data-cart]");
                if (cartBadge) {
                    cartBadge.dataset.badgeContent = newCart.size.toString();
                    cartBadge.classList.add("badge");
                }

                addSnackbar("Dish added to cart");
            }
        } catch {
            return;
        }
    };

    const cardLink = dishCard.querySelector(".card-link");
    cardLink?.addEventListener("click", addDishToCart);

    const favoriteButton = dishCard.querySelector(
        "button[data-favorite-button]"
    );
    favoriteButton?.addEventListener("click", toggleLikedStatus);
};

/**
 * "Empowers" a menu card using javascript.
 *
 * @param {HTMLElement} menuCard
 */
export const empowerMenuCard = (menuCard) => {
    const { menuId } = menuCard.dataset;

    const addDishToCart = async (event) => {
        event?.preventDefault();

        try {
            const newCart = await updateCart(parseInt(menuId ?? ""), "menu");

            if (newCart) {
                /** @type {HTMLElement?} */
                const cartBadge = document.querySelector("[data-cart]");
                if (cartBadge) {
                    cartBadge.dataset.badgeContent = newCart.size.toString();
                    cartBadge.classList.add("badge");
                }

                addSnackbar("Menu added to cart");
            }
        } catch {
            return;
        }
    };

    const cardLink = menuCard.querySelector(".card-link");
    cardLink?.addEventListener("click", addDishToCart);
};

/**
 * "Empowers" a dish card using javascript.
 *
 * @param {HTMLElement} editDishCard
 */
export const empowerEditDishCard = (editDishCard) => {
    /** @type {HTMLButtonElement?} */
    const deleteButton = editDishCard.querySelector(
        "button[data-delete-button]"
    );
    /** @type {HTMLInputElement?} */
    const nameInput = editDishCard.querySelector("input[name*=name]");
    /** @type {HTMLInputElement?} */
    const priceInput = editDishCard.querySelector("input[name*=price]");
    /** @type {HTMLInputElement?} */
    const imageInput = editDishCard.querySelector("input[type=file]");
    /** @type {HTMLLabelElement?} */
    const imagePicker = editDishCard.querySelector("label.image-input");
    /** @type {HTMLInputElement?} */
    const hiddenInput = editDishCard.querySelector("input[type=hidden]");

    /** @type {HTMLAnchorElement?} */
    const chipListLink = editDishCard.querySelector("a[data-open-dialog]");
    /** @type {HTMLElement?} */
    const chipList = chipListLink && chipListLink.querySelector("ul.chip-list");
    /** @type {HTMLFieldSetElement?} */
    const categoriesFieldset = editDishCard.querySelector(
        `${chipListLink?.dataset.openDialog} fieldset`
    );

    if (chipList && categoriesFieldset)
        empowerEditCategoryList(chipList, categoriesFieldset);

    const { dishId } = editDishCard.dataset;

    nameInput?.addEventListener("change", () => {
        /** @type {NodeListOf<HTMLInputElement>} */
        const inputs = document.querySelectorAll(
            `dialog[id*="menu-"][id$="-dishes"] fieldset input[value="${dishId}"]`
        );
        inputs.forEach((i) => {
            if (i.labels?.[0]?.lastChild)
                i.labels[0].lastChild.textContent = " " + nameInput.value;
        });
        /** @type {HTMLTemplateElement?} */
        const dishesTemplate = document.querySelector("#dishes-template");
        /** @type {HTMLInputElement?} */
        const templateInput =
            dishesTemplate &&
            dishesTemplate.content.querySelector(`input[value="${dishId}"]`);
        if (templateInput?.parentElement?.lastChild)
            templateInput.parentElement.lastChild.textContent =
                " " + nameInput.value;
    });

    if (deleteButton)
        deleteButton.addEventListener("click", async (event) => {
            event?.preventDefault();
            const deleted = editDishCard.toggleAttribute("data-deleted");

            deleteButton.dataset.toggleState = deleted ? "off" : "on";
            deleteButton.ariaLabel = deleted ? "Undo delete" : "Delete";
            deleteButton.textContent = deleted ? "delete_forever" : "delete";

            if (nameInput) nameInput.disabled = deleted;
            if (priceInput) priceInput.disabled = deleted;
            if (imageInput) imageInput.disabled = deleted;
            imagePicker?.classList.toggle("disabled", deleted);
            chipListLink?.classList.toggle("disabled", deleted);
            if (deleted) chipListLink?.removeAttribute("href");
            else chipListLink?.setAttribute("href", "#");
            if (categoriesFieldset) categoriesFieldset.disabled = deleted;

            if (hiddenInput) hiddenInput.disabled = !deleted;

            /** @type {NodeListOf<HTMLInputElement>} */
            const inputs = document.querySelectorAll(
                `dialog[id*="menu-"][id$="-dishes"] fieldset input[value="${dishId}"]`
            );
            inputs.forEach((i) => (i.disabled = deleted));
            /** @type {HTMLTemplateElement?} */
            const dishesTemplate = document.querySelector("#dishes-template");
            /** @type {HTMLInputElement?} */
            const templateInput =
                dishesTemplate &&
                dishesTemplate.content.querySelector(
                    `input[value="${dishId}"]`
                );
            if (templateInput) templateInput.disabled = deleted;
        });
};

/**
 * Creates a new dish card.
 *
 * @param {number} index
 * @returns
 */
export const createNewDishCard = (index) => {
    const card = document.createElement("article");
    card.classList.add("card", "responsive");
    card.dataset.cardType = "new-dish";
    card.dataset.dishId = (-index).toString();

    const imageInput = createImageInput(
        `dishes_to_add[${index}]`,
        `/assets/pictures/dish/default${Math.floor(Math.random() * 9)}.svg`,
        ["square", "full", "media", "gradient"],
        []
    );
    const nameInput = createTextField("Name", `dishes_to_add[${index}][name]`, {
        type: "text",
        required: "",
    });
    const priceInput = createTextField(
        "Price",
        `dishes_to_add[${index}][price]`,
        {
            type: "number",
            min: "0",
            step: "0.01",
            required: "",
        }
    );

    const chipListLink = document.createElement("a");
    chipListLink.href = "#";
    chipListLink.dataset.openDialog = `#new-dish-${index}-categories`;
    chipListLink.classList.add("fullwidth", "chip-list-edit");

    const chipListTitle = document.createElement("p");
    chipListTitle.innerText = "Categories";
    chipListLink.appendChild(chipListTitle);

    const chipList = document.createElement("ul");
    chipList.classList.add("chip-list", "wrap");
    chipListLink.appendChild(chipList);

    /** @type {HTMLTemplateElement?} */
    const categoriesTemplate = document.querySelector("#categories-template");
    /** @type {HTMLDialogElement?} */
    // @ts-ignore
    const categoriesDialog =
        categoriesTemplate &&
        categoriesTemplate.content.querySelector("dialog")?.cloneNode(true);
    if (categoriesDialog) {
        categoriesDialog.id = `new-dish-${index}-categories`;
        /** @type {HTMLButtonElement?} */
        const closeDialogButton = categoriesDialog.querySelector(
            "button[data-close-dialog]"
        );
        if (closeDialogButton)
            closeDialogButton.dataset.closeDialog = `#new-dish-${index}-categories`;
        /** @type {NodeListOf<HTMLInputElement>} */
        const categoryInputs = categoriesDialog.querySelectorAll("input");
        categoryInputs.forEach(
            (i) => (i.name = `dishes_to_add[${index}][categories][]`)
        );

        empowerDialog(categoriesDialog);
        empowerOpenDialogButton(chipListLink, categoriesDialog);
        if (closeDialogButton)
            empowerCloseDialogButton(closeDialogButton, categoriesDialog);
    }

    const deleteButton = document.createElement("button");
    deleteButton.classList.add("button", "icon", "top-right");
    deleteButton.toggleAttribute("data-delete-button", true);
    deleteButton.textContent = "delete";
    deleteButton.ariaLabel = "Delete";
    deleteButton.type = "button";

    card.append(
        imageInput,
        nameInput,
        priceInput,
        chipListLink,
        categoriesDialog ?? "",
        deleteButton
    );

    empowerEditDishCard(card);

    const addToFieldset = (/** @type {HTMLElement} */ f) => {
        const { id } = f;

        const newCheckbox = document.createElement("label");

        const newInput = document.createElement("input");
        newInput.type = "checkbox";
        newInput.value = (-index).toString();
        newInput.classList.add("checkbox");

        newInput.name = `${
            id.startsWith("new-menu") ? "menus_to_add" : "menus_to_edit"
        }[${id.match(/\d+/)?.[0] ?? ""}][dishes][]`;

        newCheckbox.append(newInput, document.createTextNode(" "));

        f.querySelector("fieldset")?.appendChild(newCheckbox);
    };

    /** @type {NodeListOf<HTMLElement>} */
    const dialogs = document.querySelectorAll(
        `dialog[id*="menu-"][id$="-dishes"]`
    );
    dialogs.forEach(addToFieldset);
    /** @type {HTMLTemplateElement?} */
    const dishesTemplate = document.querySelector("#dishes-template");
    /** @type {HTMLElement?} */
    const templateDialog =
        dishesTemplate && dishesTemplate.content.querySelector("dialog");
    if (templateDialog) addToFieldset(templateDialog);

    return card;
};

/**
 * "Empowers" a menu card using javascript.
 *
 * @param {HTMLElement} editMenuCard
 */
export const empowerEditMenuCard = (editMenuCard) => {
    /** @type {HTMLButtonElement?} */
    const deleteButton = editMenuCard.querySelector(
        "button[data-delete-button]"
    );
    /** @type {HTMLInputElement?} */
    const nameInput = editMenuCard.querySelector("input[name*=name]");
    /** @type {HTMLInputElement?} */
    const priceInput = editMenuCard.querySelector("input[name*=price]");
    /** @type {HTMLInputElement?} */
    const imageInput = editMenuCard.querySelector("input[type=file]");
    /** @type {HTMLLabelElement?} */
    const imagePicker = editMenuCard.querySelector("label.image-input");
    /** @type {HTMLInputElement?} */
    const hiddenInput = editMenuCard.querySelector("input[type=hidden]");

    /** @type {HTMLAnchorElement?} */
    const dishListLink = editMenuCard.querySelector("a[data-open-dialog]");
    /** @type {HTMLElement?} */
    const dishList = dishListLink && dishListLink.querySelector("ul");
    /** @type {HTMLFieldSetElement?} */
    const dishesFieldset = editMenuCard.querySelector(
        `${dishListLink?.dataset.openDialog} fieldset`
    );

    const onDishesInput = () => {
        const checked =
            dishesFieldset?.querySelectorAll("input:checked")?.length ?? 0;

        if (checked < 2) imageInput?.setCustomValidity("At least two dishes");
        else imageInput?.setCustomValidity("");

        console.log(dishesFieldset, checked);
    };

    dishesFieldset?.addEventListener("change", onDishesInput);
    nameInput?.addEventListener("input", onDishesInput);
    priceInput?.addEventListener("input", onDishesInput);

    empowerEditDishList(dishList, dishesFieldset);

    if (deleteButton)
        deleteButton.addEventListener("click", async (event) => {
            event?.preventDefault();
            const deleted = editMenuCard.toggleAttribute("data-deleted");

            deleteButton.dataset.toggleState = deleted ? "off" : "on";
            deleteButton.ariaLabel = deleted ? "Undo delete" : "Delete";
            deleteButton.textContent = deleted ? "delete_forever" : "delete";

            if (nameInput) nameInput.disabled = deleted;
            if (priceInput) priceInput.disabled = deleted;
            if (imageInput) imageInput.disabled = deleted;
            imagePicker?.classList.toggle("disabled", deleted);
            dishListLink?.classList.toggle("disabled", deleted);
            if (deleted) dishListLink?.removeAttribute("href");
            else dishListLink?.setAttribute("href", "#");
            if (dishesFieldset) dishesFieldset.disabled = deleted;

            if (hiddenInput) hiddenInput.disabled = !deleted;
        });
};

/**
 * Creates a new menu card.
 *
 * @param {number} index
 * @returns
 */
export const createNewMenuCard = (index) => {
    const card = document.createElement("article");
    card.classList.add("card", "responsive");
    card.dataset.cardType = "new-menu";

    const imageInput = createImageInput(
        `menus_to_add[${index}]`,
        "/assets/pictures/menu/default.svg",
        ["square", "full", "media", "gradient"],
        []
    );
    const nameInput = createTextField("Name", `menus_to_add[${index}][name]`, {
        type: "text",
        required: "",
    });
    const priceInput = createTextField(
        "Price",
        `menus_to_add[${index}][price]`,
        {
            type: "number",
            min: "0",
            step: "0.01",
            required: "",
        }
    );

    const dishListLink = document.createElement("a");
    dishListLink.href = "#";
    dishListLink.dataset.openDialog = `#new-menu-${index}-dishes`;
    dishListLink.classList.add("fullwidth");

    const dishListTitle = document.createElement("p");
    dishListTitle.innerText = "Dishes";
    dishListLink.appendChild(dishListTitle);

    const dishList = document.createElement("ul");
    dishListLink.appendChild(dishList);

    /** @type {HTMLTemplateElement?} */
    const dishesTemplate = document.querySelector("#dishes-template");
    /** @type {HTMLDialogElement?} */
    // @ts-ignore
    const dishesDialog =
        dishesTemplate &&
        dishesTemplate.content.querySelector("dialog")?.cloneNode(true);

    if (dishesDialog) {
        dishesDialog.id = `new-menu-${index}-dishes`;
        /** @type {HTMLButtonElement?} */
        const closeDialogButton = dishesDialog.querySelector(
            "button[data-close-dialog]"
        );
        if (closeDialogButton)
            closeDialogButton.dataset.closeDialog = `#new-menu-${index}-dishes`;
        /** @type {NodeListOf<HTMLInputElement>} */
        const dishInputs = dishesDialog.querySelectorAll("input");
        dishInputs.forEach(
            (i) => (i.name = `menus_to_add[${index}][dishes][]`)
        );

        empowerDialog(dishesDialog);
        empowerOpenDialogButton(dishListLink, dishesDialog);
        if (closeDialogButton)
            empowerCloseDialogButton(closeDialogButton, dishesDialog);
    }

    const deleteButton = document.createElement("button");
    deleteButton.classList.add("button", "icon", "top-right");
    deleteButton.toggleAttribute("data-delete-button", true);
    deleteButton.textContent = "delete";
    deleteButton.ariaLabel = "Delete";
    deleteButton.type = "button";

    card.append(
        imageInput,
        nameInput,
        priceInput,
        dishListLink,
        dishesDialog ?? "",
        deleteButton
    );

    empowerEditMenuCard(card);

    return card;
};

/** @type NodeListOf<HTMLElement> */
const restaurantCards = document.querySelectorAll(
    "[data-card-type=restaurant]"
);
restaurantCards.forEach(empowerRestaurantCard);

/** @type NodeListOf<HTMLElement> */
const dishCards = document.querySelectorAll("[data-card-type=dish]");
dishCards.forEach(empowerDishCard);

/** @type NodeListOf<HTMLElement> */
const menuCards = document.querySelectorAll("[data-card-type=menu]");
menuCards.forEach(empowerMenuCard);

/** @type NodeListOf<HTMLElement> */
const editDishCards = document.querySelectorAll("[data-card-type=edit-dish]");
editDishCards.forEach(empowerEditDishCard);

/** @type NodeListOf<HTMLElement> */
const editMenuCards = document.querySelectorAll("[data-card-type=edit-menu]");
editMenuCards.forEach(empowerEditMenuCard);
