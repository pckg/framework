<?php namespace Pckg\Framework\Console\Command;

use Exception;
use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CreatePckgProject
 */
class DeployProject extends Command
{

    public function handle()
    {
        $remote = config('pckg.framework.' . DeployProject::class . '.remotes.' . $this->option('remote'));

        if (!$remote) {
            throw new Exception("Remote does not exitst");
        }

        if (!$this->option('no-test')) {
            $this->exec(['php console project:test']);
        }

        $this->output('Estamblishing SSH connection to ' . $remote['host'] . '.');
        $sshConnection = ssh2_connect($remote['host'], $remote['port']);
        $this->output('SSH connection estamblished.');

        /**
         * Authenticate with username and password or username and key.
         */
        if (!isset($remote['key'])) {
            if (!ssh2_auth_password(
                $sshConnection,
                $remote['username'],
                $remote['password']
            )
            ) {
                throw new Exception('Cannot estamblish SSH connection to remote with username and password');
            }

        } elseif (!ssh2_auth_pubkey_file(
            $sshConnection,
            $remote['username'],
            $remote['key'] . '.pub',
            $remote['key'],
            ''
        )
        ) {
            throw new Exception('Cannot estamblish SSH connection to remote with username and key');

        }

        $paths = $remote['root'];
        if (!is_array($paths)) {
            $paths = ['default' => $paths];
        }
        foreach ($paths as $key => $path) {
            $commands = [
                'cd ' . $path                           => 'Changing root directory',
                // 'php ' . $path . 'console project:down'     => 'Putting project offline',
                'php ' . $path . 'console project:pull' => 'Executing project:pull',
                // 'php ' . $path . 'console migrator:install' => 'Installing migrations',
                // 'php ' . $path . 'console project:up'       => 'Putting project up',
                'php ' . $path . 'console cache:clear'  => 'Clearing cache',
            ];
            $this->output('Deploying ' . $key);
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
                 InputOption::VALUE_REQUIRED
             )
             ->addOptions(
                 [
                     'no-test' => 'Disable testing',
                 ],
                 InputOption::VALUE_NONE
             );
    }

}
