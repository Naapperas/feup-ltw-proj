// @ts-check

"use strict";

/**
//  * Creates an svg image to be used as the background of a slider element.
//  *
//  * @param {number} width The width of the slider element
//  * @param {number} percentage The slider progress percentage
//  * @param {string} fill The slider fill color
//  * @param {number} step The slider step percentage
//  * @returns {string} The svg background
//  */
// const _getSliderBackgroundSVG = (width, percentage, fill, step) => {
//     let circles = "";
//     let progressCircles = "";

//     if (step) {
//         for (let i = percentage; i <= 1; i += step)
//             circles += `
//     <circle r='1' cy='3' cx='${10 + (width - 20) * i}' fill='rgb(${fill})' />
// `.trim();
//         for (let i = 0; i < percentage; i += step)
//             progressCircles += `
//     <circle r='1' cy='3' cx='${10 + (width - 20) * i}' fill='black' />
// `.trim();
//     }

//     return (
//         "data:image/svg+xml," +
//         encodeURIComponent(
//             `
// <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 ${width} 6'>
//     <mask id="mask">
//         <rect x='0' y='0' width="${width}" height="6" fill="white" />
//         ${progressCircles}
//     </mask>

//     <rect x='7' y='0' width='${
//         (width - 14) * percentage
//     }' height='6' rx='3' ry='3' fill='rgb(${fill})' mask="url(#mask)"/>
//     <rect x='8' y='1' width='${
//         width - 16
//     }' height='4' rx='2' ry='2' fill='rgba(${fill}, .38)'/>
//     ${circles}
// </svg>
// `.trim()
//         )
//     );
// };

// /**
//  * Creates the css that styles a slider element.
//  *
//  * @param {HTMLInputElement} slider The slider to style
//  * @returns {Promise<string>} The css
//  */
// const _getSliderCSS = async (slider) => {
//     const background = _getSliderBackgroundSVG(
//         slider.offsetWidth,
//         parseInt(slider.value) / parseInt(slider.max),
//         getComputedStyle(slider).getPropertyValue(
//             slider.disabled ? "--color-on-surface" : "--color-main"
//         ),
//         parseInt(slider.step) / parseInt(slider.max)
//     );

//     // Fixes flickering on firefox
//     const img = new Image();
//     img.src = background;
//     await img.decode();

//     return `
// #${slider.id}::-webkit-slider-runnable-track {
//     background-image: url("${background}");
// }

// #${slider.id}::-moz-range-track {
//     background-image: url("${background}");
// }
// `.trim();
// };

// /**
//  * "Empowers" an html slider element using javascript.
//  *
//  * Creates a new style element to fix inconsistencies between browsers.
//  *
//  * @param {HTMLInputElement} slider The slider to empower
//  */
// const empowerSlider = (slider) => {
//     // There may be a better way to do this
//     const s = document.createElement("style");
//     document.head.appendChild(s);

//     const eventListener = async () =>
//         (s.innerHTML = await _getSliderCSS(slider));
//     slider.addEventListener("input", eventListener);
//     window.addEventListener("resize", eventListener);
//     eventListener();
// };

// /** @type NodeListOf<HTMLInputElement> */
// const _sliders = document.querySelectorAll("input[type=range][id].slider");
// _sliders.forEach(empowerSlider);

/**
 *
 * @param {HTMLElement} slider
 */
const empowerSliderNew = (slider) => {
    /** @type NodeListOf<HTMLInputElement> */
    let [inputMin, inputMax] = slider.querySelectorAll('input[type="range"]');
    /** @type HTMLElement */
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
