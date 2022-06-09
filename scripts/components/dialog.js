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
 * "Empowers" an html open dialog button using javascript
 *
 * @param {HTMLElement} btn The button to empower
 * @param {HTMLDialogElement} dialog The dialog to open
 */
export const empowerOpenDialogButton = (btn, dialog) => {
    if (btn && dialog)
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            dialog.showModal();
        });
};

/**
 * "Empowers" an html close dialog button using javascript
 *
 * @param {HTMLElement} btn The button to empower
 * @param {HTMLDialogElement} dialog The dialog to close
 */
export const empowerCloseDialogButton = (btn, dialog) => {
    if (btn && dialog) btn.addEventListener("click", closeDialog(dialog));
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

    empowerOpenDialogButton(btn, dialogToOpen);
    empowerCloseDialogButton(btn, dialogToClose);
};

export const empowerDialog = (/** @type {HTMLDialogElement} */ dialog) =>
    dialog.addEventListener("cancel", closeDialog(dialog));

/** @type {NodeListOf<HTMLDialogElement>} */
const dialogs = document.querySelectorAll("dialog.dialog");
dialogs.forEach(empowerDialog);

/** @type {NodeListOf<HTMLElement>} */
const dialogButtons = document.querySelectorAll(
    ":is([data-open-dialog], [data-close-dialog])"
);
dialogButtons.forEach(empowerDialogButton);
