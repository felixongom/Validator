<?php
namespace PHPValidator;

require 'vendor/autoload.php';

class Validate {
    protected $value;
    protected $fieldName;
    protected $rules = [];
    protected $errors = [];
    protected $type = 'string';
    protected $source;
    protected $file;

    public static function string($value, $fieldName = 'This field') {
        $v = new self;
        $v->value = $value;
        $v->fieldName = $fieldName;
        $v->type = 'string';
        return $v;
    }

    public static function number($value, $fieldName = 'This field') {
        $v = new self;
        $v->value = $value;
        $v->fieldName = $fieldName;
        $v->type = 'number';
        return $v;
    }

    public static function file($key, $source = null, $fieldName = 'File') {
        $v = new self;
        $v->source = $source ?? $_FILES;
        $v->file = $v->source[$key] ?? null;
        $v->fieldName = $fieldName;
        $v->type = 'file';
        return $v;
    }

    public function min_length($len) {
        if (strlen($this->value) < $len) {
            $this->errors[] = "$this->fieldName must be at least $len characters.";
        }
        return $this;
    }

    public function max_length($len) {
        if (strlen($this->value) > $len) {
            $this->errors[] = "$this->fieldName must be at most $len characters.";
        }
        return $this;
    }

    public function uppercase() {
        if (!preg_match('/[A-Z]/', $this->value)) {
            $this->errors[] = "$this->fieldName must contain at least one uppercase letter.";
        }
        return $this;
    }

    public function lowercase() {
        if (!preg_match('/[a-z]/', $this->value)) {
            $this->errors[] = "$this->fieldName must contain at least one lowercase letter.";
        }
        return $this;
    }

    public function has_number() {
        if (!preg_match('/\d/', $this->value)) {
            $this->errors[] = "$this->fieldName must contain at least one number.";
        }
        return $this;
    }

    public function special_character() {
        if (!preg_match('/[^a-zA-Z0-9]/', $this->value)) {
            $this->errors[] = "$this->fieldName must contain at least one special character.";
        }
        return $this;
    }

    public function email() {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "$this->fieldName must be a valid email address.";
        }
        return $this;
    }

    public function url() {
        if (!filter_var($this->value, FILTER_VALIDATE_URL)) {
            $this->errors[] = "$this->fieldName must be a valid URL.";
        }
        return $this;
    }

    public function min($val) {
        if ($this->value < $val) {
            $this->errors[] = "$this->fieldName must be at least $val.";
        }
        return $this;
    }

    public function max($val) {
        if ($this->value > $val) {
            $this->errors[] = "$this->fieldName must be at most $val.";
        }
        return $this;
    }

    public function integer() {
        if (!is_int($this->value)) {
            $this->errors[] = "$this->fieldName must be an integer.";
        }
        return $this;
    }

    public function float() {
        if (!is_numeric($this->value)) {
            $this->errors[] = "$this->fieldName must be a number.";
        }
        return $this;
    }

    public function min_size($kb) {
        if ($this->type === 'file' && $this->file) {
            if (($this->file['size'] / 1024) < $kb) {
                $this->errors[] = "$this->fieldName must be at least $kb KB.";
            }
        }
        return $this;
    }

    public function max_size($kb) {
        if ($this->type === 'file' && $this->file) {
            if (($this->file['size'] / 1024) > $kb) {
                $this->errors[] = "$this->fieldName must not exceed $kb KB.";
            }
        }
        return $this;
    }

    public function allowed_extension($exts) {
        $exts = (array) $exts;
        if ($this->type === 'file' && $this->file) {
            $ext = strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $exts)) {
                $this->errors[] = "$this->fieldName must have one of the following extensions: " . implode(', ', $exts) . ".";
            }
        }
        return $this;
    }

    public function allowed_mime($mimes) {
        $mimes = (array) $mimes;
        if ($this->type === 'file' && $this->file) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $this->file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $mimes)) {
                $this->errors[] = "$this->fieldName must be one of the allowed MIME types.";
            }
        }
        return $this;
    }

    public function custom(callable $callback, $errorMessage) {
        if (!$callback($this->value)) {
            $this->errors[] = $errorMessage;
        }
        return $this;
    }

    public function run() {
        return $this->errors;
    }
} 
