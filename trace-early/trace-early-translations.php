
<?php
// /**
//  * Запись стека вызовов ранней загрузки переводов.
//  * После диагностики удалить этот файл.
//  */

// add_action( 'doing_it_wrong_run',
//     function ( $function_name, $message, $version ) {

//         if ('_load_textdomain_just_in_time' !== $function_name && strpos( $message, 'Translation loading' ) === false) return;

//         $file = WP_CONTENT_DIR . '/translation-trace.log';
//         $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 40 );

//         $output  = "\n========================================\n";
//         $output .= 'Date: ' . date( 'Y-m-d H:i:s' ) . "\n";
//         $output .= 'Function: ' . $function_name . "\n";
//         $output .= 'Message: ' . $message . "\n";
//         $output .= "Stack trace:\n";

//         foreach ( $trace as $i => $frame ) {
//             $called = '';

//             if ( isset( $frame['class'] ) ) $called .= $frame['class'] . ($frame['type'] ?? '::');

//             $called .= $frame['function'] ?? '';

//             $source = $frame['file'] ?? '[internal]';
//             $line   = $frame['line'] ?? 0;

//             $output .= sprintf( "#%d %s:%d — %s\n", $i, $source, $line, $called );
//         }

//         $output .= "========================================\n";

//         file_put_contents( $file, $output, FILE_APPEND | LOCK_EX );
//     },
// 10,
// 3
// );










/**
 * Temporary WordPress error logger.
 * Log file: wp-content/site-debug.log
 */

if ( ! defined( 'ABSPATH' ) ) {exit;}

function site_trace_write( $type, $message, $file = '', $line = 0, $trace = null ) {
    $log_file = WP_CONTENT_DIR . '/site-debug.log';

    $entry  = "\n========================================\n";
    $entry .= 'Time: ' . date( 'Y-m-d H:i:s' ) . "\n";
    $entry .= 'Type: ' . $type . "\n";
    $entry .= 'Message: ' . $message . "\n";

    if ( $file !== '' ) {
        $entry .= 'File: ' . $file . "\n";
        $entry .= 'Line: ' . $line . "\n";
    }

    if ( is_array( $trace ) ) {
        $entry .= "Stack trace:\n";

        foreach ( $trace as $i => $frame ) {
            $called = '';

            if ( isset( $frame['class'] ) )
                $called .= $frame['class'] . ( $frame['type'] ?? '::' );

            $called .= $frame['function'] ?? '';

            $source = $frame['file'] ?? '[internal]';
            $number = $frame['line'] ?? 0;

            $entry .= sprintf( "#%d %s:%d — %s\n", $i, $source, $number, $called );
        }
    }

    $entry .= "========================================\n";

    error_log( $entry, 3, $log_file );
}

/**
 * PHP warnings, notices, deprecations and user-level errors.
 */
set_error_handler(
    function ( $severity, $message, $file, $line ) {
        $reportable = array( E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED, E_RECOVERABLE_ERROR, E_USER_ERROR, );

        if ( ! in_array( $severity, $reportable, true ) )
            return false;

        // Respect PHP's current error_reporting() configuration.
        if ( ! ( error_reporting() & $severity ) )
            return false;

        site_trace_write( 'PHP ' . $severity, $message, $file, $line, debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 30 ) );

        // Continue PHP's normal error handling.
        return false;
    }
);

/**
 * Uncaught exceptions.
 */
set_exception_handler(
    function ( $exception ) {
        site_trace_write( 'UNCAUGHT EXCEPTION', get_class( $exception ) . ': ' . $exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTrace() );
    }
);

/**
 * Fatal errors that reach shutdown.
 */
register_shutdown_function(
    function () {
        $error = error_get_last();

        if ( ! $error )
            return;

        $fatal_types = array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, );

        if ( ! in_array( $error['type'], $fatal_types, true ) )
            return;

        site_trace_write( 'FATAL PHP ERROR', $error['message'], $error['file'], $error['line'] );
    }
);

/**
 * WordPress doing-it-wrong warnings.
 */
add_action( 'doing_it_wrong_run',
    function ( $function_name, $message, $version ) {
        site_trace_write( 'WORDPRESS DOING IT WRONG: ' . $function_name, $message . ' (Since: ' . $version . ')', '', 0, debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 30 ) );
    },10,3);
