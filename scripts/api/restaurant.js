// @ts-check

"use strict";

const toggleText = {
    off: "Favorite",
    on: "Unfavorite",
};

/**
 * "Empowers" a restaurant card using javascript.
 *
 * @param {HTMLElement} restaurantCard
 */
const empowerRestaurantCard = (restaurantCard) => {
    const { restaurantId } = restaurantCard.dataset;

    /**
     * @param {MouseEvent} event
     * @returns {Promise<void>}
     */
    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        const data = new FormData(); // POSTing to PHP requires FormData
        data.append("restaurantId", restaurantId);

        try {
            const response = await fetch(
                "/api/restaurant/favorites/toggle.php",
                {
                    method: "POST",
                    body: data,
                }
            );

            if (!response.ok) return;

            const { favorite } = await response.json();

            /** @type NodeListOf<HTMLButtonElement> */
            const favoriteButtons = document.querySelectorAll(
                `[data-card-type="restaurant"][data-restaurant-id="${restaurantId}"] button[data-favorite-button]`
            );

            favoriteButtons.forEach(
                (b) => (b.dataset.toggleState = favorite ? "on" : "off")
            );
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

/** @type NodeListOf<HTMLElement> */
const _restaurantCards = document.querySelectorAll(
    "[data-card-type=restaurant]"
);
_restaurantCards.forEach(empowerRestaurantCard);
