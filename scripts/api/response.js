// @ts-check

"use strict";

/**
 *
 * @param {number} reviewId
 * @returns {Promise<{
 *      id: number,
 *      text: string,
 *      response_date: string,
 *      review: number
 * }|undefined>}
 */
export const fetchReviewResponse = async (reviewId) => {
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
        return;
    }

    return reviewResponse;
};
