// @ts-check

"use strict";

/**
 * "Empowers" an html textfield element using javascript.
 *
 *  Enables toggling visibility on password fields that have a toggle visibility
 *  button.
 *
 * @param {HTMLElement} textfield
 */
const empowerTextField = (textfield) => {
    /** @type HTMLInputElement */
    const input = textfield.querySelector("input");
    /** @type HTMLButtonElement */
    const toggleVisibilityButton = textfield.querySelector(
        "button.toggle-visible"
    );

    if (toggleVisibilityButton)
        toggleVisibilityButton.addEventListener(
            "click",
            () => (input.type = input.type === "password" ? "text" : "password")
        );
};

/** @type NodeListOf<HTMLElement> */
const _textfields = document.querySelectorAll(".textfield");
_textfields.forEach(empowerTextField);
