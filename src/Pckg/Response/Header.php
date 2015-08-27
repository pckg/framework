<?php

namespace Pckg\Response;

class Header
{
    public static function header($header, $value)
    {
        header($header . ":" . $value);
    }

    public static function location($location)
    {
        self::header("Location", $location);
    }

    public static function refresh($refresh)
    {
        self::header("Refresh", $refresh);
    }

    public static function contentType($contentType)
    {
        self::header("Content-Type", $contentType);
    }

    public static function contentLength($contentLength)
    {
        self::header("Content-Length", $contentLength);
    }

    public static function contentDescription($contentDescription)
    {
        self::header("Content-Description", $contentDescription);
    }

    public static function contentDisposition($contentDisposition)
    {
        self::header("Content-Disposition", $contentDisposition);
    }

    public static function cacheControl($cacheControl)
    {
        self::header("Cache-Control", $cacheControl);
    }

    public static function lastModified($lastModified)
    {
        self::header("Last-Modified", $lastModified);
    }

    public static function pragma($pragma)
    {
        self::header("Pragma", $pragma);
    }

    public static function expires($expires)
    {
        self::header("Expires", $expires);
    }
}