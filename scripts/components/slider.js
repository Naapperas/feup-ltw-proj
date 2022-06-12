// @ts-check

"use strict";

/**
 *
 * @param {HTMLElement} slider
 */
const empowerSliderNew = (slider) => {
    /** @type {NodeListOf<HTMLInputElement>} */
    let [inputMin, inputMax] = slider.querySelectorAll('input[type="range"]');
    /** @type {HTMLElement?} */
    const preview = slider.querySelector("[data-slider-preview]");
    const isRanged = inputMax !== undefined;

    if (isRanged) {
        inputMax.min = inputMin.min;
        inputMax.step = inputMin.step;
        inputMax.max = inputMin.max;
    }

    slider.style.setProperty("--slider-min", inputMin.min);
    slider.style.setProperty("--slider-max", inputMin.max);

    const eventListener = () => {
        if (isRanged) {
            if (parseFloat(inputMin.value) > parseFloat(inputMax.value)) {
                [inputMin.name, inputMax.name] = [inputMax.name, inputMin.name];
                [inputMin.id, inputMax.id] = [inputMax.id, inputMin.id];
                [inputMin, inputMax] = [inputMax, inputMin];
            }
        }

        slider.style.setProperty(
            "--slider-a",
            isRanged ? inputMin.value : inputMin.min
        );
        slider.style.setProperty(
            "--slider-b",
            isRanged ? inputMax.value : inputMin.value
        );

        if (preview) {
            preview.textContent = isRanged
                ? `${inputMin.value} to ${inputMax.value}`
                : inputMin.value;
        }
    };

    inputMin?.addEventListener("input", eventListener);
    inputMax?.addEventListener("input", eventListener);

    eventListener();
};

/** @type NodeListOf<HTMLElement> */
const sliders = document.querySelectorAll(":not(input).slider");
sliders.forEach(empowerSliderNew);
