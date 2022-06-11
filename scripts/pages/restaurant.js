// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { fetchOrderedRestaurantReviews, fetchReview } from "../api/review.js";
import { fetchReviewResponse } from "../api/response.js";
import { fetchUser } from "../api/user.js";

/**
 * "Empowers" a restaurant page's like button using javascript.
 *
 * @param {HTMLElement} button
 */
const empowerRestaurantLikeButton = (button) => {
    if (!button) return;

    const restaurantId = button.dataset.restaurantId;

    const toggleLikedStatus = async (event) => {
        event?.preventDefault();

        try {
            const favorite = await toggleRestaurantLikedStatus(restaurantId);

            if (favorite === undefined) return;

            button.dataset.toggleState = favorite ? "on" : "off";
            button.ariaLabel = favorite ? "Unfavorite" : "Favorite";
        } catch {
            return;
        }
    };

    button.addEventListener("click", toggleLikedStatus);
};

const createReview = async (review) => {
    try {
        const user = await fetchUser(review.client);

        if (!user) return;

        const {
            id: userId,
            name: userName,
            image: userPhotoPath,
        } = user;

        const reviewElement = document.createElement("article");
        reviewElement.classList.add("review");
        reviewElement.dataset.reviewId = review.id;

        const reviewHeaderLink = document.createElement("a");
        reviewHeaderLink.href = `/profile/?id=${userId}`;

        const reviewHeader = document.createElement("header");
        reviewHeader.classList.add("header");

        const profilePicture = document.createElement("img");
        profilePicture.src = userPhotoPath;
        profilePicture.alt = `Review profile image for user ${userId}`;
        profilePicture.classList.add("avatar", "small");

        const profileName = document.createElement("span");
        profileName.classList.add("title");
        profileName.textContent = userName;

        const reviewDate = document.createElement("span");
        reviewDate.classList.add("subtitle", "secondary");
        reviewDate.textContent = new Date(
            review.review_date
        ).toLocaleDateString('pt-PT');

        const scoreSpan = document.createElement("span");
        scoreSpan.classList.add("chip", "right");

        const iconSpan = document.createElement("span");
        iconSpan.classList.add("icon");
        iconSpan.textContent = "star";

        const scoreText = document.createTextNode(`${review.score.toFixed()}`);

        scoreSpan.append(iconSpan, scoreText);

        reviewHeader.append(profilePicture, profileName, reviewDate, scoreSpan);

        reviewHeaderLink.appendChild(reviewHeader);

        reviewElement.appendChild(reviewHeaderLink);

        const reviewText = document.createElement("p");
        reviewText.classList.add("review-content");
        // this is safe to use here because this text comes from stored
        // reviews, which can only be created through the given action which
        // uses StringParams that escape the given text
        reviewText.innerHTML = review.text;

        reviewElement.appendChild(reviewText);

        empowerReview(reviewElement);

        return reviewElement;
    } catch {
        return;
    }
};

const empowerOrderSelect = (select) => {
    /** @type HTMLElement */
    const reviewList = document.querySelector("#review-list");

    const handleInputChange = async (e) => {
        e?.preventDefault();

        const restaurantId =
            e.target.parentElement.parentElement.parentElement.dataset
                .restaurantId;

        const reviewOrdering = e.target.value;

        const [attribute, order] = reviewOrdering.split("-");

        const reviews = await fetchOrderedRestaurantReviews(
            restaurantId,
            attribute,
            order
        );

        const nodes = await Promise.all(reviews.map(createReview));

        // .replaceChildren didn't work, this did
        for (let i = 0; i < nodes.length; i++) {
            const elem = reviewList.children[i];

            reviewList.replaceChild(nodes[i], elem);
        }
    };

    select.addEventListener("change", handleInputChange);
};

const empowerReview = (reviewElement) => {
    reviewElement.addEventListener("click", async () => {
        // do this way so we can run custom code with this specific dialog
        /** @type {HTMLDialogElement} */
        const dialog = document.querySelector(
            "#review-response[data-owner-logged-in]"
        );

        if (!dialog) return; // the user is not the restaurant owner

        const { reviewId } = reviewElement.dataset;

        const review = await fetchReview(reviewId);
        const reviewResponse = await fetchReviewResponse(reviewId);

        const reviewNode = await createReview(review);

        dialog
            .querySelector("div.content > section#response-review")
            ?.replaceChildren(reviewNode);

        // TODO: this is kinda monkey-patched, figure out a way of doing
        // this right

        /** @type {HTMLButtonElement} */
        const submitButton = dialog.querySelector("[type=submit]");
        submitButton.disabled = false;
        /** @type {HTMLTextAreaElement} */
        const textArea = dialog.querySelector("textarea");
        textArea.disabled = false;
        // this does not trigger HTML re-parsing
        /** @type {HTMLElement} */
        const text = dialog.querySelector("#response-text");
        text.textContent = "";
        if (reviewResponse !== null) {
            submitButton.disabled = true;
            textArea.disabled = true;
            text.appendChild(document.createTextNode(reviewResponse.text));
        } else {
            /** @type {HTMLInputElement} */
            const hiddenInput = dialog.querySelector(
                "input[type=hidden][name=reviewId]"
            );
            hiddenInput.value = review.id;
        }

        dialog.showModal();
    });
};

/** @type HTMLElement */
const _restaurantFavoriteButton = document.querySelector(
    "[data-restaurant-id][data-favorite-button]"
);
empowerRestaurantLikeButton(_restaurantFavoriteButton);

/** @type HTMLElement */
const orderSelect = document.querySelector(".select > select");

empowerOrderSelect(orderSelect);

const reviewList = document.querySelectorAll("#review-list > .review");

reviewList.forEach(empowerReview);
