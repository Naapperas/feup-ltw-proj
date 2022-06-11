// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

export const fetchUser = async (/** @type {Number} */ userId) => {
    const data = {
        id: userId.toString(10),
    };

    const response = await fetch(
        `/api/user/?${Object.entries(data)
            .map(
                ([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`
            )
            .join("&")}`
    );

    const { user, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return user;
};
