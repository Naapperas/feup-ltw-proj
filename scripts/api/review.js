// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

/**
 *
 * @param {number} restaurantId
 * @param {"score"|"date"} attribute
 * @param {"asc"|"desc"} order
 * @returns {Promise<{
 *      id: number,
 *      score: number,
 *      text: string,
 *      review_date: string,
 *      client: number,
 *      restaurant: number
 * }[]|undefined>}
 */
export const fetchOrderedRestaurantReviews = async (
    restaurantId,
    attribute,
    order
) => {
    const data = {
        restaurantId: restaurantId.toString(10),
        attribute: attribute,
        order: order,
    };

    const response = await fetch(
        `/api/restaurant/reviews/?${Object.entries(data)
            .map(
                ([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`
            )
            .join("&")}`
    );

    const { reviews, error } = await response.json();

    if (error) {
        addSnackbar(error);
        return;
    }

    return reviews;
};

/**
 *
 * @param {number} reviewId
 * @returns {Promise<{
 *      id: number,
 *      score: number,
 *      text: string,
 *      review_date: string,
 *      client: number,
 *      restaurant: number
 * }|undefined>}
 */
export const fetchReview = async (reviewId) => {
    const data = {
        id: reviewId.toString(10),
    };

    const response = await fetch(
        `/api/review?${Object.entries(data)
            .map(
                ([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`
            )
            .join("&")}`
    );

    const { review, error } = await response.json();

    if (error) {
        addSnackbar(error);
        return;
    }

    return review;
};
