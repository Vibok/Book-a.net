<?php

use Base\Core\Resource,
    Base\Core\Error,
    Base\Core\Redirection,
    Base\Core\ACL,
    Base\Library\Text,
    Base\Library\Advice,
    Base\Library\Lang;

require_once 'config.php';
require_once 'core/common.php';

/*
 * Pagina de en mantenimiento
 */
if (CONF_MAINTENANCE === true) {
        $bodyClass = 'screen';
        include 'view/prologue.html.php';
        ?>
        <div id="its">
        <center>
        <h1>Plataforma en mantenimiento</h1>
        </div>
        <?php
        include 'view/epilogue.html.php';
        exit;
}


// Autoloader
spl_autoload_register(

    function ($cls) {

        $file = CONF_PATH . implode(DIRECTORY_SEPARATOR, explode('\\', strtolower(substr($cls, 5)))) . '.php';
        $file = realpath($file);

        if ($file === false) {

            // Try in library
            $file = CONF_PATH . 'library' . DIRECTORY_SEPARATOR . strtolower($cls) . '.php';
        }

        if ($file !== false) {
            include $file;
        }

    }

);

// Error handler
set_error_handler (

    function ($errno, $errstr, $errfile, $errline, $errcontext) {
        // @todo Insert error into buffer
//            echo "Error:  {$errno}, {$errstr}, {$errfile}, {$errline}, {$errcontext}<br />";
        //throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

);

/**
 * Sesión.
 */
session_name('booka');
session_start();

// set Lang
Lang::set();

// cambiamos el locale
\setlocale(\LC_TIME, Lang::locale());

// Get URI without query string
$uri = strtok($_SERVER['REQUEST_URI'], '?');

// Get requested segments
$segments = preg_split('!\s*/+\s*!', $uri, -1, \PREG_SPLIT_NO_EMPTY);

// Normalize URI
$uri = '/' . implode('/', $segments);

try {

    // Check permissions on requested URI
    if (!ACL::check($uri)) {
        Advice::Info(Text::get('user-login-required-access'));
        
        // si la pagina que piden existe
        throw new Redirection("/user/login/?return=".rawurlencode($uri));

        // si no existe error 404

    }

    // Get controller name
    if (!empty($segments) && class_exists("Base\\Controller\\{$segments[0]}")) {
        // Take first segment as controller
        $controller = array_shift($segments);
    } else {
        $controller = 'index';
    }

    // Continue
    try {

        $class = new ReflectionClass("Base\\Controller\\{$controller}");

        if (!empty($segments) && $class->hasMethod($segments[0])) {
            $method = array_shift($segments);
        } else {
            // Try default method
            $method = 'index';
        }

        // ReflectionMethod
        $method = $class->getMethod($method);

        // Number of params defined in method
        $numParams = $method->getNumberOfParameters();
        // Number of required params
        $reqParams = $method->getNumberOfRequiredParameters();
        // Given params
        $gvnParams = count($segments);

        if ($gvnParams >= $reqParams && (!($gvnParams > $numParams && $numParams <= $reqParams))) {

            // Try to instantiate
            $instance = $class->newInstance();

            // Start output buffer
            ob_start();

            // Invoke method
            $result = $method->invokeArgs($instance, $segments);

            if ($result === null) {
                // Get buffer contents
                $result = ob_get_contents();
            }

            ob_end_clean();

            if ($result instanceof Resource\MIME) {
                header("Content-type: {$result->getMIME()}");
            }

            echo $result;

            // Farewell
            die;

        }

    } catch (\ReflectionException $e) {}

        echo $e->getMessage();
        echo '<hr />';
        echo \trace($e);
        die;
    throw new Error(Error::NOT_FOUND);

} catch (Redirection $redirection) {
    $url = $redirection->getURL();
    $code = $redirection->getCode();
    header("Location: {$url}");

} catch (Error $error) {

    include "view/error.html.php";

} catch (Exception $exception) {

    // Default error (500)
    include "view/error.html.php";
}