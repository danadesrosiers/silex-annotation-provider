<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2018, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

declare(strict_types=1);

namespace DDesrosiers\SilexAnnotations;

/**
 * Class ControllerFinder searches file directories for potential Controller classes.
 *
 * @author Dana Desrosiers <dana.desrosiers@gmail.com>
 */
class ControllerFinder
{
    /** @var null|string */
    private $controllerDir;

    /** @var string[] */
    private $controllers;

    /**
     * ControllerFinder constructor.
     *
     * @param string|null $controllerDir
     * @param string[]    $controllers
     */
    public function __construct(string $controllerDir = null, array $controllers = [])
    {
        $this->controllerDir = $controllerDir;
        $this->controllers = $controllers;
    }

    /**
     * @return string[]
     */
    public function getControllerClasses(): array
    {
        $controllers = isset($this->controllerDir) ? $this->getClassesInDirectory($this->controllerDir) : [];
        return array_merge($controllers, $this->controllers);
    }

    /**
     * Recursively walk the file tree starting from $dir to find potential controller class files.
     * Returns array of fully qualified class names.
     * Namespace detection works with PSR-0 or PSR-4 autoloading.
     *
     * @param string   $dir
     * @param string   $namespace
     * @param string[] $files
     * @return string[]
     */
    private function getClassesInDirectory(string $dir, string $namespace = '', $files = []): array
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException("Controller directory: {$dir} does not exist.");
        }

        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (!in_array($entry, array('.', '..'))) {
                    $filePath = "$dir/$entry";
                    if (is_dir($filePath)) {
                        $subNamespace = $namespace ? $namespace."$entry\\" : '';
                        $files = $this->getClassesInDirectory($filePath, $subNamespace, $files);
                    } else {
                        if (!$namespace) {
                            $namespace = $this->parseNamespace($filePath);
                        }
                        $className = trim($namespace.pathinfo($entry)['filename']);
                        if (class_exists($className)) {
                            $files[] = $className;
                        }
                    }
                }
            }
            closedir($handle);
        }

        return $files;
    }

    /**
     * Parse the given file to find the namespace.
     *
     * @param $filePath
     * @return string
     */
    private function parseNamespace($filePath): string
    {
        preg_match('/namespace(.*);/', file_get_contents($filePath), $result);
        return isset($result[1]) ? $result[1] . "\\" : '';
    }
}