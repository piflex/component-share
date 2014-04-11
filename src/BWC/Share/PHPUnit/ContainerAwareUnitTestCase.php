<?php

namespace BWC\Share\PHPUnit;

use Symfony\Component\HttpKernel\KernelInterface;

class ContainerAwareUnitTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var KernelInterface */
    protected static $kernel;
    protected static $container;

    public static function setUpBeforeClass() {
        //require_once __DIR__ . '/../../../../../../app/AppKernel.php';
        $path = '/../app/AppKernel.php';
        for ($i=0; $i<15; $i++) {
            $file = __DIR__ .$path;
            if (is_file($file)) {
                require_once $file;
                break;
            }
            $path = '/..'.$path;
        }
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();
        self::$container = self::$kernel->getContainer();
    }

    public function get($serviceId) {
        return self::$kernel->getContainer()->get($serviceId);
    }

    public function getParameter($name) {
        return self::$kernel->getContainer()->getParameter($name);
    }

}
