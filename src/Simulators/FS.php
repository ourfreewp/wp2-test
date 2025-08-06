<?php
namespace WP2_Test\Simulators;

use org\bovigo\vfs\vfsStream;

/**
 * Virtual filesystem simulator using vfsStream.
 */
class FS
{
    protected static $root;

    public static function boot()
    {
        self::$root = vfsStream::setup('root');
    }

    public static function create_file($path, $content)
    {
        $file = vfsStream::newFile($path)->at(self::$root);
        $file->setContent($content);
        return $file;
    }

    public static function get_vfs_path($path)
    {
        return vfsStream::url('root/' . ltrim($path, '/'));
    }
}
