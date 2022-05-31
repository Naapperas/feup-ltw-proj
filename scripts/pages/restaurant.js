// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";

/**
 * "Empowers" a restaurant page's like button using javascript.
 *
 * @param {HTMLElement} button
 */
const empowerRestaurantLikeButton = (button) => {
    if (!button) return;

    const restaurantId = button.dataset.restaurantId;

    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        try {
            const favorite = await toggleRestaurantLikedStatus(restaurantId);

            button.dataset.toggleState = favorite ? "on" : "off";
            button.ariaLabel = favorite ? "Unfavorite" : "Favorite";
        } catch {
            return;
        }
    };

    button.addEventListener("click", toggleLikedStatus);
};

/** @type HTMLElement */
const _restaurantFavoriteButton = document.querySelector(
    "[data-restaurant-id][data-favorite-button]"
);
empowerRestaurantLikeButton(_restaurantFavoriteButton);
