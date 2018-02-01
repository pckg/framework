<?php namespace Pckg\Framework\Middleware;

class ShowNiceErrorPage
{

    public function handle()
    {
        return response()->setOutput(view('Pckg/Framework:error/default'));
    }

}