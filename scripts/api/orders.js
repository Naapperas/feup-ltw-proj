// @ts-check

"use strict";

import { addSnackbar } from "../components/snackbar.js";

/**
 *
 * @param {number} restaurantId
 * @param {number} orderId
 * @param {"in_progress"|"ready"|"delivered"|"canceled"} state
 * @returns {Promise<{
 *      id: number,
 *      state: "pending"|"in_progress"|"ready"|"delivered"|"canceled",
 *      order_date: string,
 *      user: number,
 *      restaurant: number
 * }>}
 */
export const setOrderState = async (restaurantId, orderId, state) => {
    const data = new FormData();
    data.append("orderId", orderId.toString());
    data.append("state", state);

    const response = await fetch(`/api/restaurant/orders/?id=${restaurantId}`, {
        method: "POST",
        body: data,
    });

    const { order, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return order;
};
