<?php namespace Pckg\Framework\Router\Console;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputOption;

class ListRoutes extends Command
{

    protected function configure()
    {
        $this->setName('router:list')
             ->setDescription(
                 'List all available routes'
             )
             ->addOptions([
                              'search' => 'List only routes that match',
                          ],
                          InputOption::VALUE_OPTIONAL);
    }

    public function handle()
    {
        $table = new Table($this->output);

        $headers = [
            'Name',
            'URL',
            'Action',
            'Methods',
        ];

        $data = [];
        $lastProvider = null;
        $search = $this->option('search');
        foreach (router()->getRoutes() as $routes) {
            foreach ($routes as $route) {
                if (array_key_exists('provider', $route) && $route['provider'] != $lastProvider) {
                    $data[] = new TableSeparator();
                    $data[] = [new TableCell("\n# " . $route['provider'] . '', ['colspan' => 3])];
                    $data[] = new TableSeparator();
                    $lastProvider = $route['provider'];
                }

                $row = [
                    'name'    => $route['name'],
                    'url'     => substr($route['url'], 0, 40),
                    'action'  => $route['controller'] . ' @ ' . $route['view'],
                    'methods' => 'POST|GET',
                ];

                if ($search) {
                    $found = false;
                    foreach ($row as $val) {
                        if (strpos($val, $search) !== false) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        continue;
                    }
                }

                $data[] = $row;
            }
        }

        $table->setHeaders($headers)
              ->setRows($data)
              ->setStyle('compact');

        $table->render();
    }

}