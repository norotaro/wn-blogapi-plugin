<?php

namespace Norotaro\BlogApi;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = [
        'Winter.Blog',
    ];

    public function boot()
    {
        \App::error(function (Classes\ApiException $apiException) {
            return [
                'code' => $apiException->getStatusCode(),
                'message' => $apiException->getMessage(),
            ];
        });
    }
}
