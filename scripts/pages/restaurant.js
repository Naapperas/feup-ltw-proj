import { toggleRestaurantLikedStatus } from '../api/restaurant.js';
import { toggleDishLikedStatus } from '../api/dish.js';

/**
 * "Empowers" a restaurant page's like button using javascript.
 *
 * @param {HTMLElement} button
 */
const empowerRestaurantLikeButton = (button) => {

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

const empowerDishLikeButton = (button) => {

    const dishId = button.dataset.dishId;

    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        try {
            const favorite = await toggleDishLikedStatus(dishId);

            button.dataset.toggleState = favorite ? "on" : "off";
        } catch {
            return;
        }
    };

    button.addEventListener("click", toggleLikedStatus);
}

const restaurantFavoriteButton = document.querySelector("[data-restaurant-id][data-favorite-button]");
empowerRestaurantLikeButton(restaurantFavoriteButton);

const dishFavoriteButtons = document.querySelectorAll("[data-dish-id][data-favorite-button]");

dishFavoriteButtons.forEach(button => empowerDishLikeButton(button));