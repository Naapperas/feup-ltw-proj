// @ts-check

"use strict";

/**
 * Creates a dish item.
 *
 * @param {string} dish
 * @param {number} id
 */
export const createDishItem = (dish, id) => {
    const item = document.createElement("li");

    item.textContent = dish;
    item.dataset.dishId = id.toString(10);

    return item;
};

export const empowerEditDishList = (dishList, dishesFieldset) => {
    dishesFieldset?.addEventListener("input", (e) => {
        /** @type {HTMLInputElement} */
        // @ts-ignore
        const target = e.target;

        const id = parseInt(target.value);
        const label = target.labels?.[0].textContent;
        const checked = target.checked;

        const chip = dishList.querySelector(`li[data-dish-id="${id}"]`);

        if (chip && !checked) {
            chip.remove();
        } else if (!chip && checked) {
            const item = createDishItem(label ?? "", id);
            dishList.appendChild(item);
        }
    });
};
