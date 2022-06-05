// @ts-check

"use-strict";

import { addSnackbar } from '../components/snackbar.js';

/**
 *
 * @param {number} id
 * @param {"dish"|"menu"} type
 * @returns {Promise<{
 *      'dishes': Record<number, number> | undefined,
 *      'menus': Record<number, number> | undefined
 *  }?>}
 */
export const addProductToCart = async (id, type) => {
    const data = new FormData();
    data.append("productId", id.toString(10));
    data.append("productType", type);

    const response = await fetch("/api/cart", {
        method: "POST",
        body: data,
    });

    const { cart, error } = await response.json();

    if (error) {
        addSnackbar(error);
    }

    return cart;
};
