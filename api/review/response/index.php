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
                'reviewId' => new IntParam(),
            ]);
        
            $review = Review::getById($params['reviewId']);

            if ($review === null) {
                APIError(HTTPStatusCode::BAD_REQUEST, "Review not found");
            }
        
            $response = $review->getResponse();
        
            if ($response === null) {
                APIError(HTTPStatusCode::NOT_FOUND, "Response not found");
            }
        
            return ['response' => $response];
        },
        post: function() {

            $user = requireSessionAuth();

            $params = parseParams(body: [
                'reviewResponse' => new StringParam(),
                'restaurantId' => new IntParam(),
                'reviewId' => new IntParam(),
            ]);
                
            if (($restaurant = Restaurant::getById($params['restaurantId'])) === null || ($review = Review::getById($params['reviewId'])) === null) {
                header("Location: /");
                die;
            }
            
            if ($restaurant->owner !== $user->id) // only owner can post review responses
                APIError(HTTPStatusCode::FORBIDDEN, 'Only restaurant owners can post responses to reviews');
                
            $response = Response::create([
                'text' => $params['reviewResponse'],
                'review' => $review->id,
                'response_date' => date(DATE_ISO8601)
            ]);

            if ($response === null || is_array($response))
                APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error while creating response');

            return ['response' => $response];
        }
    );
?>
