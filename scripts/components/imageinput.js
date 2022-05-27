// @ts-check

"use strict";

/**
 * "Empowers" an html image input using javascript.
 *
 * Shows a preview of the image and supports dragging and dropping images.
 *
 * @param {HTMLElement} imageInput
 */
const empowerImageInput = (imageInput) => {
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

/** @type NodeListOf<HTMLLabelElement> */
const _imageInputs = document.querySelectorAll("label.image-input");
_imageInputs.forEach(empowerImageInput);
