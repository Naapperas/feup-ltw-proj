// @ts-check

"use strict";

const closeDialog = (/** @type {HTMLDialogElement} */ dialog) => (e) => {
    e.preventDefault();
    dialog.dataset.closing = "";

    dialog.addEventListener(
        "animationend",
        () => {
            delete dialog.dataset.closing;
            dialog.close();
        },
        { once: true }
    );
};

/**
 * "Empowers" an html dialog button using javascript
 *
 * @param {HTMLElement} btn The button to empower
 */
export const empowerDialogButton = (btn) => {
    /** @type {HTMLDialogElement} */
    const dialogToOpen = document.querySelector(
        `dialog${btn.dataset.openDialog}`
    );
    /** @type {HTMLDialogElement} */
    const dialogToClose = document.querySelector(
        `dialog${btn.dataset.closeDialog}`
    );

    if (dialogToOpen)
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            dialogToOpen.showModal();
        });

    if (dialogToClose)
        btn.addEventListener("click", closeDialog(dialogToClose));
};

/** @type {NodeListOf<HTMLDialogElement>} */
const dialogs = document.querySelectorAll("dialog.dialog");
dialogs.forEach((d) => d.addEventListener("cancel", closeDialog(d)));

/** @type {NodeListOf<HTMLElement>} */
const dialogButtons = document.querySelectorAll(
    ":is([data-open-dialog], [data-close-dialog])"
);
dialogButtons.forEach(empowerDialogButton);
