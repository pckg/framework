<?php namespace Pckg\Framework\Console\Command;

use Pckg\Framework\Console\Command;

class LockProject extends Command
{

    public function handle()
    {
        if ($this->option('checkout')) {
            $this->checkout();
        } else {
            $this->lock();
        }

        return $this;
    }

    public function checkout()
    {
        $pckgLock = json_decode(file_get_contents(path('root') . 'pckg.json'), true);
        $composerJson = json_decode(file_get_contents(path('root') . 'composer.json'), true);

        foreach ($pckgLock as $packet => $config) {
            $lock = $config['from'] . '#' . $config['commit'];
            $set = $composerJson['require'][$packet];
            $from = $config['from'];
            $to = $config['to'];

            if ($lock != $set) {
                $to = $config['from'];
            }

            $this->output('Checking out ' . $packet . ' to ' . $to);
            $this->exec(
                [
                    'cd ' . path(
                        'root'
                    ) . 'vendor/' . $packet . ' && git checkout ' . $to . ' && git pull --ff',
                ]
            );
        }
    }

    public function lock()
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

        $composer = json_decode(file_get_contents(path('root') . 'composer.lock'), true);
        $composerJson = json_decode(file_get_contents(path('root') . 'composer.json'), true);
        $pckgLock = json_decode(file_get_contents(path('root') . 'pckg.json'), true);
        $requiredPackages = $composerJson['require'];

        foreach ($packets as $packet) {
            $path = path('root') . 'vendor' . path('ds') . 'pckg' . path('ds') . $packet;
            if (!is_dir($path)) {
                continue;
            }
            $this->output('Checking ' . $packet);
            $statusCommand = 'cd ' . $path . ' && git status';
            $logCommand = 'cd ' . $path . ' && git log';
            $outputs = $this->exec($statusCommand, false);

            if (strpos($outputs[0][0], 'On branch ') === 0) {
                $branch = substr($outputs[0][0], strlen('On branch '));
                $required = $requiredPackages['pckg/' . $packet] ?? null;

                if (!$required) {
                    foreach ($composer['packages'] as $composerPacket) {
                        if ($composerPacket['name'] == 'pckg/' . $packet) {
                            $required = $composerPacket['version'];
                        }
                    }
                }

                if ($required == 'dev-' . $branch) {
                    $this->output(
                        'Checked out to ' . $branch . ' branch, leaving untouched ...',
                        $branch != 'master' ? 'comment' : null
                    );
                } elseif (strpos($required, 'dev-' . $branch . '#')) {
                    /**
                     * Already locked ...
                     */
                } else {
                    $logOutputs = $this->exec($logCommand, false);
                    $commit = substr($logOutputs[0][0], strlen('commit '));
                    $lock = $required . '#' . $commit;

                    $this->output('Locking to ' . $lock, 'info');
                    $composerJson['require']['pckg/' . $packet] = $lock;
                    $pckgLock['pckg/' . $packet] = [
                        'from' => $required,
                        'to' => $branch,
                        'commit' => $commit,
                    ];
                }
            } else {
                dd('not on branch');
            }
            $this->output();
        }

        file_put_contents(
            path('root') . 'composer.json',
            str_replace(
                ['    ', "\n"],
                ['  ', "\r\n"],
                json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            )
        );
        file_put_contents(
            path('root') . 'pckg.json',
            str_replace(
                ['    ', "\n"],
                ['  ', "\r\n"],
                json_encode($pckgLock, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            )
        );
        $this->output('Wrote changes to composer.json');
    }

    protected function configure()
    {
        $this->setName('project:lock')
             ->setDescription(
                 'Lock composer dependencies (composer.json) as currently checked out'
             )
             ->addOptions(
                 [
                     'checkout' => 'Checkout to branch listed in pckg.json',
                 ]
             );
    }

}
