// @ts-check

"use strict";

export const createSnackbar = (/** @type {string} */ snackbarText) => {
    const snackbar = document.createElement('output');
    snackbar.classList.add('snackbar');

    const snackbarTextElement = document.createTextNode(snackbarText);

    const snackbarCloseButton = document.createElement('button');
    snackbarCloseButton.classList.add('button', 'icon');
    snackbarCloseButton.type = 'button';
    snackbarCloseButton.dataset.closeSnackbar = '';
    snackbarCloseButton.textContent = 'close';

    snackbar.appendChild(snackbarTextElement);
    snackbar.appendChild(snackbarCloseButton);

    return snackbar;
};

// mark as an export to be able to create and empower snackbars somewhere else
export const empowerSnackbar = (snackbar) => {

    const closeButton = snackbar.querySelector('[data-close-snackbar]');

    const dismissSnackbar = () => {
        snackbar.classList.add('exit');
        setTimeout(() => snackbar.remove(), 1750);
    }

    // const timeout = setTimeout(dismissSnackbar, 5000);
    closeButton.addEventListener('click', () => {
        // clearTimeout(timeout);
        dismissSnackbar();
    });
};

const snackbarContainer = document.querySelector('#snackbar-container');

export const addSnackbar = (/** @type {string|HTMLOutputElement} */snackbar) => {

    snackbar = typeof snackbar === 'string' ? createSnackbar(snackbar) : snackbar;

    snackbarContainer.appendChild(snackbar);
    empowerSnackbar(snackbar);
}

const snackbarList = snackbarContainer.querySelectorAll('output.snackbar');

snackbarList.forEach(empowerSnackbar);