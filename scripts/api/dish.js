// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

export const toggleDishLikedStatus = async (id) => {
    const data = new FormData(); // POSTing to PHP requires FormData
    data.append("dishId", id);

    const response = await fetch("/api/user/favorite_dishes", {
        method: "POST",
        body: data,
    });

    const { favorite, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return favorite;
};
