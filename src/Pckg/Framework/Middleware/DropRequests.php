<?php

namespace Pckg\Framework\Middleware;

class DropRequests
{

    public function handle()
    {
        $extensions = [
            'jpg',
            'jpeg',
            'png',
            'bmp',
            'css',
            'less',
            'js',
        ];

        $uri = strtolower(server('REQUEST_URI'));
        foreach ($extensions as $extension) {
            if (strrpos($uri, $extension) + strlen($extension) === strlen($uri)) {
                response()->code(404)->respond();
            }
        }
    }
}
