// @ts-check

"use strict";

export const toggleRestaurantLikedStatus = async (id) => {
    const data = new FormData(); // POSTing to PHP requires FormData
    data.append("restaurantId", id);

    const response = await fetch("/api/restaurant/favorites/toggle.php", {
        method: "POST",
        body: data,
    });

    if (!response.ok) return;

    const { favorite, error } = await response.json();

    if (error) {
        // TODO: add snackbar
    }

    return favorite;
};
