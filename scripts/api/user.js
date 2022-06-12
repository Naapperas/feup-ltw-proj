// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

/**
 *
 * @param {number} userId
 * @returns {Promise<{
 *      id: number,
 *      name: string,
 *      email: string,
 *      address: string,
 *      phone_number: string,
 *      full_name: string,
 *      image: string
 * }|undefined>}
 */
export const fetchUser = async (userId) => {
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
        return;
    }

    return user;
};
