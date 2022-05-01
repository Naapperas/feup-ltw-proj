<?php
    declare(strict_types=1);

    include_once('model.php');

    class Menu extends Model {

        static function create(array $data) {

            if (count($data) !== 2) throw new Exception("Should pass 2 parameters when creating Menu entries.");

            static $createQuery = "INSERT INTO Menu VALUES (NULL, ?, ?);";
            static $retrieveQuery = "SELECT * FROM Menu WHERE name = ? AND restaurant = ?;";
        
            $creationResults = executeQuery(Menu::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(Menu::getDb(), $retrieveQuery, false, $data);
        }

        static function update(int $id, array $newData) {

            if (count($newData) !== 2) throw new Exception("Should pass 2 parameters when creating Menu entries.");

            static $updateQuery = "UPDATE Menu SET restaurant = ?, name = ? WHERE id = ?;";
            static $retrieveQuery = "SELECT * FROM Menu WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(Menu::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(Menu::getDb(), $retrieveQuery, false, array($id));
        }

        static function delete(int $id) {

            static $retrieveQuery = "SELECT * FROM Menu WHERE id = ?;";
            static $deleteQuery = "DELETE FROM Menu WHERE id = ?;";
        
            $object = getQueryResults(Menu::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(Menu::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
        }
    }

    print_r(Menu::delete(1));
?>