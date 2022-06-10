<?php

    function getDBConnection(string $db_name, string $schema = "sqlite") : PDO {
        try {
            static $dbMapping = array();

            $connectionString = $schema.':'.$db_name;

            if (!array_key_exists($connectionString, $dbMapping)) {
                $db = new PDO($connectionString);
                $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $db->exec("PRAGMA foreign_keys = ON");
                $dbMapping[$connectionString] = $db;
            }

            return $dbMapping[$connectionString];

        }  catch (PDOException $exception) {
            die("Error connecting to DB: ".$exception->getMessage());
        }
    }

    function getQueryResults(PDO $db, string $query, bool $fetchMultiple = true, array $params = null) : array | false {
        try {

            list($result, $stmt) = executeQuery($db, $query, $params);

            if ($result)
                return $fetchMultiple ? $stmt->fetchAll() : $stmt->fetch();
        } catch (PDOException $e) {
        } // do nothing and leave block, expected behavior is to return false

        return false;
    }

    function executeQuery(PDO $db, string $query, array $params = null): array {
        try {
            if ($stmt = $db->prepare($query))
                return array($stmt->execute($params), $stmt);
        } catch (PDOException $e) {
        } // do nothing and leave block, expected behavior is to return false

        return array(false, null);
    }

?>