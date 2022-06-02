// @ts-check

"use strict";

import { createNewDishCard, createNewMenuCard } from "../components/card.js";

/**
 * Creates a category chip.
 *
 * @param {string} category
 * @param {number} id
 */
const createCategoryChip = (category, id) => {
    const chip = document.createElement("li");

    chip.classList.add("chip");
    chip.textContent = category;
    chip.dataset.categoryId = id.toString(10);

    return chip;
};

/** @type NodeListOf<HTMLElement> */
const chipLists = document.querySelectorAll("a[data-open-dialog]");

chipLists.forEach((chipList) => {
    /** @type HTMLElement */
    const list = chipList.querySelector("ul.chip-list");
    /** @type HTMLFieldSetElement */
    const fieldset = document.querySelector(
        `${chipList.dataset.openDialog} fieldset`
    );

    fieldset.addEventListener("input", (e) => {
        /** @type HTMLInputElement */
        // @ts-ignore
        const target = e.target;

        const id = parseInt(target.value);
        const label = target.labels[0].textContent;
        const checked = target.checked;

        const chip = list.querySelector(`li[data-category-id="${id}"]`);

        if (chip && !checked) {
            chip.remove();
        } else if (!chip && checked) {
            const chip = createCategoryChip(label, id);
            list.appendChild(chip);
        }
    });
});

/** @type HTMLElement */
const dishList = document.querySelector(".dish-list");
/** @type HTMLElement */
const newDishButtonCard = dishList.querySelector(":scope > :last-child");
/** @type HTMLButtonElement */
const newDishButton = document.querySelector("button[data-new-dish-button]");

let newDishIndex = 0;

newDishButton.addEventListener("click", () => {
    const newDishCard = createNewDishCard(newDishIndex++);

    console.debug(newDishButtonCard, newDishCard);

    newDishButtonCard.before(newDishCard);
});

/** @type HTMLElement */
const menuList = document.querySelector(".menu-list");
/** @type HTMLElement */
const newMenuButtonCard = menuList.querySelector(":scope > :last-child");
/** @type HTMLButtonElement */
const newMenuButton = document.querySelector("button[data-new-menu-button]");

let newMenuIndex = 0;

newMenuButton.addEventListener("click", () => {
    const newMenuCard = createNewMenuCard(newMenuIndex++);

    console.debug(newMenuButtonCard, newMenuCard);

    newMenuButtonCard.before(newMenuCard);
});
