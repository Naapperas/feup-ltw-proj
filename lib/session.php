<?php 

    require_once("util.php");

    class Session {

        public function __construct() {
            session_start([
                'cookie_httponly' => '1',
                'cookie_samesite' => 'Lax',
                'cookie_secure' => '1',
            ]);
            if (!isset($_SESSION['csrf'])) {
                $_SESSION['csrf'] = generate_random_token();
            }
        }

        public function set(string $key, mixed $value): void {
            $_SESSION[$key] = $value;
        }

        public function unset(string $key): void {
            unset($_SESSION[$key]);
        }

        public function &get($key): mixed {
            return $_SESSION[$key];
        }

        public function &getUser(): ?User {
            return User::getById($this->get('user'));
        }

        public function isAuthenticated(): bool {
            return $this->get('user') !== null;
        }
    }
?>