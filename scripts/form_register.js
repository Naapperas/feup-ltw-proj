// @ts-check

"use strict";

/** @type HTMLButtonElement */
const submitButton = document.querySelector("form > button[type=submit]");
/** @type HTMLInputElement */
const emailInput = document.querySelector("#email");
/** @type HTMLInputElement */
const usernameInput = document.querySelector("#username");
/** @type HTMLInputElement */
const passwordInput = document.querySelector("#password");

const eventListener = () =>
    (submitButton.disabled = !(
        emailInput.value.length &&
        usernameInput.value.length &&
        passwordInput.value.length
    ));

emailInput.addEventListener("input", eventListener);
usernameInput.addEventListener("input", eventListener);
passwordInput.addEventListener("input", eventListener);

submitButton.disabled = true;
