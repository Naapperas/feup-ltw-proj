// @ts-check

"use strict";

/**
 * "Empowers" an html image input using javascript.
 *
 * Shows a preview of the image and supports dragging and dropping images.
 *
 * @param {HTMLElement} imageInput
 */
export const empowerImageInput = (imageInput) => {
    /** @type HTMLInputElement */
    const input = imageInput.querySelector(
        'input[type="file"][accept^="image/"]'
    );
    /** @type HTMLImageElement */
    const image = imageInput.querySelector("img");

    const updatePreview = () => {
        const reader = new FileReader();

        reader.addEventListener(
            "load",
            () => (image.src = reader.result.toString())
        );

        reader.readAsDataURL(input.files[0]);
    };

    const cancelEvent = (e) => {
        e.stopPropagation();
        e.preventDefault();
    };
    imageInput.addEventListener("dragenter", cancelEvent);
    imageInput.addEventListener("dragover", cancelEvent);
    imageInput.addEventListener("drop", (e) => {
        cancelEvent(e);

        const files = e.dataTransfer.files;
        if (files.length === 1 && files[0].type.startsWith("image/")) {
            input.files = files;
            updatePreview();
        }
    });

    input.addEventListener("change", updatePreview);
};

/**
 * Creates a new image input.
 *
 * @param {string} name
 * @param {string} src
 * @param {string[]} wrapperClasses
 * @param {string[]} previewClasses
 */
export const createImageInput = (name, src, wrapperClasses, previewClasses) => {
    const imageInput = document.createElement("label");
    imageInput.classList.add("image-input", ...wrapperClasses);

    const imagePreview = document.createElement("img");
    imagePreview.classList.add(...previewClasses);
    imagePreview.src = src;

    const input = document.createElement("input");
    input.classList.add("visually-hidden");
    input.type = "file";
    input.accept = "image/*";
    input.name = name;

    imageInput.appendChild(imagePreview);
    imageInput.appendChild(input);

    return imageInput;
};

/** @type NodeListOf<HTMLLabelElement> */
const imageInputs = document.querySelectorAll("label.image-input");
imageInputs.forEach(empowerImageInput);
