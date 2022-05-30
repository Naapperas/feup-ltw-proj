// @ts-check

"use strict";

const empowerMinScoreSlider = (slider) => {

    const scoreText = slider.parentElement.querySelector("#score");

    const updateMinScore = (event) => {
        scoreText.textContent = (Math.round((event.target.value / 10) * 100) / 100).toFixed(1);
    };

    slider.addEventListener("input", updateMinScore);
}

const restaurantMinScoreSlider = document.querySelector("input[type=range][name=min_score]");

empowerMinScoreSlider(restaurantMinScoreSlider);

const empowerMinPriceSlider = (slider) => {

    const scoreText = slider.parentElement.querySelector("#price");

    const updateMinScore = (event) => {
        scoreText.textContent = `${event.target.value}€`;
    };

    slider.addEventListener("input", updateMinScore);
}

const restaurantMinPriceSlider = document.querySelector("input[type=range][name=min_price]");

empowerMinPriceSlider(restaurantMinPriceSlider);