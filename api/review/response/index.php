<?php 
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../lib/util.php");
    require_once("../../../database/models/review.php");

    APIRoute(
        get: function() {
            require_once("../../../lib/params.php");

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
        }
    );
?>
