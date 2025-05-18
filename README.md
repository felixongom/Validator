# 📄 PHPValidator
A flexible PHP validation class for validating strings, numbers, and files. It supports both static chaining and method chaining for clear and readable validation rules.

### ✅ Available Static Methods
the name spce id `use PHPValidator\Validate`
```Validate::string($value, $fieldName)``` -Initialize validation for a string.

```Validate::number($value, $fieldName)``` -Initialize validation for a number (int or float).

```Validate::file($key, $source = null, $fieldName = 'File')```-Initialize validation for an uploaded file. ```$key``` is the name of the ```$_FILES array```. ```$source``` can be ```FILES``` or a custom file array (e.g. from an API).

### 🔤 String Validation Methods

```Validate::string($value, $fieldName)``` 
| Method                  | Description                                                 |
| ----------------------- | ----------------------------------------------------------- |
| `->min_length($len)`    | Requires a minimum number of characters.                    |
| `->max_length($len)`    | Requires a maximum number of characters.                    |
| `->uppercase()`         | Requires at least one uppercase character.                  |
| `->lowercase()`         | Requires at least one lowercase character.                  |
| `->has_number()`        | Requires at least one numeric digit.                        |
| `->special_character()` | Requires at least one special character (non-alphanumeric). |
| `->email()`             | Checks if the string is a valid email address.              |
| `->url()`               | Checks if the string is a valid URL.                        |



### 🔢 Number Validation Methods
| Method          | Description                                       |
| --------------- | ------------------------------------------------- |
| `->min($value)` | Minimum numeric value allowed.                    |
| `->max($value)` | Maximum numeric value allowed.                    |
| `->integer()`   | Must be an integer.                               |
| `->float()`     | Must be a float (or an integer is also accepted). |


### 🗂️ File Validation Methods

Use after ```Validate::file('input_name')```.

| Method                       | Description                                |
| ---------------------------- | ------------------------------------------ |
| `->min_size($kb)`            | Minimum size in **kilobytes (KB)**.        |
| `->max_size($kb)`            | Maximum size in **kilobytes (KB)**.        |
| `->allowed_extension($exts)` | Allowed file extensions (string or array). |
| `->allowed_mime($mimes)`     | Allowed MIME types (string or array).      |


### 🔧 Custom Validation
```php
->custom(callable $callback, string $errorMessage)
```

Run any custom logic. If it fails, the provided error message is returned.

Example:

```php
->custom(fn($v) => strlen($v) % 2 === 0, 'Length must be even.')

```


### 🚦 Final Step

```->run()```
Executes all validation rules and returns an array of error messages.

#### 🧪 Examples
##### Validate String

```php
$errors = Validate::string('Hello123!')
    ->min_length(5)
    ->max_length(10)
    ->uppercase()
    ->lowercase()
    ->has_number()
    ->special_character()
    ->run();
```

##### Validate Number

```php
$errors = Validate::number(5.5)
    ->min(0)
    ->max(10)
    ->float()
    ->run();
```
##### Validate File Upload

```php
$errors = Validate::file('image')
    ->min_size(100) // 100KB
    ->max_size(2048) // 2MB
    ->allowed_extension(['jpg', 'png'])
    ->allowed_mime(['image/jpeg', 'image/png'])
    ->run();
```

#### 🧼 Notes
```run()``` always returns an array. If valid, it will be an empty array.

File size is automatically handled in kilobytes.

You can extend this class easily with more rules or by subclassing.

Let me know if you'd like a version of this in a .md file or integrated into a documentation tool.