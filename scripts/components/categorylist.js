// @ts-check

"use strict";

/**
 * Creates a category chip.
 *
 * @param {string} category
 * @param {number} id
 */
export const createCategoryChip = (category, id) => {
    const chip = document.createElement("li");

    chip.classList.add("chip");
    chip.textContent = category;
    chip.dataset.categoryId = id.toString(10);

    return chip;
};

export const empowerEditCategoryList = (chipList, categoriesFieldset) => {
    categoriesFieldset?.addEventListener("input", (e) => {
        /** @type {HTMLInputElement} */
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
};
