// @ts-check

"use strict";

/** @type HTMLButtonElement */
const submitButton = document.querySelector("form > button[type=submit]");
/** @type HTMLInputElement */
const usernameInput = document.querySelector("#username");
/** @type HTMLInputElement */
const passwordInput = document.querySelector("#password");

const eventListener = () =>
    (submitButton.disabled = !(
        usernameInput.value.length && passwordInput.value.length
    ));

usernameInput.addEventListener("input", eventListener);
passwordInput.addEventListener("input", eventListener);

submitButton.disabled = true;
