<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd8fc7f23428718a377536f33410e578f
{
    public static $prefixLengthsPsr4 = array (
        'G' => 
        array (
            'GithubUpdater\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'GithubUpdater\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd8fc7f23428718a377536f33410e578f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd8fc7f23428718a377536f33410e578f::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitd8fc7f23428718a377536f33410e578f::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
