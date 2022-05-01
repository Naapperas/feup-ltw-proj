<?php
    declare(strict_types=1);

    include(dirname(__DIR__).'/connection.php');

    /**
     * An abstract model representing a collection of similar data. 
     */
    abstract class Model {

         static function getDb(): PDO { return getDBConnection(dirname(__DIR__).'/main.db'); }

        /**
         * Creates an entry of this model using the specified data and returns it.
         * 
         * @param array $data the data to use to create the entry
         * 
         * @return array the newly created entry as an associative array
         */
        abstract static function create(array $data);

        /**
         * Updates the entry with the given id and returns it.
         * 
         * @param int $id the id of the entry to delete
         * @param array $data the data to use to update the entry
         * 
         * @return array the updated entry as an associative array
         */
        abstract static function update(int $id, array $newData);

        /**
         * Deletes the entry with the given id and returns it.
         * 
         * @param int $id the id of the entry to delete
         * 
         * @return array the deleted entry as an associative array
         */
        abstract static function delete(int $id);
    }
?>