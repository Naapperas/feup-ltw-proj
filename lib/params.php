<?php
declare(strict_types=1);

require_once(__DIR__.'/util.php');

function _error() {
    pageError(HTTPStatusCode::BAD_REQUEST);
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
        ?string $default = null,
        bool $optional = false,
        public ?string $pattern = null,
        public ?int $min_len = null,
        public ?int $max_len = null,
        public bool $case_insensitive = false
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {
        $r = $val ?? $this->default;

        if (is_array($r))
            return $this->error();

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
        ?int $default = null,
        bool $optional = false,
        public ?int $min = null,
        public ?int $max = null,
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {
        $r = $val ?? $this->default;

        if (is_array($r))
            return $this->error();

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

class FloatParam extends Param {
    function __construct(
        ?float $default = null,
        bool $optional = false,
        public ?float $min = null,
        public ?float $max = null,
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {
        $r = $val ?? $this->default;

        if (is_array($r))
            return $this->error();

        if (!$this->optional && !isset($r))
            return $this->error();

        $r = floatval($r);

        if (isset($this->min) && $r < $this->min)
            return $this->error();

        if (isset($this->max) && $r > $this->max)
            return $this->error();

        return $r;
    }
}

class ArrayParam extends Param {
    function __construct(
        ?array $default = null,
        bool $optional = false,
        public ?Param $param_type = null,
        public ?int $minLen = null,
        public ?int $maxLen = null,
    ) {
        parent::__construct($default, $optional);

        if (!isset($this->param_type))
            $this->param_type = new StringParam();
    }

    function parse(string|array|null $val) {
        $r = $val ?? $this->default;

        if (!is_array($r))
            return $this->error();

        if (!$this->optional && !isset($r))
            return $this->error();

        if (isset($this->minLen) && count($r) < $this->minLen)
            return $this->error();

        if (isset($this->maxLen) && count($r) > $this->maxLen)
            return $this->error();

        $r = array_map(fn($x) => $this->param_type->parse($x), $r);

        return $r;
    }
}

class ObjectParam extends Param {
    function __construct(
        public array $object_schema,
        ?array $default = null,
        bool $optional = false,
    ) {
        parent::__construct($default, $optional);
    }

    function parse(string|array|null $val) {
        $val ??= $this->default;

        if (!is_array($val))
            return $this->error();

        if (!$this->optional && !isset($val))
            return $this->error();
            
        foreach ($this->object_schema as $key => $value)
            $r[$key] = $value->parse($val[$key]);

        return $r;
    }
}

function parseParams(array $query = [], array $body = []) {
    foreach ($query as $name => $p) {
        if (is_int($name)){
            $name = $p;
            $p = new StringParam();
        }
        
        $r[$name] = $p->parse($_GET[$name]);
    }

    foreach ($body as $name => $p) {
        if (is_int($name)){
            $name = $p;
            $p = new StringParam();
        }
        
        $r[$name] = $p->parse($_POST[$name]);
    }

    return $r;
}

?>
