// @ts-check

"use strict";

export const toggleDishLikedStatus = async (id) => {
    const data = new FormData(); // POSTing to PHP requires FormData
    data.append("dishId", id);

    const response = await fetch("/api/dish/favorites/toggle.php", {
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
