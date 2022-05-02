"use strict";

// Sliders suck.

const getSliderBackgroundSVG = (width, percentage, fill) =>
    "data:image/svg+xml," +
    encodeURIComponent(
        `
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 ${width} 6'>
    <rect x='7' y='0' width='${
        (width - 14) * percentage
    }' height='6' rx='3' ry='3' fill='rgba(${fill})'/>
    <rect x='8' y='1' width='${
        width - 16
    }' height='4' rx='2' ry='2' fill='rgba(${fill}, .38)'/>
</svg>
`.trim()
    );

const getSliderCSS = (slider) => {
    const background = getSliderBackgroundSVG(
        slider.offsetWidth,
        slider.value / slider.max,
        getComputedStyle(slider).getPropertyValue("--tcolor-primary")
    );

    return `
#${slider.id}::-webkit-slider-runnable-track {
    background-image: url("${background}");
}

#${slider.id}::-moz-range-track {
    background-image: url("${background}");
}
`.trim();
};

document.querySelectorAll("input[type=range][id].slider").forEach((slider) => {
    // There may be a better way to do this
    const s = document.createElement("style");
    document.body.appendChild(s);

    const eventListener = (e) => (s.innerHTML = getSliderCSS(slider));
    slider.addEventListener("input", eventListener);
    window.addEventListener("resize", eventListener);
    eventListener();
});
