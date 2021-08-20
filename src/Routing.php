<?php

namespace Rtaners\Routing;


Abstract class Routing
{

    /**
     * Router Version
     */
    const VERSION = '1.0.0';


    /**
     * url parse method
     *
     * */
    public static function parseUrl()
    {
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $basename = basename($_SERVER['SCRIPT_NAME']);
        $requestUri = str_replace([$dirname, $basename], null, $_SERVER['REQUEST_URI']);
        return $requestUri;
    }

    /**
     *
     *
     * @param string $url
     * @param string|closure $callback
     * @param string $method
     * */
    public static function run($url, $callback, $method = 'get')
    {

        $method = explode('|', strtoupper($method));

        if (in_array($_SERVER['REQUEST_METHOD'], $method)):

            $patterns = [
                '{url}' => '([0-9a-zA-Z]+)',
                '{id}' => '([0-9]+)'
            ];


            /*
             * variables $s
             * */
            $requestUri = self::parseUrl();
            $controller = explode('@', $callback);
            $className = explode('/', $controller[0]);
            $className = end($className);
            $controllerFile = __DIR__ . '/controller/' . strtolower($controller[0]) . '.php';

            $url = str_replace(array_keys($patterns), array_values($patterns), $url);

            /*
             * eğer fonksiyon ise fonksiyonu çalıştır
             * */
            if (preg_match('@^' . $url . '$@', $requestUri, $parameters)):
                unset($parameters[0]);

                if (is_callable($callback)):
                    call_user_func($callback, $parameters);
                endif;
            endif;
            /*
             * */

            /*
             * controller dosyası varsa çağırıyorum
             * controller@method şeklinde gelen değerden parse ettiğim methodu çağırıyorum
             * */
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                call_user_func_array([new $className, $controller[1]], $parameters);
            } else {
                /*
                 * eğer controller yoksa hata fırlatmak için error yolluyorum
                 * index.php kısmında $error kontrolü sağlayıp basmalıyız hata varsa dönsün
                 * */
                $error = 'Controller Not Found';
                return $error;
            }

        endif;

    }

}