// @ts-check

"use-strict";

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

    try {
        const response = await fetch("/api/cart/index.php", {
            method: "POST",
            body: data,
        });

        if (!response.ok) return;

        return await response.json();
    } catch {
        return;
    }
};
