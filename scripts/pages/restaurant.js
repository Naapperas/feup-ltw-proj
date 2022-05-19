import { toggleRestaurantLikedStatus } from '../api/restaurant.js';

/**
 * "Empowers" a restaurant page's like button using javascript.
 *
 * @param {HTMLElement} button
 */
const empowerLikeButton = (button) => {

    const restaurantId = button.dataset.restaurantId;

    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        try {
            const favorite = await toggleRestaurantLikedStatus(restaurantId);

            button.dataset.toggleState = favorite ? "on" : "off";
        } catch {
            return;
        }
    };

    button.addEventListener("click", toggleLikedStatus);
}

const button = document.querySelector("[data-favorite-button]");

empowerLikeButton(button);