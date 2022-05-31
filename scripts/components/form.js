// @ts-check

"use strict";

const defaultErrorMessages = Object.freeze({
    badInput: "Error: invalid input",
    patternMismatch: "Error: invalid input",
    rangeOverflow: "Error: value too high",
    rangeUnderflow: "Error: value too low",
    stepMismatch: "Error: invalid value",
    tooLong: "Error: too many characters",
    tooShort: "Error: too little characters",
    typeMismatch: "Error: invalid input",
    valueMissing: "Error: field is required",
});

/**
 * "Empowers" an html form element using javascript
 *
 * @param {HTMLFormElement} form The form to empower
 */
const empowerForm = (form) => {
    form.noValidate = true;

    /** @type NodeListOf<HTMLFieldSetElement> */
    const sections = form.querySelectorAll("fieldset[data-section]");

    sections.forEach((section, i) => {
        /** @type HTMLButtonElement */
        const backButton = section.querySelector("button[data-back]");

        /** @type HTMLButtonElement */
        const nextButton = section.querySelector("button[data-next]");

        /** @type NodeListOf<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement> */
        const inputs = section.querySelectorAll("input, select, textarea");

        if (backButton && i > 0) {
            section.classList.add("hidden");
            section.ariaHidden = "true";
            section
                .querySelectorAll("input, select, textarea, button")
                // @ts-ignore
                .forEach((i) => (i.tabIndex = -1));

            backButton.addEventListener("click", () => {
                section.classList.add("hidden");
                section.ariaHidden = "true";
                section
                    .querySelectorAll("input, select, textarea, button")
                    // @ts-ignore
                    .forEach((i) => (i.tabIndex = -1));

                sections[i - 1].classList.remove("hidden");
                sections[i - 1].ariaHidden = "false";
                sections[i - 1]
                    .querySelectorAll("input, select, textarea, button")
                    // @ts-ignore
                    .forEach((i) => (i.tabIndex = 0));
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
                section.ariaHidden = "true";
                section
                    .querySelectorAll("input, select, textarea, button")
                    // @ts-ignore
                    .forEach((i) => (i.tabIndex = -1));

                sections[i + 1].classList.remove("hidden");
                sections[i + 1].ariaHidden = "false";

                /** @type NodeListOf<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement | HTMLButtonElement> */
                const fields = sections[i + 1].querySelectorAll(
                    "input, select, textarea, button"
                );
                fields.forEach((i) => (i.tabIndex = 0));
                fields[0].focus();
            });
        }
    });

    /** @type HTMLButtonElement */
    const submitButton = form.querySelector("button[type=submit]");

    /** @type NodeListOf<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement> */
    const inputs = form.querySelectorAll("input, select, textarea");

    inputs[0].focus();

    inputs.forEach((e) => {
        try {
            /** @type HTMLElement */
            const errorText = document.querySelector(`#${e.dataset.errorText}`);

            const resetValidity = () => {
                if (errorText) errorText.textContent = "";

                e.classList.remove("error");
                e.removeEventListener("input", resetValidity);
            };

            e.addEventListener("blur", () => {
                if (!e.checkValidity()) {
                    if (errorText)
                        for (let p in e.validity)
                            if (e.validity[p]) {
                                errorText.textContent =
                                    errorText.dataset[p] ||
                                    defaultErrorMessages[p];
                                break;
                            }

                    e.classList.add("error");
                    e.addEventListener("input", resetValidity);
                }
            });
        } catch {}
    });

    const setButtonState = () =>
        (submitButton.disabled = !form.checkValidity());
    form.addEventListener("input", setButtonState);
    setButtonState();
};

/** @type NodeListOf<HTMLFormElement> */
const _forms = document.querySelectorAll("form[data-empower]");
_forms.forEach(empowerForm);
