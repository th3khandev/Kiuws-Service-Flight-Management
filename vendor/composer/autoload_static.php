<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite45cd8bc9bbd4940dcc56a65834dd1e6
{
    public static $prefixLengthsPsr4 = array (
        'K' => 
        array (
            'Kiuws_Service_Flight_Management\\Includes\\' => 41,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Kiuws_Service_Flight_Management\\Includes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite45cd8bc9bbd4940dcc56a65834dd1e6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite45cd8bc9bbd4940dcc56a65834dd1e6::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite45cd8bc9bbd4940dcc56a65834dd1e6::$classMap;

        }, null, ClassLoader::class);
    }
}
