"use strict";

// Sliders suck.

const getSliderBackgroundSVG = (width, percentage, fill, step) => {
    let circles = "";
    let progressCircles = "";

    if (step) {
        for (let i = percentage; i <= 1; i += step)
            circles += `
    <circle r='1' cy='3' cx='${10 + (width - 20) * i}' fill='rgb(${fill})' />
`.trim();
        for (let i = 0; i < percentage; i += step)
            progressCircles += `
    <circle r='1' cy='3' cx='${10 + (width - 20) * i}' fill='black' />
`.trim();
    }

    return (
        "data:image/svg+xml," +
        encodeURIComponent(
            `
<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 ${width} 6'>
    <mask id="mask">
        <rect x='0' y='0' width="${width}" height="6" fill="white" />
        ${progressCircles}
    </mask>

    <rect x='7' y='0' width='${
        (width - 14) * percentage
    }' height='6' rx='3' ry='3' fill='rgb(${fill})' mask="url(#mask)"/>
    <rect x='8' y='1' width='${
        width - 16
    }' height='4' rx='2' ry='2' fill='rgba(${fill}, .38)'/>
    ${circles}
</svg>
`.trim()
        )
    );
};

const getSliderCSS = async (slider) => {
    const background = getSliderBackgroundSVG(
        slider.offsetWidth,
        slider.value / slider.max,
        getComputedStyle(slider).getPropertyValue(
            slider.disabled ? "--tcolor-on-surface" : "--tcolor-primary"
        ),
        slider.step / slider.max
    );

    // Fixes flickering on firefox
    const img = new Image();
    img.src = background;
    await img.decode();

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

    const eventListener = async (e) =>
        (s.innerHTML = await getSliderCSS(slider));
    slider.addEventListener("input", eventListener);
    window.addEventListener("resize", eventListener);
    eventListener();
});
