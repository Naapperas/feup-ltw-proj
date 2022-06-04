// @ts-check

"use strict";

export const fetchOrderedReviews = async (/** @type {Number} */ restaurantId, /** @type {string} */ attribute, /** @type {string} */ order) => {

    const data = {
        'restaurantId': restaurantId.toString(10),
        'attribute': attribute,
        'order': order
    };

    const response = await fetch(`/api/review?${Object.entries(data).map(([k, v]) => `${k}=${v}`).join("&")}`);

    if (!response.ok) return [];

    const { reviews, error } = await response.json();

    if (error) {
        // TODO: add snackbar
    }

    return reviews
}
