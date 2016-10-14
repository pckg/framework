<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class ComposerProject extends Command
{

    public function handle()
    {
        $packets = [
            'auth',
            'collection',
            'concept',
            'database',
            'framework',
            'generic',
            'htmlbuilder',
            'import',
            'mail',
            'manager',
            'migrator',
            'payment',
            'queue',
            'tempus',
            'translator',
            'charts',
        ];

        $clean = 'nothing to commit, working directory clean';
        $composer = json_decode(file_get_contents(path('root') . 'composer.lock'), true);
        $outdated = false;

        foreach ($packets as $packet) {
            $this->output('Checking ' . $packet);
            $path = path('root') . 'vendor' . path('ds') . 'pckg' . path('ds') . $packet;
            $statusCommand = 'cd ' . $path . '; git status';
            $diffCommand = 'cd ' . $path . '; git diff';
            $pullCommand = 'cd ' . $path . '; git pull --ff';
            $pushCommand = 'cd ' . $path . '; git push';
            $logCommand = 'cd ' . $path . '; git log';
            $commitCommand = 'cd ' . $path . '; git add . --all; git commit -m';
            $outputs = $this->exec($statusCommand, false);
            if (isset($outputs[0])) {
                $output = $outputs[0];
                if (isset($output[2]) && $output[2] === $clean) {
                    $this->output('Packet is clean.');
                } else {
                    $this->output('Packet is changed.');
                    $this->exec($statusCommand);
                    $this->exec($diffCommand);
                    if ($message = $this->askQuestion(
                        'Enter commit message (or leave empty if you want to skip commit)'
                    )
                    ) {
                        $this->exec($pullCommand);
                        $this->output('Committing changes.');
                        $this->exec($commitCommand . ' ' . $message);
                        $this->output('Changes commited.');
                        if ($this->askConfirmation('Push changes?')) {
                            $this->exec($pushCommand);
                        }
                    } else {
                        $this->output('Skipping commit.');
                    }
                }
                foreach ($composer['packages'] as $composerPacket) {
                    if ($composerPacket['name'] == 'pckg/' . $packet) {
                        $installed = $composerPacket['source']['reference'];
                        $logOutput = $this->exec($logCommand, false);
                        $git = str_replace('commit ', '', $logOutput[0][0]);
                        $this->output('Installed: ' . $installed);
                        $this->output('Git: ' . $git);
                        if ($git != $installed) {
                            $this->output('Run composer update or project update command');
                            $outdated = true;
                        }
                    }
                }
            }
            $this->output();
        }

        if ($outdated) {
            $this->output('Some packages are outdated!');
        }
    }

    protected function configure()
    {
        $this->setName('project:composer')
             ->setDescription(
                 'Check changes for pckg dependencies'
             );
    }

}
