// @ts-check

"use strict";

import { addSnackbar } from '../components/snackbar.js';

export const fetchOrderedRestaurantReviews = async (/** @type {Number} */ restaurantId, /** @type {string} */ attribute, /** @type {string} */ order) => {

    const data = {
        'restaurantId': restaurantId.toString(10),
        'attribute': attribute,
        'order': order
    };

    const response = await fetch(`/api/restaurant/reviews?${Object.entries(data).map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join("&")}`);

    const { reviews, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return reviews;
}

export const fetchReview = async (/** @type {Number} */ reviewId) => {
    
    const data = {
        'reviewId': reviewId.toString(10),
    };

    const response = await fetch(`/api/review?${Object.entries(data).map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`).join("&")}`);

    const { review, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return review;
}