<?php
namespace WP2_Test\Core;

use Brain\Monkey\Functions;

/**
 * I18n helper for translation stubs and assertions.
 */
class I18n
{
    /**
     * Stub all translation functions for tests.
     */
    public static function stub_all()
    {
        Functions\when('__')->returnArg(0);
        Functions\when('_e')->returnArg(0);
        Functions\when('_n')->returnArg(0);
        Functions\when('_x')->returnArg(0);
        Functions\when('_ex')->returnArg(0);
        Functions\when('_nx')->returnArg(0);
    }

    /**
     * Assert that a translation lookup was performed.
     * @param string $domain
     * @param string $text
     */
    public static function expect_lookup(string $domain, string $text)
    {
        Functions\expect('__')
            ->once()
            ->with($text, $domain);
    }
}
