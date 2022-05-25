<?php
declare(strict_types=1);

function _error() {
    http_response_code(400);
    require(dirname(__DIR__).'/error.php');
    die();
}

abstract class Param {
    function __construct(
        public mixed $default = null,
        public bool $optional = false
    ) {}

    abstract function parse(string|array|null $val);

    protected function error() {
        if ($this->optional)
            return null;
        else
            return _error();
    }
}

class StringParam extends Param {
    function __construct(
        public mixed $default = null,
        public bool $optional = false,
        public ?string $pattern = null,
        public ?int $min_len = null,
        public ?int $max_len = null,
        public bool $case_insensitive = false
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {

        if (is_array($val))
            return $this->error();

        $r = $val ?? $this->default;

        if (!$this->optional && !isset($r))
            return $this->error();

        $r = strval($r);

        if (isset($this->pattern) && preg_match($this->pattern, $r) === false)
            return $this->error();

        if (isset($this->min_len) && strlen($r) < $this->min_len)
            return $this->error();

        if (isset($this->max_len) && strlen($r) > $this->max_len)
            return $this->error();

        if ($this->case_insensitive)
            $r = strtolower($r);

        return htmlentities($r);
    }
}

class IntParam extends Param {
    function __construct(
        public mixed $default = null,
        public bool $optional = false,
        public ?int $min = null,
        public ?int $max = null,
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {

        if (is_array($val))
            return $this->error();

        $r = $val ?? $this->default;

        if (!$this->optional && !isset($r))
            return $this->error();

        $r = intval($r);

        if (isset($this->min) && $r < $this->min)
            return $this->error();

        if (isset($this->max) && $r > $this->max)
            return $this->error();

        return $r;
    }
}

class ArrayParam extends Param {
    function __construct(
        public mixed $default = null,
        public bool $optional = false,
        public ?int $minLen = null,
        public ?int $maxLen = null,
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {

        if (!is_array($val))
            return $this->error();

        $r = $val ?? $this->default;

        if (!$this->optional && !isset($r))
            return $this->error();

        if (isset($this->min) && count($r) < $this->minLen)
            return $this->error();

        if (isset($this->max) && count($r) > $this->maxLen)
            return $this->error();

        return $r;
    }
}

function parseParams(array $get_params = [], array $post_params = []) {
    foreach ($get_params as $name => $p) {
        if (is_int($name)){
            $name = $p;
            $p = new StringParam();
        }
        
        $r[$name] = $p->parse($_GET[$name]);
    }

    foreach ($post_params as $name => $p) {
        if (is_int($name)){
            $name = $p;
            $p = new StringParam();
        }
        
        $r[$name] = $p->parse($_POST[$name]);
    }

    return $r;
}

?>
