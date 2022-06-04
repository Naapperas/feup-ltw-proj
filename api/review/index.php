<?php 
    declare(strict_types = 1);

    require_once("../../lib/util.php");

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        error(HTTPStatusCode::METHOD_NOT_ALLOWED);
        die;
    }

    require_once("../../lib/params.php");

    $params = parseParams(get_params: [
        'restaurantId' => new IntParam(),
        'attribute' => new StringParam(
            pattern: '/^(score|date)$/'
        ),
        'order' => new StringParam(
            pattern: '/^(asc|desc)$/'
        ),
        'limit' => new IntParam(
            optional: true,
            default: 50
        )
    ]);

    const orderings = [
        'date' => 'id',
        'score' => 'score',
    ];

    require_once("../../database/models/review.php");

    $orderClause = new OrderClause([
        [orderings[$params['attribute']], strcmp($params['order'], 'asc') === 0]
    ]);

    $equalClause = new Equals('restaurant', $params['restaurantId']);

    $reviews = Review::getWithFilters([$equalClause], $params['limit'], $orderClause);

    echo json_encode(['reviews' => $reviews]);
?>
