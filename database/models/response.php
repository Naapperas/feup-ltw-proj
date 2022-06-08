<?php
    declare(strict_types=1);

    require_once('model.php');
    require_once('review.php');

    class Response extends Model {

        public string $text;
        public string $response_date;

        public int $review;

        protected static function getTableName(): string {
            return "Review_response";
        }

        public function getReview(): ?Review {
            return Review::getById($this->review);
        }
    }
?>