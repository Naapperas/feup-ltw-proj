// @ts-check

"use-strict";

export const addProductToCart = async (id, type) => {
    const data = new FormData();
    data.append("productId", id);
    data.append("productType", type);

    const response = await fetch(
        "/api/cart/index.php",
        {
            method: "POST",
            body: data,
        }
    );

    if (!response.ok) return;

    const { ok } = await response.json();

    return ok;
}
