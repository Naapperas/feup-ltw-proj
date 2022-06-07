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
 * @param {boolean} errorText
 */
export const createTextField = (
    label,
    name,
    inputAttributes,
    errorText = true
) => {
    const wrapper = document.createElement("div");
    wrapper.classList.add("textfield");

    const input = document.createElement("input");
    input.placeholder = " ";
    for (const i in inputAttributes) input.setAttribute(i, inputAttributes[i]);
    input.id = name.replace(/\[/g, "-").replace(/]/g, "");
    input.name = name;

    const labelEl = document.createElement("label");
    labelEl.htmlFor = input.id;
    labelEl.textContent = label;

    wrapper.appendChild(input);
    wrapper.appendChild(labelEl);

    if (errorText) {
        input.setAttribute("aria-describedby", `${input.id}-error-text`);
        input.dataset.errorText = `${input.id}-error-text`;

        const errorTextEl = document.createElement("span");
        errorTextEl.classList.add("error-text");
        errorTextEl.setAttribute("aria-live", "assertive");
        errorTextEl.id = `${input.id}-error-text`;

        wrapper.appendChild(errorTextEl);
    }

    empowerTextField(wrapper);

    return wrapper;
};

/** @type NodeListOf<HTMLElement> */
const textfields = document.querySelectorAll(".textfield");
textfields.forEach(empowerTextField);
