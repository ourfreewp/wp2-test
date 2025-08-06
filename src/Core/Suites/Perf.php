<?php
namespace WP2_Test\Core;

use PHPUnit\Framework\AssertionFailedError;

/**
 * Performance assertion utility.
 */
class Perf
{
    /**
     * Assert that a callback runs under the given performance budget.
     *
     * @param callable $callback
     * @param array $thresholds ['time_ms' => int, 'memory_mb' => int]
     * @throws AssertionFailedError
     */
    public static function assert_under_performance_budget(callable $callback, array $thresholds)
    {
        $start_time = microtime(true);
        $start_mem = memory_get_usage();
        $callback();
        $end_time = microtime(true);
        $end_mem = memory_get_usage();
        $elapsed_ms = ($end_time - $start_time) * 1000;
        $used_mb = ($end_mem - $start_mem) / 1048576;
        if (isset($thresholds['time_ms']) && $elapsed_ms > $thresholds['time_ms']) {
            throw new AssertionFailedError("Execution time {$elapsed_ms}ms exceeds budget of {$thresholds['time_ms']}ms");
        }
        if (isset($thresholds['memory_mb']) && $used_mb > $thresholds['memory_mb']) {
            throw new AssertionFailedError("Memory usage {$used_mb}MB exceeds budget of {$thresholds['memory_mb']}MB");
        }
        return true;
    }
}
