<?php

namespace Pckg\Framework\Request\Data;

use Pckg\Framework\Helper\Lazy;

class Session extends Lazy
{

    public function init()
    {
        $SID = session_id();

        if (empty($SID)) {
            session_set_cookie_params(7 * 24 * 60 * 60, '/');
            //session_start();
        }

        $this->data = &$_SESSION;
    }

}

?>