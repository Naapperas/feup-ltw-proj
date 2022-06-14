<?php 
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../lib/util.php");
    require_once("../../../lib/params.php");

    require_once("../../../database/models/review.php");
    require_once("../../../database/models/restaurant.php");
    require_once("../../../database/models/response.php");

    APIRoute(
        get: function() {
            $params = parseParams(query: [
                'id' => new IntParam(),
            ]);
        
            $review = Review::getById($params['id']);

            if ($review === null) {
                APIError(HTTPStatusCode::BAD_REQUEST, "Review not found");
            }
        
            $response = $review->getResponse();
        
            if ($response === null) {
                APIError(HTTPStatusCode::NOT_FOUND, "Response not found");
            }
        
            return ['response' => $response];
        },
        post: postModel(Review::class, [], [
            'text' => new StringParam(min_len: 1),
            'review' => new IntParam(),
        ], function($review) {
            APIError(HTTPStatusCode::BAD_REQUEST, 'Invalid arguments');
        }, function(&$values) {
            $review = Review::getById($values['review']);

            if (!isset($review))
                APIError(HTTPStatusCode::NOT_FOUND, 'Review not found');

            $restaurant = Restaurant::getById($values['restaurant']);
            $user = requireAuth();

            if ($restaurant->owner !== $user->id)
                APIError(HTTPStatusCode::FORBIDDEN, 'Only owner can reply');

            $values['response_date'] = date(DATE_ISO8601);
        })
    );
?>
