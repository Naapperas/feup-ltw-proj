// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { toggleDishLikedStatus } from "../api/dish.js";
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
    favoriteButton.addEventListener("click", toggleLikedStatus);
};

/**
 * "Empowers" a dish card using javascript.
 *
 * @param {HTMLElement} dishCard
 */
export const empowerDishCard = (dishCard) => {
    const { dishId } = dishCard.dataset;

    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

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

    const favoriteButton = dishCard.querySelector(
        "button[data-favorite-button]"
    );

    favoriteButton.addEventListener("click", toggleLikedStatus);
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

/** @type NodeListOf<HTMLElement> */
const restaurantCards = document.querySelectorAll(
    "[data-card-type=restaurant]"
);
restaurantCards.forEach(empowerRestaurantCard);

/** @type NodeListOf<HTMLElement> */
const dishCards = document.querySelectorAll("[data-card-type=dish]");
dishCards.forEach(empowerDishCard);

/** @type NodeListOf<HTMLElement> */
const editDishCards = document.querySelectorAll("[data-card-type=edit-dish]");
editDishCards.forEach(empowerEditDishCard);
