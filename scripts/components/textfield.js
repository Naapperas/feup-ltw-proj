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
    /** @type HTMLElement */
    const characterCounter = textfield.querySelector(".character-counter");

    if (toggleVisibilityButton)
        toggleVisibilityButton.addEventListener(
            "click",
            () => (input.type = input.type === "password" ? "text" : "password")
        );

    if (characterCounter) {
        if (input.maxLength !== -1 || input.minLength !== -1) {
            const counterValue =
                input.maxLength === -1 ? input.minLength : input.maxLength;

            const eventListener = () =>
                (characterCounter.innerText = `${input.value.length}/${counterValue}`);

            input.addEventListener("input", eventListener);
            eventListener();
        } else {
            characterCounter.remove();
        }
    }
};

/** @type NodeListOf<HTMLElement> */
const _textfields = document.querySelectorAll(".textfield");
_textfields.forEach(empowerTextField);
