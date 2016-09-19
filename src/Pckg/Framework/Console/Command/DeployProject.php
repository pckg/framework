<?php namespace Pckg\Framework\Console\Command;

use Exception;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class CreatePckgProject
 */
class DeployProject extends Command
{

    public function handle()
    {
        /**
         * Estamblish SSH connection,
         * put site under maintenance,
         * pull chamges from git,
         * install composer dependencies,
         * execute migrations,
         * put site online
         */
        $remote = config('pckg.framework.' . DeployProject::class . '.remotes.default');
        $path = $remote['root'];
        $commands = [
            'cd ' . $path                               => 'Changing root directory',
            'php ' . $path . 'console project:down'     => 'Putting project offline',
            'php ' . $path . 'console project:pull'     => 'Executing project:pull',
            'php ' . $path . 'console migrator:install' => 'Installing migrations',
            'php ' . $path . 'console project:up'       => 'Putting project up',
            'php ' . $path . 'console cache:clear'      => 'Clearing cache',
        ];
        $this->output('Estamblishing SSH connection.');
        $sshConnection = ssh2_connect($remote['host'], $remote['port']);
        $this->output('SSH connection estamblished.');

        /**
         * Authenticate.
         */
        if (!ssh2_auth_password($sshConnection, $remote['username'], $remote['password'])) {
            throw new Exception('Cannot estamblish SSH connection to remote');
        }

        foreach ($commands as $command => $notice) {
            $this->output($notice);

            $stream = ssh2_exec($sshConnection, $command);

            $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

            stream_set_blocking($errorStream, true);
            stream_set_blocking($stream, true);

            $errorStreamContent = stream_get_contents($errorStream);
            $streamContent = stream_get_contents($stream);
            $this->output($errorStreamContent . "\n" . $streamContent);
        }

        $this->output('Done!');
    }

    protected function configure()
    {
        $this->setName('project:deploy')
             ->setDescription('Deploy project')
             ->addOptions(
                 [
                     'remote' => 'Set remote server',
                 ],
                 InputArgument::OPTIONAL
             );
    }

}
