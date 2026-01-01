<?php

// config for Adultdate/FilamentShop
return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for the shop. This will be used for displaying
    | prices and formatting monetary values.
    |
    */
    'currency' => env('SHOP_CURRENCY', 'SEK'),

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale for the shop. This affects number formatting,
    | dates, and other locale-specific features.
    |
    */
    'locale' => env('SHOP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Product Images Disk
    |--------------------------------------------------------------------------
    |
    | The filesystem disk to use for storing product images.
    | Make sure this disk is configured in config/filesystems.php
    |
    */
    'product_images_disk' => env('SHOP_PRODUCT_IMAGES_DISK', 'product-images'),

    /*
    |--------------------------------------------------------------------------
    | Enable Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the shop.
    |
    */
    'features' => [
        'brands' => true,
        'categories' => true,
        'customers' => true,
        'orders' => true,
        'products' => true,
    ],
];
