// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

export const toggleRestaurantLikedStatus = async (id) => {
    const data = new FormData(); // POSTing to PHP requires FormData
    data.append("restaurantId", id);

    const response = await fetch("/api/user/favorite_restaurants/", {
        method: "POST",
        body: data,
    });

    const { favorite, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return favorite;
};
