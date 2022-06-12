// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { fetchOrderedRestaurantReviews, fetchReview } from "../api/review.js";
import { fetchReviewResponse } from "../api/response.js";
import { fetchUser } from "../api/user.js";
import { setOrderState } from "../api/orders.js";

const restaurantId = parseInt(
    new URLSearchParams(window.location.search).get("id") ?? ""
);

/**
 * "Empowers" a restaurant page's like button using javascript.
 *
 * @param {HTMLElement} button
 */
const empowerRestaurantLikeButton = (button) => {
    if (!button) return;

    button.addEventListener("click", async (event) => {
        event.preventDefault();

        try {
            const favorite = await toggleRestaurantLikedStatus(restaurantId);

            if (favorite === undefined) return;

            button.dataset.toggleState = favorite ? "on" : "off";
            button.ariaLabel = favorite ? "Unfavorite" : "Favorite";
        } catch {
            return;
        }
    });
};

// TODO: Show response
const createReview = async (review) => {
    try {
        const user = await fetchUser(review.client);

        if (!user) return;

        const { id: userId, name: userName, image: userPhotoPath } = user;

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
        ).toLocaleDateString("pt-PT");

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

/**
 *
 * @param {HTMLSelectElement} select
 */
const empowerOrderSelect = (select) => {
    /** @type {HTMLElement?} */
    const reviewList = document.querySelector("#review-list");

    if (reviewList)
        select.addEventListener("change", async (e) => {
            e?.preventDefault();

            const reviewOrdering = select.value;

            const [attribute, order] = reviewOrdering.split("-");

            if (
                (attribute != "score" && attribute != "date") ||
                (order != "asc" && order != "desc")
            )
                return;

            const reviews = await fetchOrderedRestaurantReviews(
                restaurantId,
                attribute,
                order
            );

            const nodes = await Promise.all(reviews?.map(createReview) ?? []);

            // .replaceChildren didn't work, this did
            for (let i = 0; i < nodes.length; i++) {
                const elem = reviewList.children[i];
                const node = nodes[i];

                if (node) reviewList.replaceChild(node, elem);
            }
        });
};

/**
 *
 * @param {HTMLElement} reviewElement
 */
const empowerReview = (reviewElement) => {
    /** @type {HTMLButtonElement?} */
    const replyButton = reviewElement.querySelector(
        "button[data-reply-button]"
    );
    /** @type {HTMLDialogElement?} */
    const dialog = document.querySelector("dialog#review-response");

    replyButton?.addEventListener("click", async () => {
        if (!dialog) return;

        const { reviewId } = reviewElement.dataset;

        /** @type {HTMLElement} */
        // @ts-ignore
        const reviewNode = reviewElement.cloneNode(true);
        reviewNode.querySelector("button[data-reply-button]")?.remove();
        reviewNode.classList.add("fullwidth");

        dialog.querySelector(".review")?.replaceWith(reviewNode);

        /** @type {HTMLInputElement?} */
        const hiddenInput = dialog.querySelector(
            "input[type=hidden][name=reviewId]"
        );
        if (hiddenInput) hiddenInput.value = reviewId ?? "";

        dialog.showModal();
    });
};

const stateDict = {
    pending: "Pending",
    canceled: "Canceled",
    in_progress: "In progress",
    ready: "Ready",
    delivered: "Delivered",
};

/**
 *
 * @param {HTMLElement} orderElement
 */
const empowerOrder = (orderElement) => {
    const orderId = parseInt(orderElement.dataset.orderId ?? "");
    /** @type {NodeListOf<HTMLButtonElement>} */
    const buttons = orderElement.querySelectorAll("button[data-order-button]");
    /** @type {HTMLButtonElement?} */
    const cancelButton = orderElement.querySelector(
        'button[data-order-button="canceled"]'
    );
    /** @type {HTMLButtonElement?} */
    const button = buttons.item(buttons.length - 1);
    /** @type {HTMLElement?} */
    const orderStateEl = orderElement.querySelector(".state");

    buttons.forEach((b) =>
        b.addEventListener("click", async () => {
            let { orderButton: newState } = b.dataset;

            if (
                newState !== "in_progress" &&
                newState !== "ready" &&
                newState !== "delivered" &&
                newState !== "canceled"
            )
                return;

            const newOrder = await setOrderState(
                restaurantId,
                orderId,
                newState
            );

            if (!newOrder) return;

            if (orderStateEl)
                orderStateEl.textContent = stateDict[newOrder.state];

            if (newOrder.state !== "pending") cancelButton?.remove();

            if (button)
                switch (newOrder.state) {
                    case "pending":
                        break;
                    case "canceled":
                    case "delivered":
                        button.remove();
                        break;
                    case "in_progress":
                        button.dataset.orderButton = "ready";
                        button.textContent = "Mark as ready";
                        break;
                    case "ready":
                        button.dataset.orderButton = "delivered";
                        button.textContent = "Mark as delivered";
                        break;
                }
        })
    );
};

/** @type {HTMLButtonElement?} */
const restaurantFavoriteButton = document.querySelector(
    "[data-restaurant-id][data-favorite-button]"
);
if (restaurantFavoriteButton)
    empowerRestaurantLikeButton(restaurantFavoriteButton);

/** @type {HTMLSelectElement?} */
const orderSelect = document.querySelector(".select > select");
if (orderSelect) empowerOrderSelect(orderSelect);

/** @type {NodeListOf<HTMLElement>} */
const reviewList = document.querySelectorAll("#review-list > .review");
reviewList.forEach(empowerReview);

/** @type {NodeListOf<HTMLElement>} */
const orders = document.querySelectorAll(".order");
orders.forEach(empowerOrder);
