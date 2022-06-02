// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { toggleDishLikedStatus } from "../api/dish.js";
import { addProductToCart } from "../api/cart.js";
import { createTextField } from "./textfield.js";
import { createImageInput } from "./imageinput.js";

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
            const favorite = await toggleRestaurantLikedStatus(restaurantId);

            /** @type NodeListOf<HTMLButtonElement> */
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

    /** @type HTMLButtonElement */
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
            const favorite = await toggleDishLikedStatus(dishId);

            /** @type NodeListOf<HTMLButtonElement> */
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

    const addDishToCart = async (event) => {
        event?.preventDefault();

        try {
            const newCart = await addProductToCart(parseInt(dishId), "dish");

            console.debug(newCart);

            if (newCart) {
                /** @type HTMLElement */
                const cartBadge = document.querySelector("[data-cart]");

                let cartCount = 0;
                for (const id in newCart.dishes)
                    cartCount += newCart.dishes[id];
                for (const id in newCart.menus) cartCount += newCart.menus[id];

                cartBadge.dataset.badgeContent = cartCount.toString();
                cartBadge.classList.add("badge");
            }
        } catch {
            return;
        }
    };

    const cardLink = dishCard.querySelector(".card-link");
    cardLink.addEventListener("click", addDishToCart);

    const favoriteButton = dishCard.querySelector(
        "button[data-favorite-button]"
    );
    favoriteButton.addEventListener("click", toggleLikedStatus);
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
            const newCart = await addProductToCart(parseInt(menuId), "menu");

            console.debug(newCart);

            if (newCart) {
                /** @type HTMLElement */
                const cartBadge = document.querySelector("[data-cart]");

                let cartCount = 0;
                for (const id in newCart.dishes)
                    cartCount += newCart.dishes[id];
                for (const id in newCart.menus) cartCount += newCart.menus[id];

                cartBadge.dataset.badgeContent = cartCount.toString();
                cartBadge.classList.add("badge");
            }
        } catch {
            return;
        }
    };

    const cardLink = menuCard.querySelector(".card-link");
    cardLink.addEventListener("click", addDishToCart);
};

/**
 * "Empowers" a dish card using javascript.
 *
 * @param {HTMLElement} editDishCard
 */
export const empowerEditDishCard = (editDishCard) => {
    /** @type HTMLButtonElement */
    const deleteButton = editDishCard.querySelector(
        "button[data-delete-button]"
    );
    /** @type HTMLInputElement */
    const nameInput = editDishCard.querySelector("input[name*=name]");
    /** @type HTMLInputElement */
    const priceInput = editDishCard.querySelector("input[name*=price]");
    /** @type HTMLInputElement */
    const imageInput = editDishCard.querySelector("input[type=file]");
    /** @type HTMLLabelElement */
    const imagePicker = editDishCard.querySelector("label.image-input");
    /** @type HTMLInputElement */
    const hiddenInput = editDishCard.querySelector("input[type=hidden]");

    const toggleDeletedStatus = async (event) => {
        event?.preventDefault();
        const deleted = editDishCard.toggleAttribute("data-deleted");

        deleteButton.dataset.toggleState = deleted ? "off" : "on";
        deleteButton.ariaLabel = deleted ? "Undo delete" : "Delete";
        deleteButton.textContent = deleted ? "delete_forever" : "delete";

        if (nameInput) nameInput.disabled = deleted;
        if (priceInput) priceInput.disabled = deleted;
        if (imageInput) imageInput.disabled = deleted;
        imagePicker?.classList.toggle("disabled", deleted);

        if (hiddenInput) hiddenInput.disabled = !deleted;
    };

    if (deleteButton)
        deleteButton.addEventListener("click", toggleDeletedStatus);
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

    const imageInput = createImageInput(
        `dishes_to_add[${index}]`,
        "/assets/pictures/dish/default.webp",
        ["thumbnail", "full", "media"],
        ["thumbnail"]
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

    const deleteButton = document.createElement("button");
    deleteButton.classList.add("button", "icon", "top-right");
    deleteButton.toggleAttribute("data-delete-button", true);
    deleteButton.textContent = "delete";
    deleteButton.ariaLabel = "Delete";
    deleteButton.type = "button";

    card.append(imageInput, nameInput, priceInput, deleteButton);

    empowerEditDishCard(card);

    return card;
};

/**
 * "Empowers" a menu card using javascript.
 *
 * @param {HTMLElement} editMenuCard
 */
export const empowerEditMenuCard = (editMenuCard) => {
    /** @type HTMLButtonElement */
    const deleteButton = editMenuCard.querySelector(
        "button[data-delete-button]"
    );
    /** @type HTMLInputElement */
    const nameInput = editMenuCard.querySelector("input[name*=name]");
    /** @type HTMLInputElement */
    const priceInput = editMenuCard.querySelector("input[name*=price]");
    /** @type HTMLInputElement */
    const imageInput = editMenuCard.querySelector("input[type=file]");
    /** @type HTMLLabelElement */
    const imagePicker = editMenuCard.querySelector("label.image-input");
    /** @type HTMLInputElement */
    const hiddenInput = editMenuCard.querySelector("input[type=hidden]");

    const toggleDeletedStatus = async (event) => {
        event?.preventDefault();
        const deleted = editMenuCard.toggleAttribute("data-deleted");

        deleteButton.dataset.toggleState = deleted ? "off" : "on";
        deleteButton.ariaLabel = deleted ? "Undo delete" : "Delete";
        deleteButton.textContent = deleted ? "delete_forever" : "delete";

        if (nameInput) nameInput.disabled = deleted;
        if (priceInput) priceInput.disabled = deleted;
        if (imageInput) imageInput.disabled = deleted;
        imagePicker?.classList.toggle("disabled", deleted);

        if (hiddenInput) hiddenInput.disabled = !deleted;
    };

    if (deleteButton)
        deleteButton.addEventListener("click", toggleDeletedStatus);
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
        "/assets/pictures/menu/default.webp",
        ["thumbnail", "full", "media"],
        ["thumbnail"]
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

    const deleteButton = document.createElement("button");
    deleteButton.classList.add("button", "icon", "top-right");
    deleteButton.toggleAttribute("data-delete-button", true);
    deleteButton.textContent = "delete";
    deleteButton.ariaLabel = "Delete";
    deleteButton.type = "button";

    card.append(imageInput, nameInput, priceInput, deleteButton);

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
