
<?php
/**
 * Запись стека вызовов ранней загрузки переводов.
 * После диагностики удалить этот файл.
 */

add_action( 'doing_it_wrong_run',
    function ( $function_name, $message, $version ) {

        if ('_load_textdomain_just_in_time' !== $function_name && strpos( $message, 'Translation loading' ) === false) return;

        $file = WP_CONTENT_DIR . '/translation-trace.log';
        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 40 );

        $output  = "\n========================================\n";
        $output .= 'Date: ' . date( 'Y-m-d H:i:s' ) . "\n";
        $output .= 'Function: ' . $function_name . "\n";
        $output .= 'Message: ' . $message . "\n";
        $output .= "Stack trace:\n";

        foreach ( $trace as $i => $frame ) {
            $called = '';

            if ( isset( $frame['class'] ) ) $called .= $frame['class'] . ($frame['type'] ?? '::');

            $called .= $frame['function'] ?? '';

            $source = $frame['file'] ?? '[internal]';
            $line   = $frame['line'] ?? 0;

            $output .= sprintf( "#%d %s:%d — %s\n", $i, $source, $line, $called );
        }

        $output .= "========================================\n";

        file_put_contents( $file, $output, FILE_APPEND | LOCK_EX );
    },
10,
3
);
