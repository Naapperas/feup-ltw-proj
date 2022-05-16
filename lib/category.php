<?php
    declare(strict_types = 1);

    require_once("../database/models/restaurant.php");

    function getCategories(int $id) : array {
        $retrieveQuery = "SELECT category FROM Restaurant_category WHERE restaurant = ?";

        $object = getQueryResults(Restaurant::getDb(), $retrieveQuery, true, array($id));

        foreach($object as $category) {
            $query = "SELECT name FROM Category WHERE id = ?";
            $categories[] = getQueryResults(Restaurant::getDb(), $query, true, array($category));
        }

        return $categories ? $categories : array();
    }

?>
