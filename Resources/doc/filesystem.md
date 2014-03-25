Working with filesystem
=======================

Publero FileBundle uses gaufrette. To access FileBundle`s filesystem simply get service `publero_file.filesystem`
from *service container*.

``` php
<?php
$filesystem = $container->get('publero_file.filesystem');
```

[Full configuration reference](configuration_reference.md)

Visit https://github.com/KnpLabs/KnpGaufretteBundle for more information about how to use *gaufrette filesystem*.
