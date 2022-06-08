<?php 
    declare(strict_types = 1);

    require_once("../../../lib/util.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
    }

    require_once("../../../lib/params.php");

    $params = parseParams(get_params: [
        'reviewId' => new IntParam(),
    ]);

    require_once("../../../database/models/response.php");
    require_once("../../../database/models/review.php");
    require_once("../../../database/models/query.php");

    if (Review::getById($params['reviewId']) === null) {
        APIError(HTTPStatusCode::BAD_REQUEST, "Review with the given id does not exist");
    }

    $clause = new Equals('review', $params['reviewId']);

    $response = Response::getWithFilters([$clause]);

    if ($response === null) {
        APIError(HTTPStatusCode::NOT_FOUND, "Could not find response for the given review");
    }

    echo json_encode(['response' => $response[0]]); // since 'review' is unique, there is at most one response for this review
?>
