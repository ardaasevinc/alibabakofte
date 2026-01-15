<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Varsayılan dosya sistemi diskiniz burada belirlenir. 'local', 'public', 
    | veya 'uploads' gibi ayarlanabilir.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'uploads'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Burada farklı driver'lar için birden fazla disk tanımlayabilirsiniz.
    | local, ftp, sftp, s3 gibi desteklenen driver türleri mevcuttur.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'uploads' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'url' => env('APP_URL') . '/uploads',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],


        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Eğer `php artisan storage:link` komutu çalıştırılırsa, burada ayarlanan
    | sembolik linkler oluşturulur. Ancak biz doğrudan public/uploads kullandığımız
    | için buraya çok da ihtiyacımız yok.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
