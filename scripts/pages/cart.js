// @ts-check

"use strict";

import { updateCart } from "../api/cart.js";

/** @type {HTMLElement?} */
const cartBadge = document.querySelector("[data-cart]");

const empowerOrderForm = (/** @type {HTMLFormElement} */ form) => {
    /** @type {NodeListOf<HTMLElement>} */
    const cards = form.querySelectorAll("[data-cart-card-type]");
    /** @type {HTMLFormElement?} */
    const totalSpan = form.querySelector("span.cart-total");

    const restaurantId = parseInt(form.id.match(/\d+/)?.[0] ?? "", 10);

    const empowerCartCard = (/** @type {HTMLElement} */ card) => {
        const { cartCardType } = card.dataset;
        const cartCardId = parseInt(card.dataset.cartCardId ?? "", 10);

        if (cartCardType !== "menu" && cartCardType !== "dish") return;

        /** @type {HTMLButtonElement?} */
        const addButton = card.querySelector("button[data-add-unit]");
        /** @type {HTMLButtonElement?} */
        const removeButton = card.querySelector("button[data-remove-unit]");
        /** @type {HTMLButtonElement?} */
        const deleteButton = card.querySelector("button[data-delete-unit]");
        /** @type {HTMLElement?} */
        const amountSpan = card.querySelector("span.product-amount");
        /** @type {HTMLInputElement?} */
        const amountInput = card.querySelector(
            "input:is([name*=dishes_to_order], [name*=menus_to_order])"
        );

        const deleteCard = () => {
            card.remove();

            const formLength = form.querySelectorAll(
                "[data-cart-card-type]"
            ).length;
            if (formLength === 0) {
                form.remove();
            }

            const orderFormAmount = document.querySelectorAll(
                "[id*=place_order_restaurant]"
            ).length;
            if (orderFormAmount === 0) {
                window.location.assign("/");
            }
        };

        addButton?.addEventListener("click", async () => {
            const newCart = await updateCart(cartCardId, cartCardType);

            if (!newCart) return;

            const amount =
                newCart[cartCardType === "dish" ? "dishes" : "menus"]?.[
                    cartCardId
                ].toString() ?? "0";

            if (amountSpan) amountSpan.textContent = amount;
            if (amountInput) amountInput.value = amount;
            if (totalSpan)
                totalSpan.textContent = newCart.total[restaurantId].toFixed(2);
            if (cartBadge)
                cartBadge.dataset.badgeContent = newCart.size.toString();
        });

        removeButton?.addEventListener("click", async () => {
            const newCart = await updateCart(cartCardId, cartCardType, -1);

            if (!newCart) return;

            const amount =
                newCart[cartCardType === "dish" ? "dishes" : "menus"]?.[
                    cartCardId
                ].toString() ?? "0";

            if (amountSpan) amountSpan.textContent = amount;
            if (amountInput) amountInput.value = amount;
            if (totalSpan)
                totalSpan.textContent = newCart.total[restaurantId].toFixed(2);
            if (cartBadge)
                cartBadge.dataset.badgeContent = newCart.size.toString();

            if (amount === "0") deleteCard();
        });

        deleteButton?.addEventListener("click", async () => {
            const amount = parseInt(amountInput?.value ?? "", 10);

            const newCart = await updateCart(cartCardId, cartCardType, -amount);

            if (!newCart) return;

            if (cartBadge)
                cartBadge.dataset.badgeContent = newCart.size.toString();
            if (totalSpan)
                totalSpan.textContent = newCart.total[restaurantId].toFixed(2);

            deleteCard();
        });
    };

    cards.forEach(empowerCartCard);
};

/** @type {NodeListOf<HTMLFormElement>} */
const orderForms = document.querySelectorAll(
    "form[id*=place_order_restaurant]"
);
orderForms.forEach(empowerOrderForm);
