// @ts-check

"use strict";

/**
 * "Empowers" an html textfield element using javascript.
 *
 * Enables toggling visibility on password fields that have a toggle visibility
 * button.
 *
 * @param {HTMLElement} textfield
 */
export const empowerTextField = (textfield) => {
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

/**
 * Creates a new textfield element.
 *
 * @param {string} label
 * @param {string} name
 * @param {Object} inputAttributes
 */
export const createTextField = (label, name, inputAttributes) => {
    const wrapper = document.createElement("div");
    wrapper.classList.add("textfield");

    const input = document.createElement("input");
    input.placeholder = " ";
    for (const i in inputAttributes) input.setAttribute(i, inputAttributes[i]);
    input.id = name;
    input.name = name;

    const labelEl = document.createElement("label");
    labelEl.htmlFor = name;
    labelEl.textContent = label;

    wrapper.appendChild(input);
    wrapper.appendChild(labelEl);

    return wrapper;
};

/** @type NodeListOf<HTMLElement> */
const textfields = document.querySelectorAll(".textfield");
textfields.forEach(empowerTextField);
