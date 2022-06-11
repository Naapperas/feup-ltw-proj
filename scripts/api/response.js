// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

export const fetchReviewResponse = async (/** @type {Number} */ reviewId) => {
    const data = {
        reviewId: reviewId.toString(10),
    };

    const response = await fetch(
        `/api/review/response/?${Object.entries(data)
            .map(
                ([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(v)}`
            )
            .join("&")}`
    );

    const { response: reviewResponse, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return reviewResponse;
};
