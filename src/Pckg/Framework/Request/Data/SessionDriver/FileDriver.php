<?php namespace Pckg\Framework\Request\Data\SessionDriver;

use SessionHandler;

class FileDriver extends SessionHandler
{

    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        if (!($SID = session_id())) {
            session_set_save_handler([$this, 'open'],
                                     [$this, 'close'],
                                     [$this, 'read'],
                                     [$this, 'write'],
                                     [$this, 'destroy'],
                                     [$this, 'gc']);

            register_shutdown_function('session_write_close');

            /**
             * Keep session data in server in client for 24h by default.
             */
            $time = 24 * 60 * 60;
            ini_set('session.gc_maxlifetime', $time);
            session_set_cookie_params($time);

            session_start(/*[
                              'cookie_lifetime' => $time,
                          ]*/);

            /**
             * Allow session to be reused for 90 seconds.
             */
            if (isset($_SESSION['deactivated']) && $_SESSION['deactivated'] + 90 < time()) {
                session_destroy();
                throw new Exception('Using inactive session');
            }
        }
    }

}