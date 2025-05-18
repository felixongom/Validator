<?php
namespace Validator;

require 'vendor/autoload.php';


class Validate {
    private $value;
    private $errors = [];
    private $rules = [];
    private $type;
    private $file;

    public static function string($value) {
        $v = new self();
        $v->value = $value;
        $v->type = 'string';
        return $v;
    }

    public static function number($value) {
        $v = new self();
        $v->value = $value;
        $v->type = 'number';
        return $v;
    }

    public static function file($key, $source = null) {
        $v = new self();
        $v->type = 'file';
        $source = $source ?? $_FILES;
        $v->file = isset($source[$key]) ? $source[$key] : null;
        return $v;
    }

    public function min_length($len) {
        $this->rules[] = function() use ($len) {
            if (strlen($this->value) < $len) {
                $this->errors[] = "Minimum length is $len characters.";
            }
        };
        return $this;
    }

    public function max_length($len) {
        $this->rules[] = function() use ($len) {
            if (strlen($this->value) > $len) {
                $this->errors[] = "Maximum length is $len characters.";
            }
        };
        return $this;
    }

    public function uppercase() {
        $this->rules[] = function() {
            if (!preg_match('/[A-Z]/', $this->value)) {
                $this->errors[] = "Must contain at least one uppercase letter.";
            }
        };
        return $this;
    }

    public function lowercase() {
        $this->rules[] = function() {
            if (!preg_match('/[a-z]/', $this->value)) {
                $this->errors[] = "Must contain at least one lowercase letter.";
            }
        };
        return $this;
    }

    public function has_number() {
        $this->rules[] = function() {
            if (!preg_match('/[0-9]/', $this->value)) {
                $this->errors[] = "Must contain at least one number.";
            }
        };
        return $this;
    }

    public function special_character() {
        $this->rules[] = function() {
            if (!preg_match('/[^a-zA-Z0-9]/', $this->value)) {
                $this->errors[] = "Must contain at least one special character.";
            }
        };
        return $this;
    }

    public function email() {
        $this->rules[] = function() {
            if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Invalid email format.";
            }
        };
        return $this;
    }

    public function url() {
        $this->rules[] = function() {
            if (!filter_var($this->value, FILTER_VALIDATE_URL)) {
                $this->errors[] = "Invalid URL format.";
            }
        };
        return $this;
    }

    public function custom(callable $callback, $message) {
        $this->rules[] = function() use ($callback, $message) {
            if (!$callback($this->value)) {
                $this->errors[] = $message;
            }
        };
        return $this;
    }

    public function min($min) {
        $this->rules[] = function() use ($min) {
            if ($this->value < $min) {
                $this->errors[] = "Minimum value is $min.";
            }
        };
        return $this;
    }

    public function max($max) {
        $this->rules[] = function() use ($max) {
            if ($this->value > $max) {
                $this->errors[] = "Maximum value is $max.";
            }
        };
        return $this;
    }

    public function integer() {
        $this->rules[] = function() {
            if (!is_int($this->value)) {
                $this->errors[] = "Value must be an integer.";
            }
        };
        return $this;
    }

    public function float() {
        $this->rules[] = function() {
            if (!is_float($this->value) && !is_int($this->value)) {
                $this->errors[] = "Value must be a float or integer.";
            }
        };
        return $this;
    }

    public function min_size($kb) {
        $this->rules[] = function() use ($kb) {
            if ($this->file && $this->file['size'] < ($kb * 1024)) {
                $this->errors[] = "File size must be at least {$kb}KB.";
            }
        };
        return $this;
    }

    public function max_size($kb) {
        $this->rules[] = function() use ($kb) {
            if ($this->file && $this->file['size'] > ($kb * 1024)) {
                $this->errors[] = "File size must not exceed {$kb}KB.";
            }
        };
        return $this;
    }

    public function allowed_extension($exts) {
        $this->rules[] = function() use ($exts) {
            if ($this->file) {
                $ext = strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION));
                $allowed = is_array($exts) ? $exts : [$exts];
                if (!in_array($ext, $allowed)) {
                    $this->errors[] = "Invalid file extension. Allowed: " . implode(', ', $allowed);
                }
            }
        };
        return $this;
    }

    public function allowed_mime($mimes) {
        $this->rules[] = function() use ($mimes) {
            if ($this->file) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $this->file['tmp_name']);
                finfo_close($finfo);
                $allowed = is_array($mimes) ? $mimes : [$mimes];
                if (!in_array($mime, $allowed)) {
                    $this->errors[] = "Invalid MIME type. Allowed: " . implode(', ', $allowed);
                }
            }
        };
        return $this;
    }

    public function run() {
        foreach ($this->rules as $rule) {
            $rule();
        }
        return $this->errors;
    }
}
