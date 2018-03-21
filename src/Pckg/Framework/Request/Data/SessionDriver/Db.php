<?php namespace Pckg\Framework\Request\Data\SessionDriver;

use Pckg\Framework\Request\Session\Record\Session;
use SessionHandler;

class Db extends SessionHandler
{

    protected $record;

    public function __construct()
    {
        $this->register();
    }

    function register()
    {
        session_set_save_handler([$this, 'open'],
                                 [$this, 'close'],
                                 [$this, 'read'],
                                 [$this, 'write'],
                                 [$this, 'destroy'],
                                 [$this, 'gc']);

        register_shutdown_function('session_write_close');

        if (!($SID = session_id())) {
            /**
             * Keep session data in server in client for 1h by default.
             */
            $time = 7 * 24 * 60 * 60;
            ini_set('session.gc_maxlifetime', $time);
            session_set_cookie_params($time);

            session_start(/*[
                              'cookie_lifetime' => $time,
                          ]*/);
        }

        message('Creating Session and Flash');
    }

    public function open($savePath, $name)
    {
        $sessionId = session_id();

        $this->record = Session::getAndUpdateOrCreate(['hash' => $sessionId], ['timestamp' => date('Y-m-d H:i:s')]);

        return !!$this->record;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        return $this->record->data;
    }

    public function write($sessionId, $data)
    {
        $ok = $this->record->setAndSave(['timestamp' => date('Y-m-d H:i:s'), 'data' => $data]);

        return !!$ok;
    }

    public function destroy($sessionId)
    {
        $ok = $this->record->delete();

        return !!$ok;
    }

    public function gc($maxLifetime)
    {
        return true;
    }

}