<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class ComposerProject extends Command
{

    public function handle()
    {
        $packets = [
            'pckg/auth',
            'pckg/collection',
            'pckg/concept',
            'pckg/database',
            'pckg/framework',
            'pckg/generic',
            'pckg/htmlbuilder',
            'pckg/import',
            'pckg/mail',
            'pckg/manager',
            'pckg/migrator',
            'pckg/payment',
            'pckg/queue',
            'pckg/tempus',
            'pckg/translator',
            'pckg/charts',
            'pckg/locale',
            'pckg/cache',
            'pckg/helpers-js',
            'pckg/helpers-less',
            'pckg-app/impero',
            'pckg-app/pendo-api',
            'pckg-app/impero-api',
            'pckg-app/mailo-api',
            'pckg-app/center-api',
            'pckg-app/medium-api',
            'pckg-app/api',
        ];

        $clean = ['nothing to commit, working directory clean', 'nothing to commit, working tree clean'];
        $ahead = ['Your branch is ahead of '];
        $composer = json_decode(file_get_contents(path('root') . 'composer.lock'), true);
        $outdated = false;

        foreach ($packets as $packet) {
            $path = path('root') . 'vendor' . path('ds') . $packet;
            if (!is_dir($path)) {
                continue;
            }
            $this->output('Checking ' . $packet);
            $statusCommand = 'cd ' . $path . ' && git status';
            $diffCommand = 'cd ' . $path . ' && git diff';
            $pullCommand = 'cd ' . $path . ' && git pull --ff';
            $pushCommand = 'cd ' . $path . ' && git push';
            $logCommand = 'cd ' . $path . ' && git log';
            $commitCommand = 'cd ' . $path . ' && git add . --all && git commit -m';
            $outputs = $this->exec($statusCommand, false);
            if (isset($outputs[0])) {
                $output = $outputs[0];
                $end = end($output);
                if (in_array($end, $clean)) {
                    $this->output('Packet is clean.');
                    $this->exec($pullCommand);

                    if (isset($output[1])) {
                        foreach ($ahead as $a) {
                            if (strpos($output[1], $a) === 0) {
                                if ($this->askConfirmation('Push changes?')) {
                                    $this->exec($pushCommand);
                                }
                                break;
                            }
                        }
                    }
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
                        if ($this->askConfirmation('Push commited changes?')) {
                            $this->exec($pushCommand);
                        }
                        $this->exec($pullCommand);
                    } else {
                        $this->output('Skipping commit.');
                        $this->exec($pullCommand);
                    }
                }
                foreach ($composer['packages'] ?? [] as $composerPacket) {
                    if ($composerPacket['name'] == $packet) {
                        $installed = $composerPacket['source']['reference'];
                        $logOutput = $this->exec($logCommand, false);
                        $git = str_replace('commit ', '', $logOutput[0][0]);
                        $this->output('Installed: ' . $installed);
                        $this->output('Git:       ' . $git);
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
