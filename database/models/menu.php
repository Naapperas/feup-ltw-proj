<?php
    declare(strict_types=1);

    include_once('model.php');

    class Menu extends Model {

        static function create(array $data): array {

            if (count($data) !== 2) throw new Exception("Should pass 2 parameters when creating Menu entries.");

            static $createQuery = "INSERT INTO Menu VALUES (NULL, ?, ?, 0);";
            static $retrieveQuery = "SELECT * FROM Menu WHERE name = ? AND restaurant = ?;";
        
            $creationResults = executeQuery(Menu::getDb(), $createQuery, $data);

            if ($creationResults[0])
                return getQueryResults(Menu::getDb(), $retrieveQuery, false, $data);

            return array();
        }

        static function update(int $id, array $newData): array {

            if (count($newData) !== 2) throw new Exception("Should pass 2 parameters when updating Menu entries.");

            static $updateQuery = "UPDATE Menu SET restaurant = ?, name = ? WHERE id = ?;"; // intentionally left price out because it is supposed to be a derived attribute
            static $retrieveQuery = "SELECT * FROM Menu WHERE id = ?;";
        
            $newData[] = $id;

            $updateResults = executeQuery(Menu::getDb(), $updateQuery, $newData);

            if ($updateResults[0])
                return getQueryResults(Menu::getDb(), $retrieveQuery, false, array($id));
                
            return array();
        }

        static function delete(int $id): array {

            static $retrieveQuery = "SELECT * FROM Menu WHERE id = ?;";
            static $deleteQuery = "DELETE FROM Menu WHERE id = ?;";
        
            $object = getQueryResults(Menu::getDb(), $retrieveQuery, false, array($id));
            
            $deleteResults = executeQuery(Menu::getDb(), $deleteQuery, array($id));

            if ($deleteResults[0])
                return $object;
            
            return array();
        }

        static function get(int|array $id): array {

            $retrieveQuery = "SELECT * FROM Menu WHERE id = ?";

            if (gettype($id) == 'integer') {
                
                $object = getQueryResults(Menu::getDb(), $retrieveQuery, false, array($id));

                return $object ? $object : array();
                
            } else {
                
                $result = array();
                
                foreach($id as $entry_id) {
                    $object = getQueryResults(Menu::getDb(), $retrieveQuery, false, array($entry_id));
    
                    if ($object)
                        $result[] = $object;

                }

                return $result;
            }
        }
    }

    print_r(Menu::delete(1));
?>