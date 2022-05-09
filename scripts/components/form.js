// @ts-check

"use strict";

/**
 * "Empowers" an html form element using javascript
 *
 * @param {HTMLFormElement} form The form to empower
 */
const empowerForm = (form) => {
    form.noValidate = true;

    /** @type NodeListOf<HTMLFieldSetElement> */
    const sections = form.querySelectorAll("fieldset[section]");

    sections.forEach((section, i) => {
        /** @type HTMLButtonElement */
        const backButton = section.querySelector("button[back]");

        /** @type HTMLButtonElement */
        const nextButton = section.querySelector("button[next]");

        /** @type NodeListOf<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement> */
        const inputs = section.querySelectorAll("input, select, textarea");

        if (backButton && i > 0) {
            section.classList.add("hidden");
            backButton.addEventListener("click", () => {
                section.classList.add("hidden");
                sections[i - 1].classList.remove("hidden");
            });
        }

        if (nextButton && i < sections.length - 1) {
            const setButtonState = () =>
                (nextButton.disabled = !Array.prototype.reduce.call(
                    inputs,
                    (a, b) => a && b.checkValidity(),
                    true
                ));
            section.addEventListener("input", setButtonState);
            setButtonState();

            nextButton.addEventListener("click", () => {
                section.classList.add("hidden");
                sections[i + 1].classList.remove("hidden");
            });
        }
    });

    /** @type HTMLButtonElement */
    const submitButton = form.querySelector("button[type=submit]");

    /** @type NodeListOf<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement> */
    const inputs = form.querySelectorAll("input, select, textarea");

    inputs.forEach((e) => {
        const resetValidity = () => {
            e.classList.remove("error");
            e.removeEventListener("input", resetValidity);
        };

        e.addEventListener("change", () => {
            if (!e.checkValidity()) {
                e.classList.add("error");
                e.addEventListener("input", resetValidity);
            }
        });
    });

    const setButtonState = () =>
        (submitButton.disabled = !form.checkValidity());
    form.addEventListener("input", setButtonState);
    setButtonState();
};

/** @type NodeListOf<HTMLFormElement> */
const _forms = document.querySelectorAll("form[empower]");
_forms.forEach(empowerForm);
