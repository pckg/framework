<?php

namespace Pckg\Framework\Helper;

/**
 * Class Retry
 * @package Pckg\Framework\Helper
 */
class Retry
{

    /**
     * @var int|null
     */
    protected $retry = 1;

    /**
     * @var int|null
     */
    protected $interval;

    /**
     * @var callable|null
     */
    protected $onError;

    /**
     * @var callable|null
     */
    protected $check;

    /**
     * @var
     */
    protected $heartbeat;

    /**
     * Define how many times we should retry the task.
     *
     * @param int $times
     * @return $this
     */
    public function retry(int $times)
    {
        $this->retry = $times;

        return $this;
    }

    /**
     * @param callable $heartbeat
     * @return $this
     */
    public function heartbeat(callable $heartbeat)
    {
        $this->heartbeat = $heartbeat;

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function check(callable $callable)
    {
        $this->check = $callable;

        return $this;
    }

    /**
     * Define how many seconds should we wait between retries.
     *
     * @param int $interval
     * @return $this
     */
    public function interval(int $interval)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function onError(callable $callable)
    {
        $this->onError = $callable;

        return $this;
    }

    /**
     * Try to execute the task.
     *
     * @return bool
     */
    public function make(callable $task)
    {
        $repeat = false;
        $tries = 0;
        $exceptions = collect();
        do {
            /**
             * Make heart beat at the beginning to have as much time as possible.
             */
            if ($this->heartbeat) {
                ($this->heartbeat)();
            }

            $repeat = false;
            $tries++;

            try {
                /**
                 * Call task.
                 */
                $response = $task($tries - 1);

                try {
                    if ($this->heartbeat) {
                        ($this->heartbeat)();
                    }
                } catch (\Throwable $e) {
                    error_log(exception($e));
                }

                return $response;
            } catch (\Throwable $e) {
                $exceptions->push(exception($e));
                if ($this->onError) {
                    $break = ($this->onError)($e);
                    if ($break) {
                        throw $e;
                    }
                }
            }

            /**
             * Make heart beat at the beginning to have as much time as possible.
             */
            if ($this->heartbeat) {
                ($this->heartbeat)();
            }

            /**
             * Check if we should try to repeat the task.
             */
            if ($this->shouldRepeat($tries)) {
                $repeat = true;
            }

            /**
             * Sleep between repeats.
             */
            if ($repeat && $this->interval) {
                sleep($this->interval);
            }

            /**
             * Make heartbeat right after sleep.
             */
            if ($this->heartbeat) {
                ($this->heartbeat)();
            }
        } while ($repeat);

        throw new \Exception('Retry make failed after ' . $tries . ' tries: ' . $exceptions->unique()->implode(', '));
    }

    /**
     * @param $tries
     * @return bool
     */
    protected function shouldRepeat($tries)
    {
        if ($this->check && !($this->check)($tries)) {
            return false;
        }

        /**
         * Retry when
         */
        if ($this->retry && $tries <= $this->retry) {
            return true;
        }

        return false;
    }
}
