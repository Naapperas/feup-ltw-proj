// @ts-check

"use strict";

const toggleStyles = {
    "favorite": "favorite_border",
    "favorite_border": "favorite"
};

const toggleText = {
    "favorite": "Favorite",
    "favorite_border": "Unfavorite"
};

const toggleLikeButtonStyle = (element) => {

    const oldIcon = element.innerHTML.trim();

    element.innerHTML = toggleStyles[oldIcon];
    element.ariaLabel = toggleText[oldIcon];
};

const toggleRestaurantLikedStatus = async (event) => {
    event?.preventDefault();

    const restaurantId = event.target.parentNode.parentNode.dataset.restaurantId;

    const data = new FormData(); // POSTing to PHP requires FormData
    data.append("restaurantId", restaurantId);

    const options = {
        method: 'POST',
        body: data,
    };
    const url = "../../api/restaurant/favorites/toggle.php";

    const response = await fetch(url, options).catch(() => null);

    if (response === null || !response.ok) return;

    const success = await response.json();

    if (success === false) return;

    const restaurantCards = document.querySelectorAll(`[data-card-type="restaurant"][data-restaurant-id="${restaurantId}"]>article>button`);

    restaurantCards.forEach(element => toggleLikeButtonStyle(element));
}