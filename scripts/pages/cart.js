// @ts-check

"use strict";

import { addProductToCart, removeProductFromCart, removeBatchFromCart } from "../api/cart.js";

/** @type HTMLElement */
const cartBadge = document.querySelector("[data-cart]");

const orderForms = document.querySelectorAll("[id*=place_order_restaurant]");

const empowerOrderForm = (form) => {

    const dishSection = form.querySelector(".cart-dish-cards");
    const menuSection = form.querySelector(".cart-menu-cards");

    const dishCards = dishSection?.querySelectorAll("article.cart-dish-card") ?? [];
    const menuCards = menuSection?.querySelectorAll("article.cart-menu-card") ?? [];

    const empowerCartCard = (section) => (type) => (card) => {

        const { cartCardType, cartCardId } = card.dataset;

        /** @type HTMLButtonElement */
        const addButton = card.querySelector("[data-add-unit]");
        const removeButton = card.querySelector("[data-remove-unit]");
        const deleteButton = card.querySelector("[data-delete-unit]");
        const amountSpan = card.querySelector("span.product-amount");
        const amountInput = card.querySelector("input:is([name*=dishes_to_order], [name*=menus_to_order])"); // there is only going to be one of this per card
    
        const deleteCard = () => {
            card.remove();

            const sectionLength = section.querySelectorAll(`article.cart-${type}-card`).length;
            if (sectionLength === 0) {
                section.remove();
            }

            const formLength = form.querySelectorAll(":is(.cart-dish-cards, .cart-menu-cards)").length;
            if (formLength === 0) {
                form.remove();
            }

            const orderFormAmount = document.querySelectorAll("[id*=place_order_restaurant]").length;
            if (orderFormAmount === 0) {
                window.location = "/";
            }
    
        };
    
        addButton.addEventListener("click", async () => {

            await addProductToCart(cartCardId, cartCardType);

            const amount = parseInt(amountSpan.textContent, 10) + 1;

            amountSpan.textContent = amount;
    
            const cartCount = parseInt(cartBadge.dataset.badgeContent, 10) + 1;
    
            cartBadge.dataset.badgeContent = cartCount.toString();
            amountInput.value = amount;
        });
    
        removeButton.addEventListener("click", async () => {
    
            await removeProductFromCart(cartCardId, cartCardType);

            const amount = parseInt(amountSpan.textContent, 10);
    
            amountSpan.textContent = amount - 1;
    
            const cartCount = parseInt(cartBadge.dataset.badgeContent, 10) - 1;
    
            cartBadge.dataset.badgeContent = cartCount.toString();

            amountInput.value = amount;

            if (amount === 1) {
                deleteCard();
                return;
            }
        });
    
        deleteButton.addEventListener("click", async () => {

            const amount = parseInt(amountSpan.textContent, 10);
            
            await removeBatchFromCart(cartCardId, cartCardType, amount);
        
            const cartCount = parseInt(cartBadge.dataset.badgeContent, 10) - amount;
    
            cartBadge.dataset.badgeContent = cartCount.toString();

            amountInput.value = 0;

            deleteCard();
        });
    }

    dishCards.forEach(empowerCartCard(dishSection)('dish'));
    menuCards.forEach(empowerCartCard(menuSection)('menu'));
}

orderForms.forEach(empowerOrderForm);