<?php 
    declare(strict_types = 1);

    require_once("../../../lib/api.php");
    require_once("../../../lib/params.php");

    APIRoute(get: function() {
        $params = parseParams(query: [
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
    
        $orderings = [
            'date' => 'id',
            'score' => 'score',
        ];
    
        require_once("../../../database/models/review.php");
        require_once("../../../database/models/query.php");
    
        $orderClause = new OrderClause([
            [$orderings[$params['attribute']], strcmp($params['order'], 'asc') === 0]
        ]);
    
        $equalClause = new Equals('restaurant', $params['restaurantId']);
    
        $reviews = Review::getWithFilters([$equalClause], $params['limit'], $orderClause);

        return ['reviews' => $reviews];
    });
?>
