<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Post extends Lazy
{

    public function setFromGlobals()
    {
        $input = file_get_contents('php://input');
        if ($input && (strpos($input, '{') === 0 && strrpos($input, '}') === strlen($input) - 1)) {
            $input = json_decode($input, true);
        } elseif ($input) {
            parse_str($input, $input);
        } else {
            $input = [];
        }

        if (!$input && $_POST) {
            /**
             * Why is php input sometimes empty?
             */
            $input = $_POST;
        }

        $this->setData($_POST);

        return $this;
    }
}
