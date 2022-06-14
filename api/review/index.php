<?php 
    declare(strict_types = 1);

    require_once("../../lib/api.php");
    require_once("../../lib/session.php");
    
    require_once("../../database/models/review.php");
    require_once("../../database/models/review.php");
    require_once("../../database/models/review.php");

    APIRoute(
        get: getModel(Review::class),
        post: postModel(Review::class, [], [
            'score' => new IntParam(
                max: 5,
                min: 0
            ),
            'text' => new StringParam(min_len: 1),
            'restaurant' => new IntParam(),
        ], function($review) {
            APIError(HTTPStatusCode::BAD_REQUEST, 'Invalid arguments');
        }, function(&$values) {
            $restaurant = Restaurant::getById($values['restaurant']);
            $user = requireAuth();

            if (!isset($restaurant))
                APIError(HTTPStatusCode::NOT_FOUND, 'Restaurant not found');

            if ($restaurant->owner === $user->id)
                APIError(HTTPStatusCode::FORBIDDEN, 'Owner can\'t post reviews to one of his own restaurants');

            $values['client'] = $user->id;
            $values['review_date'] = date(DATE_ISO8601);
        })
    );
?>
