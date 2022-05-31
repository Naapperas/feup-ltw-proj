// @ts-check

"use strict";

/**
 * "Empowers" an html dialog button using javascript
 *
 * @param {HTMLElement} btn The button to empower
 */
const empowerDialogButton = (btn) => {
    /** @type HTMLDialogElement */
    const dialogToOpen = document.querySelector(
        `dialog${btn.dataset.openDialog}`
    );
    /** @type HTMLDialogElement */
    const dialogToClose = document.querySelector(
        `dialog${btn.dataset.closeDialog}`
    );

    if (dialogToOpen)
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            dialogToOpen.showModal();
        });

    if (dialogToClose)
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            dialogToClose.dataset.closing = "";

            dialogToClose.addEventListener(
                "animationend",
                () => {
                    delete dialogToClose.dataset.closing;
                    dialogToClose.close();
                },
                { once: true }
            );
        });
};

/** @type NodeListOf<HTMLElement> */
const _dialogBtns = document.querySelectorAll(
    ":is([data-open-dialog], [data-close-dialog])"
);
_dialogBtns.forEach(empowerDialogButton);

const empowerMinScoreSlider = (slider) => {

    const scoreText = slider.parentElement.querySelector("#score");

    const updateMinScore = (event) => {
        scoreText.textContent = (Math.round((event.target.value / 10) * 100) / 100).toFixed(1);
    };

    slider.addEventListener("input", updateMinScore);
}

const restaurantMinScoreSlider = document.querySelector("input[type=range][name=min_restaurant_score]");

empowerMinScoreSlider(restaurantMinScoreSlider);

const empowerMinPriceSlider = (slider) => {

    const scoreText = slider.parentElement.querySelector("#price");

    const updateMinScore = (event) => {
        scoreText.textContent = `${(Math.round((event.target.value) * 100) / 100).toFixed(2)}€`;
    };

    slider.addEventListener("input", updateMinScore);
}

const restaurantMinPriceSlider = document.querySelector("input[type=range][name=min_dish_price]");

empowerMinPriceSlider(restaurantMinPriceSlider);
