<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/session.php");
    
    require_once("../../database/models/review.php");
    require_once("../../database/models/review.php");
    require_once("../../database/models/review.php");

    APIRoute(
        get: getModel(Review::class),
        post: function() {
            
            $user = requireSessionAuth();

            $params = parseParams(body: [
                'score' => new IntParam(
                    max: 5,
                    min: 0
                ),
                'content' => new StringParam(),
                'restaurantId' => new IntParam(),
            ]);

            if (($restaurant = Restaurant::getById($params['restaurantId'])) === null)
                APIError(HTTPStatusCode::NOT_FOUND, 'Restaurant not found');
            
            if ($restaurant->owner === $user->id) // owner cant post reviews
                APIError(HTTPStatusCode::FORBIDDEN, 'Owner can\'t post reviews on one of his restaurants');    
        
            $review = Review::create([
                'text' => $params['content'],
                'score' => round($params['score'], 1),
                'restaurant' => $params['restaurantId'],
                'client' => $user->id,
                'review_date' => date(DATE_ISO8601)
            ]);

            if ($review === null || is_array($review))
                APIError(HTTPStatusCode::INTERNAL_SERVER_ERROR, 'Error while creating review');

            return ['review' => $review];
        }
    );
?>
