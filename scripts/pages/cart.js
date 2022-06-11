// @ts-check

"use strict";

import { updateCart } from "../api/cart.js";

/** @type HTMLElement */
const cartBadge = document.querySelector("[data-cart]");

const empowerOrderForm = (/** @type {HTMLFormElement} */ form) => {
    /** @type {HTMLElement} */
    const dishSection = form.querySelector(".cart-dish-cards");
    /** @type {HTMLElement} */
    const menuSection = form.querySelector(".cart-menu-cards");

    /** @type {NodeListOf<HTMLElement> | []} */
    const dishCards =
        dishSection?.querySelectorAll("[data-cart-card-type=dish]") ?? [];
    /** @type {NodeListOf<HTMLElement> | []} */
    const menuCards =
        menuSection?.querySelectorAll("[data-cart-card-type=menu]") ?? [];

    const empowerCartCard =
        (/** @type {HTMLElement} */ section) =>
        (/** @type {HTMLElement} */ card) => {
            const { cartCardType } = card.dataset;
            const cartCardId = parseInt(card.dataset.cartCardId, 10);

            if (cartCardType !== "menu" && cartCardType !== "dish") return;

            /** @type {HTMLButtonElement} */
            const addButton = card.querySelector("button[data-add-unit]");
            /** @type {HTMLButtonElement} */
            const removeButton = card.querySelector("button[data-remove-unit]");
            /** @type {HTMLButtonElement} */
            const deleteButton = card.querySelector("button[data-delete-unit]");
            /** @type {HTMLElement} */
            const amountSpan = card.querySelector("span.product-amount");
            /** @type {HTMLInputElement} */
            const amountInput = card.querySelector(
                "input:is([name*=dishes_to_order], [name*=menus_to_order])"
            );

            const deleteCard = () => {
                card.remove();

                const sectionLength = section.querySelectorAll(
                    `[data-cart-card-type=${cartCardType}]`
                ).length;
                if (sectionLength === 0) {
                    section.remove();
                }

                const formLength = form.querySelectorAll(
                    ":is(.cart-dish-cards, .cart-menu-cards)"
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

            addButton.addEventListener("click", async () => {
                const newCart = await updateCart(cartCardId, cartCardType);
                const amount =
                    newCart[cartCardType === "dish" ? "dishes" : "menus"][
                        cartCardId
                    ].toString();

                amountSpan.textContent = amount;
                amountInput.value = amount;
                cartBadge.dataset.badgeContent = newCart.size.toString();
            });

            removeButton.addEventListener("click", async () => {
                const newCart = await updateCart(cartCardId, cartCardType, -1);
                const amount =
                    newCart[cartCardType === "dish" ? "dishes" : "menus"][
                        cartCardId
                    ].toString();

                amountSpan.textContent = amount;
                amountInput.value = amount;
                cartBadge.dataset.badgeContent = newCart.size.toString();

                if (amount === "0") deleteCard();
            });

            deleteButton.addEventListener("click", async () => {
                const amount = parseInt(amountInput.value, 10);

                const newCart = await updateCart(
                    cartCardId,
                    cartCardType,
                    -amount
                );

                deleteCard();
            });
        };

    dishCards.forEach(empowerCartCard(dishSection));
    menuCards.forEach(empowerCartCard(menuSection));
};

/** @type {NodeListOf<HTMLFormElement>} */
const orderForms = document.querySelectorAll(
    "form[id*=place_order_restaurant]"
);
orderForms.forEach(empowerOrderForm);
