<?php 
    declare(strict_types = 1);

    require_once("../../lib/util.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
    }

    require_once("../../lib/params.php");

    $params = parseParams(get_params: [
        'reviewId' => new IntParam(),
    ]);

    require_once("../../database/models/review.php");

    $review = Review::getById($params['reviewId']);

    if ($review === null) {
        APIError(HTTPStatusCode::NOT_FOUND, "Could not find review with given id");
    }

    echo json_encode(['review' => $review]);
?>
