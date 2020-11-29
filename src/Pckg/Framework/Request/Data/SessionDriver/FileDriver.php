<?php namespace Pckg\Framework\Request\Data\SessionDriver;

use SessionHandler;
use \Exception;

class FileDriver extends SessionHandler
{

    const PHPSESSID = 'SID'; // PHPSESSID

    const SIGNATURE = 'SIGNATURE';

    const SECURE = true;

    const UUIDLENGTH = 36;

    const DURATION = 24 * 60 * 60;

    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        /**
         * Set session handlers.
         */
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
        ini_set('session.gc_maxlifetime', static::DURATION);
        session_name(static::PHPSESSID);
        session_set_cookie_params([
            'lifetime' => static::DURATION,
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            //'samesite' => 'Lax',
        ]);

        /**
         * Read parameters for session.
         */
        $PHPSESSID = $_COOKIE[static::PHPSESSID] ?? null;
        $SID = session_id();

        /**
         * Nullify session.
         */
        if (!$SID) {
            $SID = null;
        }

        /**
         * We do not need to always start a session?
         */
        if (!$PHPSESSID) {
            //return;
        }

        /**
         * Start a new session.
         */
        $PHPSESSIDSECURE = $this->startSession($SID, $PHPSESSID);

        /**
         * Cookie-defined session should have signature fields set.
         */
        if (static::SECURE && !$PHPSESSIDSECURE && !array_key_exists(static::PHPSESSID . static::SIGNATURE, $_SESSION)) {
            $this->destroyCookieSession('Missing session signature! ' . $PHPSESSID);
        } /**
         * Cookie defined session should have valid signature.
         */
        else if (static::SECURE && !$PHPSESSIDSECURE && !auth()->hashedPasswordMatches($_SESSION[static::PHPSESSID . static::SIGNATURE], $PHPSESSID)) {
            $this->destroyCookieSession('Invalid session signature!');
        }

        /**
         * Allow session to be reused for 90 seconds.
         */
        if (isset($_SESSION['deactivated']) && $_SESSION['deactivated'] + 90 < time()) {
            $this->destroyCookieSession('Using inactive session');
        }
    }

    protected function startSession($SID = null, $PHPSESSID = null)
    {
        $readAndClose = ($SID || $PHPSESSID) && (in_array('session:close', router()->get('tags')) || (!get('lang') && !post()->all()) || request()->isSearch());

        /**
         * Start a new session.
         */
        $started = session_start([
            'cookie_lifetime' => static::DURATION,
            'read_and_close' => $readAndClose,
        ]);

        if (!$started) {
            error_log('Cannot start session?');
        }

        /**
         * Start new session procedure.
         */
        if ($SID || $PHPSESSID) {
            return null;
        }

        /**
         * Sign new sessions.
         */
        $PHPSESSID = session_id();
        $PHPSESSIDSECURE = auth()->hashPassword($PHPSESSID);

        /**
         * Start / override session.
         */
        $_SESSION = [
            static::PHPSESSID . static::SIGNATURE => $PHPSESSIDSECURE,
        ];

        return $PHPSESSIDSECURE;
    }

    /**
     * @param null $message
     * @throws Exception
     */
    public function destroyCookieSession($message = null)
    {
        try {
            session_destroy();
        } catch (\Throwable $e) {
            error_log(exception($e));
        }
        $_SESSION = [];
        $PHPSESSIDSECURE = $this->startSession();
        //cookie()->set(static::PHPSESSID, null);

        if ($message) {
            error_log($message);
        }

        return $PHPSESSIDSECURE;
    }

}