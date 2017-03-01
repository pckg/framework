<?php namespace Pckg\Framework\Router\Console;

use Pckg\Framework\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class ListRoutes extends Command
{

    protected function configure()
    {
        $this->setName('router:list')
             ->setDescription(
                 'List all available routes'
             );
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
        foreach (router()->getRoutes() as $url => $routes) {
            foreach ($routes as $route) {
                $data[] = [
                    'name'    => $route['name'],
                    'url'     => $route['url'],
                    'action'  => $route['controller'] . ' @ ' . $route['view'],
                    'methods' => 'POST|GET',
                ];

                if (array_key_exists('provider', $route) && $route['provider'] != $lastProvider) {
                    $data[] = new TableSeparator();
                    $data[] = [new TableCell($route['provider'], ['colspan' => 3])];
                    $data[] = new TableSeparator();
                    $lastProvider = $route['provider'];
                }
            }
        }

        $table->setHeaders($headers)
              ->setRows($data);

        $table->render();
    }

}