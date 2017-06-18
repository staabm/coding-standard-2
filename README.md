# Coding Standard

[![Build Status](https://img.shields.io/travis/Symplify/CodingStandard/master.svg?style=flat-square)](https://travis-ci.org/Symplify/CodingStandard)
[![Downloads](https://img.shields.io/packagist/dt/symplify/coding-standard.svg?style=flat-square)](https://packagist.org/packages/symplify/coding-standard)

Set of smart and advanced sniffs PHP_CodeSniffer.

## Install

```bash
composer require symplify/coding-standard --dev
```

## Rules Overview

### Traits are forbidden. Prefer services and constructor injection

- [Architecture/ForbiddenTraitSniff](/src/Sniffs/Architecture/ForbiddenTraitSniff.php)
- `Symplify.Architecture.ForbiddenTrait`

:x:

```php
trait SomeTrait
{
}
```

### Implementation of interface should only contain its methods

- [Classes/EqualInterfaceImplementationSniff](/src/Sniffs/Classes/EqualInterfaceImplementationSniff.php)
- `Symplify.Classes.EqualInterfaceImplementation`

:x:

```php
interface SomeInterface
{
    public function run(): void;
}

final class SomeClass implements SomeInterface
{
    public function run(): void
    {
    }
    
    public function extra(): void
    {
    }
}
```

:+1:

```php
interface SomeInterface
{
    public function run(): void;
}

final class SomeClass implements SomeInterface
{
    public function run(): void
    {
    }
}
```


### Non-abstract class that implements interface should be final

- [Classes/FinalInterfaceSniff](/src/Sniffs/Classes/FinalInterfaceSniff.php)
- `Symplify.Classes.FinalInterface`

:x:

```php
class SomeClass implements SomeInterface
{
}
```

:+1:

```php
final class SomeClass implements SomeInterface
{
}
```

- Except for Doctrine entities, they cannot be final.


### Block comment should be used instead of one liner

- [Commenting/BlockPropertyCommentSniff](/src/Sniffs/Commenting/BlockPropertyCommentSniff.php)
- `Symplify.Commenting.BlockPropertyComment`

:x:

```php
class SomeClass
{
    /** @var int */
    public $count;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var int 
     */
    public $count;
}
```


### Constant should have docblock comment

- [Commenting/VarConstantCommentSniff](/src/Sniffs/Commenting/VarConstantCommentSniff.php)
- `Symplify.Commenting.VarConstantComment`

:x:

```php
class SomeClass
{
    private const EMPATH_LEVEL = 55;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var int
     */
    private const EMPATH_LEVEL = 55;
}
```


### Property should have docblock comment

- [Commenting/VarPropertyCommentSniff](/src/Sniffs/Commenting/VarPropertyCommentSniff.php)
- `Symplify.Commenting.VarPropertyComment`

:x:

```php
class SomeClass
{
    private $someProperty;
}
```

:+1:

```php
class SomeClass
{
    /**
     * @var int
     */
    private $someProperty;
}
```


### Controller should have max. 1 render method

- [Classes/ControllerRenderMethodLimitSniff](/src/Sniffs/Classes/ControllerRenderMethodLimitSniff.php)
- `Symplify.Classes.ControllerRenderMethodLimit`

:x:

```php
final class Controller
{
    public function defaultAction()
    {
    }
    
    public function listAction()
    {
    }
}
```

:+1:

```php
final class Controller
{
    public function defaultAction()
    {
    }
}
```

### Controller has to contain `__invoke()` method
 
- [Classes/InvokableControllerSniff](/src/Sniffs/Classes/InvokableControllerSniff.php)
- `Symplify.Classes.InvokableController`

:x:

```php
final class Controller
{
    public function defaultAction()
    {
    }
}
```

:+1:

```php
final class Controller
{
    public function __invoke()
    {
    }
}
```


### New class statement should not have empty parentheses

- [ControlStructures/NewClassSniff](/src/Sniffs/ControlStructures/NewClassSniff.php)
- `Symplify.ControlStructures.NewClass`

:x:

```php
$file = new File();
```
 
:+1:

```php
$file = new File;
$directory = new Directory([$file]);
```

### There should comments with valid code

- [Debug/CommentedOutCodeSniff](/src/Sniffs/Debug/CommentedOutCodeSniff.php)
- `Symplify.Debug.CommentedOutCode`

:x:

```php
// $file = new File;
// $directory = new Diretory([$file]);
```

### Debug functions should not be left in the code

- [Debug/DebugFunctionCallSniff](/src/Sniffs/Debug/DebugFunctionCallSniff.php)
- `Symplify.Debug.DebugFunctionCall`

:x:

```php
dump($value);
```

### Class name after new/instanceof should not start with slash.

- [Namespaces/ClassNamesWithoutPreSlashSniff](/src/Sniffs/Namespaces/ClassNamesWithoutPreSlashSniff.php)
- `Symplify.Namespaces.ClassNamesWithoutPreSlash`

:x:

```php
$file = new \File;
```
 
:+1:

```php
$file = new File;
```

### Abstract class should have prefix "Abstract"

- [Naming/AbstractClassNameSniff](/src/Sniffs/Naming/AbstractClassNameSniff.php)
- `Symplify.Naming.AbstractClassName`

:x:

```php
abstract class SomeClass
{
}
```

:+1:

```php
abstract class AbstractSomeClass
{
}
```


### Exception should have suffix "Exception"

- [Naming/ExceptionNameSniff](/src/Sniffs/Naming/ExceptionNameSniff.php)
- `Symplify.Naming.ExceptionName`

:x:

```php
class SomeClass extends Exception
{
}
```

:+1:

```php
class SomeClassException extends Exception
{
}
```

### Interface should have suffix "Interface"

- [Naming/InterfaceNameSniff](/src/Sniffs/Naming/InterfaceNameSniff.php)
- `Symplify.Naming.InterfaceName`

:x:

```php
interface Some
{
}
```

:+1:

```php
interface SomeInterface
{
}
```


### Trait should have suffix "Trait"

- [Naming/TraitNameSniff](/src/Sniffs/Naming/TraitNameSniff.php)
- `Symplify.Naming.TraitName`

:x:

```php
trait Some
{
}
```

:+1:

```php
trait SomeTrait
{
}
```


### Non-abstract class that extends TestCase should be final

- [PHPUnit\FinalTestCaseSniff]((/src/Sniffs/PHPUnit/FinalTestCaseSniff.php)
- `Symplify.PHPUnit.FinalTestCase`

:x:

```php
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
}
```

:+1:

```php
use PHPUnit\Framework\TestCase;

final class SomeTest extends TestCase
{
}
```


## Contributing

Send [issue](https://github.com/Symplify/Symplify/issues) or [pull-request](https://github.com/Symplify/Symplify/pulls) to main repository.
