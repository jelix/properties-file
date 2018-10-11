Some classes to read and write properties files. 

Properties files are like Java Properties file. The implemented format is using
to store locales for an application made with [Jelix](https://jelix.org), 
a PHP Framework.

# installation

You can install it from Composer. In your project:

```
composer require "jelix/propertiesfile"
```

# Usage

You have two classes: `Properties` which is a container for key/value pairs.
And a `Reader` to  parse a properties file.

```php

use \Jelix\PropertiesFile\Properties;
use \Jelix\PropertiesFile\Parser;

$properties = new Properties();

$reader = new Parser();
$reader->parseFromFile('file.properties', $properties);

$value = $properties->get('a_key');
$value = $properties->a_key;
$value = $properties['a_key'];

$properties->set('a_key', 'new_value');
$properties->a_key = 'new_value';
$properties['a_key'] = 'new_value';

```

# History

The parser is based on a class, jBundle coming from the [Jelix Framework](http://jelix.org)
until Jelix 1.6, and has been released in 2018 into a separate repository as Jelix\PropertiesFile\Parser.

