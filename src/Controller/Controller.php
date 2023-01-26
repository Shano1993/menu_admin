<?php

namespace App\Controller;

use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

abstract class Controller
{
    abstract public function index();

    private static ?Environment $twigInstance = null;
    private static ?FilesystemLoader $twigLoader = null;

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(...$params): void
    {
        try {
            echo self::getTwig()->render(...$params);
        }
        catch (LoaderError $e) {
            echo self::getTwig()->render('error/404.html.twig');
        }
        catch (RuntimeError|SyntaxError $e) {
            echo self::getTwig()->render('error/500.html.twig');
        }
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        if (null === self::$twigInstance) {
            if (null === self::$twigLoader) {
                self::$twigLoader = new FilesystemLoader('../templates');
            }
            self::$twigInstance = new Environment(self::$twigLoader, [
                'debug' => true,
                'strict_variables' => true,
                // 'cache' => '../var/cache',
            ]);

            self::$twigInstance->addExtension(new DebugExtension());
        }

        return self::$twigInstance;
    }

    /**
     * @return FilesystemLoader|null
     */
    public function getTwigLoader(): ?FilesystemLoader
    {
        return self::$twigLoader;
    }

    /**
     * @return bool
     */
    public static function isFormSubmitted(): bool
    {
        return isset($_POST['save']);
    }

    /**
     * @param string $randomName
     * @return string
     */
    public static function getRandomName(string $randomName): string
    {
        $infos = pathinfo($randomName);
        try {
            $bytes = random_bytes(20);
        }
        catch (Exception $exception) {
            $bytes = openssl_random_pseudo_bytes(20);
        }
        return bin2hex($bytes) . '.' . $infos['extension'];
    }

    /**
     * @param $tmpname
     * @return bool
     */
    public static function checkImageMime($tmpname): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mtype = finfo_file($finfo, $tmpname);
        if (strpos($mtype, 'image/') === 0) {
            return true;
        }
        finfo_close($finfo);
        return false;
    }
}