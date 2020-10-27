<?php namespace Pckg\Framework\Request\Data\SessionDriver;

use SessionHandler;
use \Exception;

class FileDriver extends SessionHandler
{

    const PHPSESSID = 'PHPSESSID';

    const SIGNATURE = 'SIGNATURE';

    const SECURE = true;

    const UUIDLENGTH = 36;

    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        /**
         * Read parameters for session.
         */
        $PHPSESSID = $_COOKIE[static::PHPSESSID] ?? null;
        $PHPSESSIDSECURE = null;
        $SID = session_id();

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
        $time = 24 * 60 * 60;
        ini_set('session.gc_maxlifetime', $time);
        session_set_cookie_params([
            'lifetime' => $time,
            'path' => '/',
            'domain' => '',
            'secure' => 'true',
            'httponly' => 'true',
            'samesite' => 'Lax',
        ]);

        /**
         * Old compatibility layer, will be removed.
         */
        if (!$SID) {
            $SID = null;
        }

        if (false && static::SECURE && $SID && strlen($SID) !== static::UUIDLENGTH) {
            $this->destroyCookieSession('Invalid session length');
            $SID = $PHPSESSID = null;
        } else if (false && static::SECURE && $PHPSESSID && strlen($PHPSESSID) !== static::UUIDLENGTH) {
            $this->destroyCookieSession('Invalid cookie session length');
            $SID = $PHPSESSID = null;
        }

        /**
         * Start new session procedure.
         */
        if (!$SID) {
            /**
             * Define parameters for new session.
             */
            if (!$PHPSESSID) {
                $PHPSESSID = uuid4();
                $PHPSESSIDSECURE = auth()->hashPassword($PHPSESSID);
                session_id($PHPSESSID);
            }

            /**
             * Start a new session.
             */
            $readAndClose = !$PHPSESSIDSECURE;
            session_start([
                'cookie_lifetime' => $time,
                'read_and_close' => false && $readAndClose,
            ]);
        }

        /**
         * Cookie-defined session should have signature fields set.
         */
        if (static::SECURE && !$PHPSESSIDSECURE && !array_key_exists(static::PHPSESSID . static::SIGNATURE, $_SESSION)) {
            $this->destroyCookieSession('Missing session signature! ' . $PHPSESSID);
        }

        /**
         * Cookie defined session should have valid signature.
         */
        if (static::SECURE && !$PHPSESSIDSECURE && !auth()->hashedPasswordMatches($_SESSION[static::PHPSESSID . static::SIGNATURE], $PHPSESSID)) {
            $this->destroyCookieSession('Invalid session signature!');
        }

        /**
         * Allow session to be reused for 90 seconds.
         */
        if (isset($_SESSION['deactivated']) && $_SESSION['deactivated'] + 90 < time()) {
            $this->destroyCookieSession('Using inactive session');
        }

        /**
         * Start / override session.
         */
        if ($PHPSESSIDSECURE) {
            $_SESSION = [
                static::PHPSESSID . static::SIGNATURE => $PHPSESSIDSECURE,
            ];
        }
    }

    /**
     * @param null $message
     * @throws Exception
     */
    public function destroyCookieSession($message = null)
    {
        session_destroy();
        //cookie()->set(static::PHPSESSID, null);

        if (!$message) {
            return;
        }

        error_log($message);
    }

}