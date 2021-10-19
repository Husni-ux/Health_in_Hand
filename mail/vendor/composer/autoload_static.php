<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitde56a2572b674cb5fe5050d104df72ad
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitde56a2572b674cb5fe5050d104df72ad::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitde56a2572b674cb5fe5050d104df72ad::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitde56a2572b674cb5fe5050d104df72ad::$classMap;

        }, null, ClassLoader::class);
    }
}
