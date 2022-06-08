// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { fetchOrderedRestaurantReviews, fetchReview } from "../api/review.js";
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
        const { user, userPhotoPath } = await fetchUser(review.client);

        if (!user || !userPhotoPath) return;

        const { id: userId, name: userName, address: userAddress } = user;

        const reviewElement = document.createElement('article');
        reviewElement.classList.add('review');

        const reviewHeaderLink = document.createElement('a');
        reviewHeaderLink.href = `/profile/?id=${userId}`;

        const reviewHeader = document.createElement('header');
        reviewHeader.classList.add('header');

        const profilePicture = document.createElement('img');
        profilePicture.src = userPhotoPath;
        profilePicture.alt = `Review profile image for user ${userId}`;
        profilePicture.classList.add('avatar', 'small');

        const profileName = document.createElement('span');
        profileName.classList.add('title');

        profileName.textContent = userName;
        
        const profileAddress = document.createElement('span');
        profileAddress.classList.add('subtitle', 'secondary');
        profileAddress.textContent = userAddress;

        const scoreSpan = document.createElement('span');
        scoreSpan.classList.add('chip', 'right')

        const iconSpan = document.createElement('span');
        iconSpan.classList.add('icon');
        iconSpan.textContent = 'star';

        const scoreText = document.createTextNode(`${Math.round(review.score * 100) / 100}`);

        scoreSpan.appendChild(iconSpan);
        scoreSpan.appendChild(scoreText);
        
        reviewHeader.appendChild(profilePicture);
        reviewHeader.appendChild(profileName);
        reviewHeader.appendChild(profileAddress);
        reviewHeader.appendChild(scoreSpan);

        reviewHeaderLink.appendChild(reviewHeader);

        reviewElement.appendChild(reviewHeaderLink);

        const reviewText = document.createElement('p');
        reviewText.classList.add('review-content');
        reviewText.innerHTML = review.text; // this is safe to use here because this text comes from stored reviews, which can only be created through the given action which uses StringParams that escape the given text

        reviewElement.appendChild(reviewText);

        return reviewElement;
    } catch {
        return;
    }
}

const empowerOrderSelect = (select) => {

    /** @type HTMLElement */
    const reviewList = document.querySelector("#review-list");

    const handleInputChange = async (e) => {
        e?.preventDefault();

        const restaurantId = e.target.parentElement.parentElement.parentElement.dataset.restaurantId;

        const reviewOrdering = e.target.value;

        const [attribute, order] = reviewOrdering.split('-');

        const reviews = await fetchOrderedRestaurantReviews(restaurantId, attribute, order);

        const nodes = await Promise.all(reviews.map(createReview));

        for (let i = 0; i < nodes.length; i++) { // .replaceChildren didn't work, this did

            const elem = reviewList.children[i];

            reviewList.replaceChild(nodes[i], elem);
        }
    }

    // @ts-ignore
    select.addEventListener("change", handleInputChange);
}

const empowerReview = (reviewElement) => {

    reviewElement.addEventListener('click', async () => {
        const dialog = document.querySelector("#review-response[data-owner-logged-in]"); // do this way so we can run custom code with this specific dialog

        if (!dialog) return; // the user is not the restaurant owner

        const { reviewId } = reviewElement.dataset;

        const review = await fetchReview(reviewId);

        const reviewNode = await createReview(review);

        dialog.querySelector("div.content > section#response-review")?.replaceChildren(reviewNode);
        // @ts-ignore
        dialog.querySelector('input[type=hidden][name=reviewId]').value = review.id;

        // @ts-ignore
        dialog.showModal();
    });
}

/** @type HTMLElement */
const _restaurantFavoriteButton = document.querySelector(
    "[data-restaurant-id][data-favorite-button]"
);
empowerRestaurantLikeButton(_restaurantFavoriteButton);

/** @type HTMLElement */
const orderSelect = document.querySelector(
    ".select > select"
);

empowerOrderSelect(orderSelect);

const reviewList = document.querySelectorAll('#review-list > .review');

reviewList.forEach(empowerReview);