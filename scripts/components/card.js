import { toggleRestaurantLikedStatus } from '../api/restaurant.js';
import { toggleDishLikedStatus } from '../api/dish.js';

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

/**
 * "Empowers" a dish card using javascript.
 *
 * @param {HTMLElement} restaurantCard
 */
const empowerDishCard = (dishCard) => {

    const { dishId } = dishCard.dataset;

    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        try {
            const favorite = await toggleDishLikedStatus(dishId);

            /** @type NodeListOf<HTMLButtonElement> */
            const favoriteButtons = document.querySelectorAll(
                `[data-card-type="dish"][data-dish-id="${dishId}"] button[data-favorite-button]`
            );

            favoriteButtons.forEach(
                (b) => (b.dataset.toggleState = favorite ? "on" : "off")
            );
        } catch {
            return;
        }
    };

    const favoriteButton = dishCard.querySelector(
        "button[data-favorite-button]"
    );

    favoriteButton.addEventListener("click", toggleLikedStatus);
}

/** @type NodeListOf<HTMLElement> */
const _restaurantCards = document.querySelectorAll(
    "[data-card-type=restaurant]"
);
_restaurantCards.forEach(empowerRestaurantCard);

/** @type NodeListOf<HTMLElement> */
const _dishCards = document.querySelectorAll(
    "[data-card-type=dish]"
);
_dishCards.forEach(empowerDishCard);
