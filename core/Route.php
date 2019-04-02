<?php
namespace Core;

class Route
{
    private $routes;

    public function __construct(array $routes)
    {
        /* $this->routes = $routes; */
        $this->setRoutes($routes);
        $this->run();
    }

    private function setRoutes($routes)
    {
        foreach ($routes as $route)
        {
            $explode = explode('@', $route[1]);
            $result = [$route[0], $explode[0], $explode[1]];
            $new_routes[] = $result;
        }
        $this->routes = $new_routes;
    }

    private function getRequest()
    {
        $obj = new \stdClass;

        foreach($_GET as $key => $value)
        {
            $obj->get->$key = $value;
        }

        foreach($_POST as $key => $value)
        {
            $obj->post->$key = $value;
        }

        return $obj;
    }

    private function getUrl()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    private function run()
    {
        $url = $this->getUrl();
        $urlArray = explode('/', $url);
/*
        print_r($urlArray);
        echo '<br>';
*/
        foreach($this->routes as $route)
        {
            $routeArray = explode('/', $route[0]);
/*
            print_r($routeArray);
            echo '<br>';
*/
            for($i = 0; $i < count($routeArray); $i++)
            {
                if((strpos($routeArray[$i], '{') !== false) && (count($urlArray) == count($routeArray)))
                {
                    $routeArray[$i] = $urlArray[$i];
                    $parameter[] = $urlArray[$i];
                }
                $route[0] = implode($routeArray, '/');
            }

            if($url == $route[0])
            {
                $found = true;
                $controller = $route[1];
                $action = $route[2];
/*
                echo '<br>Rota válida!' . '<br>';
                echo 'Rota: ' . $route[0] . '<br>';
                echo 'Controller: ' . $route[1] . '<br>';
                echo 'Ação: ' . $route[2] . '<br>';
                echo 'Parâmetro: ' . $parameter[0] . '<br>';
*/
                break;
            }
/*
            else
            {
                echo '<br>Rota inválida!';

                break;
            }
*/
        }

        if($found)
        {
            $controller = Container::newController($controller);

            switch(count($parameter))
            {
                case 1:
                    $controller->$action($parameter[0], $this->getRequest());
                    break;
                case 2:
                    $controller->$action($parameter[0], $parameter[1], $this->getRequest());
                    break;
                case 3:
                    $controller->$action($parameter[0], $parameter[1], $parameter[2], $this->getRequest());
                    break;
                default:
                    $controller->$action($this->getRequest());
            }
        }

        else
        {
            echo 'Page not found!';
        }
    }
}
?>
