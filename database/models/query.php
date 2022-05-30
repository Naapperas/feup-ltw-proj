<?php 

    declare(strict_types=1);

    interface QueryToken {
        public function getQueryString(): string;
        public function getQueryValues(): array;
    }

    abstract class QueryFilter implements QueryToken {

        public function __construct(public string $attribute, public mixed $value) {}

        public abstract function getOp(): string;

        public function getQueryString(): string {
            return sprintf('%s %s ?', $this->attribute, $this->getOp());
        }

        public function getQueryValues(): array {
            return [$this->value];
        }
    };

    class Equals extends QueryFilter {
        public function __construct(string $attribute, mixed $value) {
            parent::__construct($attribute, $value);
        }

        public function getOp(): string {
            return '=';
        }
    }

    class NotEquals extends QueryFilter {
        public function __construct(string $attribute, mixed $value) {
            parent::__construct($attribute, $value);
        }

        public function getOp(): string {
            return '<>';
        }
    }

    class LessThan extends QueryFilter {

        public function __construct(string $attribute, int|float $amount) {
            parent::__construct($attribute, $amount);
        }

        public function getOp(): string {
            return '<';
        }
    }

    class LessThanOrEqual extends QueryFilter {

        public function __construct(string $attribute, int|float $amount) {
            parent::__construct($attribute, $amount);
        }

        public function getOp(): string {
            return '<=';
        }
    }
    
    class GreaterThan extends QueryFilter {

        public function __construct(string $attribute, int|float $amount) {
            parent::__construct($attribute, $amount);
        }

        public function getOp(): string {
            return '>';
        }
    }

    class GreaterThanOrEqual extends QueryFilter {

        public function __construct(string $attribute, int|float $amount) {
            parent::__construct($attribute, $amount);
        }

        public function getOp(): string {
            return '>=';
        }
    }

    class Like extends QueryFilter {
        public function __construct(string $attribute, string $text) {
            parent::__construct($attribute, sprintf("%%%s%%", $text));
        }

        public function getOp(): string {
            return 'LIKE';
        }
    }

    // these classes could be further refactored to avoid repetition but that is not the scope of this project (for now)

    class In implements QueryToken {

        private string $queryString;
        private array $queryValues;

        public function __construct(public string $attribute, public array $elems) {
            
            $this->queryString = sprintf("%s IN (%s)", $attribute, implode(', ', array_fill(0, count($elems), '?')));

            $this->queryValues = [...$elems];
        }

        public function getQueryString(): string {
            return $this->queryString;
        }

        public function getQueryValues(): array {
            return $this->queryValues;
        }
    }

    class AndClause implements QueryToken {

        private string $queryString;
        private array $queryValues;

        public function __construct(public array $clauses) {

            $this->queryString = sprintf("(%s)", implode(" AND ", array_map(fn (QueryToken $clause) => $clause->getQueryString(), $this->clauses)));

            $this->queryValues = array_reduce($clauses, fn (array $values, QueryToken $clause) => array_merge($values, $clause->getQueryValues()), []);
        }

        public function getQueryString(): string {
            return $this->queryString;
        }

        public function getQueryValues(): array {
            return $this->queryValues;
        }
    }

    class OrClause implements QueryToken {

        private string $queryString;
        private array $queryValues;

        public function __construct(public array $clauses) {

            $this->queryString = sprintf("(%s)", implode(" OR ", array_map(fn (QueryToken $clause) => $clause->getQueryString(), $this->clauses)));
            
            $this->queryValues = array_reduce($clauses, fn (array $values, QueryToken $clause) => array_merge($values, $clause->getQueryValues()), []);
        }

        public function getQueryString(): string {
            return $this->queryString;
        }

        public function getQueryValues(): array {
            return $this->queryValues;
        }
    }
    
    class NotClause implements QueryToken {

        private string $queryString;
        private array $queryValues;

        public function __construct(public QueryToken $clause) {
            $this->queryString = sprintf(" NOT (%s)", $this->clause->getQueryString());
            $this->queryValues = $clause->getQueryValues();
        }

        public function getQueryString(): string {
            return $this->queryString;
        }

        public function getQueryValues(): array {
            return $this->queryValues;
        }
    }

    class OrderClause implements QueryToken {
        
        private string $queryString;

        public function __construct(array $orderings) {

            for ($i = 0; $i < count($orderings); $i++)
                $tokens[] = sprintf("%s %s", $orderings[$i][0], ($orderings[$i][1] ?? true) ? 'ASC' : 'DESC');

            $this->queryString = sprintf(' ORDER BY %s', implode(', ', $tokens));
        }

        public function getQueryString(): string {
            return $this->queryString;
        }

        public function getQueryValues(): array {
            return [];
        }
    }
?>