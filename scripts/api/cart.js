// @ts-check

"use-strict";

import { addSnackbar } from "../components/snackbar.js";

/**
 *
 * @param {number} id
 * @param {"dish"|"menu"} type
 * @param {number} amount
 * @returns {Promise<{
 *      dishes: Record<number, number> | undefined,
 *      menus: Record<number, number> | undefined,
 *      size: number,
 *      total: Record<number, number>
 *  }|undefined>}
 */
export const updateCart = async (id, type, amount = 1) => {
    const data = new FormData();
    data.append("productId", id.toString(10));
    data.append("productType", type);
    data.append("amount", amount.toString());

    const response = await fetch("/api/cart/", {
        method: "POST",
        body: data,
    });

    const { cart, error } = await response.json();

    if (error) {
        addSnackbar(error);
        return;
    }

    cart.size = 0;
    for (const id in cart.dishes) cart.size += cart.dishes[id];
    for (const id in cart.menus) cart.size += cart.menus[id];

    return cart;
};
