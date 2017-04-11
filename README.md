Cloudflare API
==================

Компонент Yii2 для работы с api v4.0 сервиса https://www.cloudflare.com/

Минимальные требования — Yii2

Пример использования:

```php
'components' => [
        'cloudflare' => [
            'class'         => 'biozahard\cloudflare\CloudflareApi',
            'apiendpoint'   => 'https://api.cloudflare.com/client/v4/',
            'authkey'       => '5gds0kfdsc024ndsofsj049jisdofjsd034jw',
            'authemail'     => 'admin@mail.com',
            'sites'         => [
                'mywebsite.com',
                'thebest-country.ua',
                'anotheronesite.biz',
            ],
        ],
]
```

***

Лицензия: LGPL v3 or later
