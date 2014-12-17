<?php

namespace BWC\Share\Composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Script
{
    static function vendor2web(Event $event) {
        $options = self::getOptions($event);
        if (isset($options['vendor2web']) && is_array($options['vendor2web'])) {
            print "Installing vendor assets to web\n";
            $installType = $options['symfony-assets-install']; // hard|symlink|relative
            $webDir = $options['web-dir-full'];
            $vendorDir = $options['vendor-dir-full'];
            if ($installType != 'hard' && $installType != 'symlink' && $installType != 'relative') {
                print "ERROR: Unknown install type $installType \n";
                return;
            }
            if (!is_dir($webDir)) {
                print "ERROR: No web dir $webDir \n";
                return;
            }
            if (!is_dir(($vendorDir))) {
                print "ERROR: No vendor dir $vendorDir \n";
                return;
            }
            if (!is_dir($webDir.DIRECTORY_SEPARATOR.'vendor')) {
                mkdir($webDir.DIRECTORY_SEPARATOR.'vendor');
            }
            self::_deleteOldWebVendors($webDir);
            print "Installing new vendors to web:\n";
            foreach ($options['vendor2web'] as $vendor=>$destination) {
                self::_installVendorToWeb($vendor, $destination, $webDir, $vendorDir, $installType);
            }
        }
    }

    static private function _deleteOldWebVendors($webDir) {
        $finder = new Finder();
        $finder->directories()->in($webDir.DIRECTORY_SEPARATOR.'vendor')->depth(1);
        if ($finder->count()) {
            print "Deleting old vendors in web:\n";
            foreach ($finder as $dir) {
                /** @var $name \SplFileInfo */
                $name = $dir->getRelativePathname();
                $fn = $webDir.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.$name;
                print "    $name\n";
                @unlink($fn);
                @rmdir($fn);
            }
        }
    }

    static private function _installVendorToWeb($vendor, $destination, $webDir, $vendorDir, $installType) {
        print "    $vendor => $destination\n";
        $source = $vendorDir.DIRECTORY_SEPARATOR.$vendor;
        $dir = $webDir.DIRECTORY_SEPARATOR.'vendor';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $target = $dir.DIRECTORY_SEPARATOR.$destination;
        if (!file_exists($source)) {
            print "WARNING: Source $vendor does not exist\n";
            return;
        }
        if (file_exists($target)) {
            @unlink($target);
            @rmdir($target);
        }
        // TODO target might contain some dir... should be created if not exist
        symlink($source, $target);
    }

    static protected function getOptions(Event $event) {
        $options = array_merge(
            array(
                'symfony-app-dir' => 'app',
                'symfony-web-dir' => 'web'
            ),
            $event->getComposer()->getPackage()->getExtra()
        );
        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: @$options['symfony-assets-install'];
        if (!$options['symfony-assets-install']) $options['symfony-assets-install'] = 'symlink';
        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');
        $options['vendor-dir'] = $event->getComposer()->getConfig()->get('vendor-dir');
        $composerJsonFile = realpath(\Composer\Factory::getComposerFile());
        $rootDir = dirname($composerJsonFile);
        $options['root-dir'] = $rootDir;
        $options['app-dir-full'] = $rootDir.DIRECTORY_SEPARATOR.$options['symfony-app-dir'];
        $options['web-dir-full'] = $rootDir.DIRECTORY_SEPARATOR.$options['symfony-web-dir'];

        if ('/' == substr($options['vendor-dir'], 0, 1) ||
            ':' == substr($options['vendor-dir'], 1, 1)
        ) {
            $options['vendor-dir-full'] = $options['vendor-dir'];
        } else {
            $options['vendor-dir-full'] = $rootDir.DIRECTORY_SEPARATOR.$options['vendor-dir'];
        }

        return $options;
    }

}
