<?php

// http://php.net/manual/en/function.debug-backtrace.php

namespace Modules\Helpers\Reporting;

use Modules\Singleton;
use View\View;

class ErrorCatcher
{
    use Singleton;

    public static function start( )
    {
        ini_set( 'display_errors', 1 );
        ini_set( 'track_errors', 1 );
        error_reporting(E_ALL);
        $closure = function (...$argv) {
            ErrorCatcher::generateErrorLog($argv);
            View::contents('error','500error');
        };
        set_error_handler($closure);
        set_exception_handler($closure);
    }

    public static function generateErrorLog($argv = array()){
        $self = static::getInstance();
        $self->generateLog($argv);
    }

    public function generateLog($argv = array())
    {
        $file = fopen(ERROR_LOG , "a");

        ob_start( );
        print PHP_EOL. date( 'D, d M Y H:i:s' , time());
        print 'Printout of Function Stack: ' . PHP_EOL;
        print $this->generateCallTrace( ) . PHP_EOL;
        if (count( $argv ) >=4 ){
        echo 'Message: ' . $argv[1] . PHP_EOL;
        echo 'line: ' . $argv[2] .'('. $argv[3] .')';
        } else var_dump( $argv );
        echo PHP_EOL . PHP_EOL;
        $output = ob_get_contents( );
        ob_end_clean( );
        // Write the contents back to the file
        fwrite( $file, $output );
        fclose( $file );
    }
    
    private function generateCallTrace()
    {

        $e = new \Exception( );
        ob_start( );
        $trace = explode( "\n", $e->getTraceAsString() );
        // reverse array to make steps line up chronologically
        $trace = array_reverse( $trace );
        array_shift( $trace ); // remove {main}
        array_pop( $trace ); // remove call to this method
        array_pop( $trace ); // remove call to this method
        $length = count( $trace );
        $result = array( );

        for ( $i = 0; $i < $length; $i++ ) {
            $result[] = ($i + 1) . ') ' . substr(substr( $trace[$i], strpos( $trace[$i], ' ' ) ), 35) . PHP_EOL;
            print PHP_EOL; // replace '#someNum' with '$i)', set the right ordering
        }

        print "\t" . implode( "\n\t", $result );

        $output = ob_get_contents( );
        ob_end_clean( );
        return $output;
    }
    
}


