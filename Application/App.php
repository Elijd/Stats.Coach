<?php
/**
 * Created by IntelliJ IDEA.
 * User: richardmiles
 * Date: 2/25/18
 * Time: 3:20 AM
 */

namespace App;

use Carbon\Route;
use Carbon\View;

abstract class App extends Route
{
    /**
     * App constructor. If no uri is set than
     * the Route constructor will execute the
     * defaultRoute method defined below.
     * @param null $structure
     * @throws \Mustache_Exception_InvalidArgumentException
     * @throws \Carbon\Error\PublicAlert
     */

    public
    function fullPage()
    {
        return catchErrors(function (string $file) {
            return include APP_VIEW . $file;
        });
    }

    public
    function wrap()
    {
        return function (string $file): bool {
            return View::content(APP_VIEW . $file);
        };
    }

    public
    function MVC()
    {
        return function (string $class, string $method, array &$argv = []) {
            return MVC($class, $method, $argv);         // So I can throw in ->structure($route->MVC())-> anywhere
        };
    }

    public
    function events($selector = '')
    {
        return function ($class, $method, $argv) use ($selector) {
            global $alert, $json;

            if (false === $argv = CM($class, $method, $argv)) {
                return false;
            }

            if (!file_exists(SERVER_ROOT . $file = (APP_VIEW . $class . DS . $method . '.hbs'))) {
                $alert = 'Mustache Template Not Found ' . $file;
            }

            if (!\is_array($alert)) {
                $alert = array();
            }

            $json = array_merge($json, [
                'Errors' => $alert,
                'Event' => 'Controller->Model',   // This doesn't do anything.. Its just a mental note when I look at the json's in console (controller->model only)
                'Model' => $argv,
                'Mustache' => DS . $file,
                'Widget' => $selector
            ]);

            print PHP_EOL . json_encode($json) . PHP_EOL; // new line ensures it sends through the socket

            return true;
        };
    }
}
