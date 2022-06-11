<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('user.php');

    class Order extends Model {
        public string $state;
        public string $order_date;

        public int $user;
        public int $restaurant;

        protected static function getTableName(): string {
            return "\"Order\"";
        }
        
        public function getUser(): ?User {
            return User::getById($this->user);
        }
        
        public function getRestaurant(): ?Restaurant {
            return Restaurant::getById($this->restaurant);
        }

        public function getDishes(): array {
            $query = "SELECT dish AS id FROM Dish_order WHERE order = ?;";

            $dishes = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($dishes === false) return [];

            $result = array_map(fn(array $data) => Dish::getById($data['id']), $dishes);

            return $result;
        }

        public function addDish(int $dishId, int $amount): bool {
            $query = "INSERT INTO Dish_order VALUES (?, ?, ?);";

            list($success,) = executeQuery(static::getDB(), $query, [$dishId, $this->id, $amount]);
        
            return $success;
        }

        public function getMenus(): array {
            $query = "SELECT menu AS id FROM Menu_order WHERE order = ?;";

            $menus = getQueryResults(static::getDB(), $query, true, [$this->id]);
        
            if ($menus === false) return [];

            $result = array_map(fn(array $data) => Menu::getById($data['id']), $menus);

            return $result;
        }

        public function addMenu(int $menuId, int $amount): bool {
            $query = "INSERT INTO Menu_order VALUES (?, ?, ?);";

            list($success,) = executeQuery(static::getDB(), $query, [$menuId, $this->id, $amount]);
        
            return $success;
        }
    }
?>