import { toggleRestaurantLikedStatus } from '../api/restaurant.js';

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

        try {
            const favorite = await toggleRestaurantLikedStatus(restaurantId);

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
