// @ts-check

"use strict";

let _colorSchemeLocalValue = localStorage.getItem("color-scheme");

if (_colorSchemeLocalValue === "dark" || _colorSchemeLocalValue === "light") {
    document.documentElement.setAttribute(
        "color-scheme",
        _colorSchemeLocalValue
    );
} else _colorSchemeLocalValue = null;

const _colorSchemeMatch = matchMedia("(prefers-color-scheme: dark)");

const _updateColorScheme = () =>
    document.documentElement.setAttribute(
        "color-scheme",
        _colorSchemeLocalValue ?? (_colorSchemeMatch.matches ? "dark" : "light")
    );

const _colorSchemeToggleEventListener = (e) => {
    if (_colorSchemeLocalValue === "dark") _colorSchemeLocalValue = "light";
    else if (_colorSchemeLocalValue === "light")
        _colorSchemeLocalValue = "dark";
    else if (_colorSchemeMatch) _colorSchemeLocalValue = "light";
    else _colorSchemeLocalValue = "dark";

    localStorage.setItem("color-scheme", _colorSchemeLocalValue);
    _updateColorScheme();
};

window.addEventListener("storage", (e) => {
    if (
        e.key == "color-scheme" &&
        (e.newValue === "dark" || e.newValue === "light")
    ) {
        _colorSchemeLocalValue = e.newValue;
        _updateColorScheme();
    }
});

/**
 * "Empowers" an html color scheme toggle using javascript.
 *
 * Toggles color scheme on click.
 *
 * @param {HTMLElement} colorSchemeToggle
 * @returns {void}
 */
const empowerColorSchemeToggle = (colorSchemeToggle) =>
    colorSchemeToggle.addEventListener(
        "click",
        _colorSchemeToggleEventListener
    );

/** @type NodeListOf<HTMLElement> */
const _colorSchemeToggles = document.querySelectorAll(".color-scheme-toggle");
_colorSchemeToggles.forEach(empowerColorSchemeToggle);

_colorSchemeMatch.addEventListener("change", _updateColorScheme);
_updateColorScheme();
