// @ts-check

"use strict";

import { toggleRestaurantLikedStatus } from "../api/restaurant.js";
import { fetchOrderedReviews } from "../api/review.js";
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

            button.dataset.toggleState = favorite ? "on" : "off";
            button.ariaLabel = favorite ? "Unfavorite" : "Favorite";
        } catch {
            return;
        }
    };

    button.addEventListener("click", toggleLikedStatus);
};

const empowerOrderSelect = (select) => {

    /** @type HTMLElement */
    const reviewList = select.parentElement.parentElement.parentElement.querySelector("#review-list");

    const createReview = async (review) => {

        try {
            const { user, userPhotoPath } = await fetchUser(review.client);

            if (!user) return;

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
            reviewText.textContent = review.text;

            reviewElement.appendChild(reviewText);

            reviewList.appendChild(reviewElement);

        } catch {
            return
        }
    }

    const handleInputChange = async (e) => {
        e?.preventDefault();

        const restaurantId = e.target.parentElement.parentElement.parentElement.dataset.restaurantId;

        let child = reviewList.lastElementChild; 
        while (child !== null) {
            reviewList.removeChild(child);
            child = reviewList.lastElementChild;
        }

        const reviewOrdering = e.target.value;

        const [attribute, order] = reviewOrdering.split('-');

        const reviews = await fetchOrderedReviews(restaurantId, attribute, order);

        reviews.forEach(createReview);
    }

    // @ts-ignore
    select.addEventListener("change", handleInputChange);
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
