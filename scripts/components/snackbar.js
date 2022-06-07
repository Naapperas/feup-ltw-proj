// @ts-check

"use strict";

export const createSnackbar = (
    /** @type {string} */ snackbarText,
    /** @type {number} */ snackbarDelay = 5
) => {
    const snackbar = document.createElement("output");
    snackbar.classList.add("snackbar");
    snackbar.style.setProperty("--snackbar-delay", `${snackbarDelay}s`);
    snackbar.setAttribute("role", "status");
    snackbar.setAttribute("aria-live", "polite");
    snackbar.innerText = snackbarText;

    // const snackbarCloseButton = document.createElement("button");
    // snackbarCloseButton.classList.add("button", "icon");
    // snackbarCloseButton.type = "button";
    // snackbarCloseButton.dataset.closeSnackbar = "";
    // snackbarCloseButton.textContent = "close";

    // snackbar.appendChild(snackbarCloseButton);

    return snackbar;
};

// mark as an export to be able to create and empower snackbars somewhere else
export const empowerSnackbar = (/** @type {HTMLOutputElement} */ snackbar) => {
    // const closeButton = snackbar.querySelector("[data-close-snackbar]");

    Promise.allSettled(snackbar.getAnimations().map((a) => a.finished)).then(
        () => snackbar.remove()
    );

    // closeButton.addEventListener("click", () => {
    //     console.debug(snackbar.getAnimations());
    //     snackbar.style.setProperty("--snackbar-delay", "0ms");
    // });
};

const createSnackbarContainer = () => {
    const snackbarContainer = document.createElement("section");
    snackbarContainer.id = "snackbar-container";
    document.body.appendChild(snackbarContainer);

    return snackbarContainer;
};

/** @type {HTMLElement} */
const snackbarContainer =
    document.querySelector("#snackbar-container") ?? createSnackbarContainer();

export const addSnackbar = (
    /** @type {string} */ snackbarText,
    /** @type {number} */ snackbarDelay = 5
) => {
    const snackbar = createSnackbar(snackbarText, snackbarDelay);

    if (snackbarContainer.children.length > 0) {
        const from = snackbarContainer.offsetHeight;
        snackbarContainer.appendChild(snackbar);
        empowerSnackbar(snackbar);
        const to = snackbarContainer.offsetHeight;

        const animation = snackbarContainer.animate(
            [
                { transform: `translateY(${to - from}px)` },
                { transform: `translateY(0)` },
            ],
            { duration: 200, easing: "ease-in-out" }
        );
        animation.startTime = document.timeline.currentTime;
    } else {
        snackbarContainer.appendChild(snackbar);
        empowerSnackbar(snackbar);
    }
};

/** @type {NodeListOf<HTMLOutputElement>} */
const snackbarList = snackbarContainer.querySelectorAll("output.snackbar");
snackbarList.forEach(empowerSnackbar);
