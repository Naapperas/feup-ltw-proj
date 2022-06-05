// @ts-check

"use strict";

import { addSnackbar } from '../components/snackbar.js';

export const fetchOrderedReviews = async (/** @type {Number} */ restaurantId, /** @type {string} */ attribute, /** @type {string} */ order) => {

    const data = {
        'restaurantId': restaurantId.toString(10),
        'attribute': attribute,
        'order': order
    };

    const response = await fetch(`/api/review?${Object.entries(data).map(([k, v]) => `${k}=${v}`).join("&")}`);

    const { reviews, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return reviews
}
