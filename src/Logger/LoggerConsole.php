<?php

namespace Z3\T3build\Logger;

use Psr\Log\AbstractLogger;

class LoggerConsole extends AbstractLogger
{
    private $messages = '';

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->messages .= 'LOGGER: ' . $level . ' => ' . $message . "\n";
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
