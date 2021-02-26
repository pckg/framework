<?php

namespace Pckg\Framework\Request\Data\PostResolver;

class Globals implements PostSource
{

    public function readFromSource(): array
    {
        $input = file_get_contents('php://input');
        if ($input && (strpos($input, '{') === 0 && strrpos($input, '}') === strlen($input) - 1)) {
            $input = json_decode($input, true);
        } elseif ($input) {
            parse_str($input, $input);
        } else {
            $input = [];
        }

        /**
         * Why is php input sometimes empty?
         * On XHR requests?
         */
        if (!$input && $_POST) {
            $input = $_POST;
        }

        return $input;
    }

    public function writeToSource(array $data): bool
    {
        $_POST = $data;

        return true;
    }
}
