// @ts-check

"use strict";

import { createNewDishCard } from "../components/card.js";

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

/** @type HTMLElement */
const chipList = document.querySelector(
    'a[data-open-dialog="#categories"] > ul.chip-list'
);
/** @type HTMLFieldSetElement */
const categoriesFieldSet = document.querySelector("#categories fieldset");

categoriesFieldSet.addEventListener("input", (e) => {
    /** @type HTMLInputElement */
    // @ts-ignore
    const target = e.target;

    const id = parseInt(target.value);
    const label = target.labels[0].textContent;
    const checked = target.checked;

    const chip = chipList.querySelector(`li[data-category-id="${id}"]`);

    if (chip && !checked) {
        chip.remove();
    } else if (!chip && checked) {
        const chip = createCategoryChip(label, id);
        chipList.appendChild(chip);
    }
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
