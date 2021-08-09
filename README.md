DaData для WebSpace Engine
====
######(Плагин)

Плагин для интеграции с сервисом подсказок и стандартизаций DaData

#### Установка
Docker + BASH:
```
% ./composer install
```

Поместить в папку `plugin` и подключить в `index.php` добавив строку:
```php
// s3proto plugin
$plugins->register(new \Plugin\DaData\DaDataPlugin($container));
```

#### License
Licensed under the MIT license. See [License File](LICENSE.md) for more information.
