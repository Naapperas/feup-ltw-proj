// @ts-check

"use strict";

import { createNewDishCard, createNewMenuCard } from "../components/card.js";
import { empowerEditCategoryList } from "../components/categorylist.js";

/** @type {HTMLElement?} */
const dishList = document.querySelector(".dish-list");
/** @type {HTMLElement?} */
const newDishButtonCard =
    dishList && dishList.querySelector(":scope > :last-child");
/** @type {HTMLButtonElement?} */
const newDishButton = document.querySelector("button[data-new-dish-button]");

let newDishIndex = 0;

newDishButton?.addEventListener("click", () => {
    const newDishCard = createNewDishCard(newDishIndex++);

    if (newDishButtonCard) newDishButtonCard.before(newDishCard);
});

/** @type {HTMLElement?} */
const menuList = document.querySelector(".menu-list");
/** @type {HTMLElement?} */
const newMenuButtonCard =
    menuList && menuList.querySelector(":scope > :last-child");
/** @type {HTMLButtonElement?} */
const newMenuButton = document.querySelector("button[data-new-menu-button]");

let newMenuIndex = 0;

newMenuButton?.addEventListener("click", () => {
    const newMenuCard = createNewMenuCard(newMenuIndex++);

    newMenuButtonCard?.before(newMenuCard);
});

/** @type {HTMLElement?} */
const chipList = document.querySelector("ul.chip-list");
/** @type {HTMLFieldSetElement?} */
const categoriesFieldset = document.querySelector(`#categories fieldset`);

if (chipList && categoriesFieldset)
    empowerEditCategoryList(chipList, categoriesFieldset);
