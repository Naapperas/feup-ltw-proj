// @ts-check

"use strict";

export const fetchUser = async (/** @type {Number} */ userId) => {

    const data = {
        'userId': userId.toString(10),
    };

    const response = await fetch(`/api/user?${Object.entries(data).map(([k, v]) => `${k}=${v}`).join("&")}`);

    if (!response.ok) return {};

    const { user, userPhotoPath, error } = await response.json();
    
    if (error) {
        // TODO: add snackbar
    }

    return { user, userPhotoPath };
}
