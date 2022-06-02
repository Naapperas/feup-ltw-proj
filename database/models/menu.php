<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('restaurant.php');
    require_once('dish.php');

    class Menu extends Model {
        use HasImage;

        public string $name;
        public float $price;

        public int $restaurant;

        protected static function getTableName(): string {
            return 'Menu';
        }

        protected static function getImageFolder(): string {
            return "menu";
        }

        public function getRestaurant(): ?Restaurant {
            return Restaurant::getById($this->restaurant);
        }

        public function addDish(Dish $dish): bool {

            $query = 'INSERT INTO Dish_menu VALUES (?, ?);';

            list($success,) = executeQuery(static::getDB(), $query, [$dish->id, $this->id]);

            if ($success) {

                $this->price += $dish->price;
                while(!$this->update()); // yikes

                return true;
            } else return false;
        }

        public function getDishes(): array {
            $query = "SELECT dish AS id FROM Dish_menu WHERE menu = ?;";

            $categories = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($categories === false) return [];

            $result = array_map(fn(array $data) => Dish::getById($data['id']), $categories);

            return $result;
        }

        public function hasDish(int $dishId) : bool {
            $query = "SELECT * FROM Dish_menu WHERE menu = ? AND dish = ?;";

            $queryResults = getQueryResults(static::getDB(), $query, true, [$this->id, $dishId]);
        
            if ($queryResults === false) return false;

            return count($queryResults) > 0;
        }

        public function setDishes(array $dishes) : bool {
            $deleteQuery = "DELETE FROM Dish_menu WHERE menu = ?;";

            list($success,) = executeQuery(static::getDB(), $deleteQuery, [$this->id]);

            if (count($dishes) === 0) return $success;

            // HACK
            // using this query format we avoid making multiple queries to the DB,
            // with the downside of 'having' to hardcode the id into the query itself, 
            // but since that id comes from the model itself, there should be no problem (unless the DB is breached)
            $query = sprintf("INSERT INTO Dish_menu(menu, dish) VALUES (%d, ?)", $this->id); 

            for($i = 1; $i < sizeof($dishes); $i++) {
                $query.= sprintf(", (%d, ?)", $this->id);
            }

            $query .= ";";

            list($success,) = executeQuery(static::getDB(), $query, $dishes);
        
            return $success;
        }
    }
?>