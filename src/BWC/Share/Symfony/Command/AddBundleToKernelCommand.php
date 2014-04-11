<?php

namespace BWC\Share\Symfony\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Symfony\Component\HttpKernel\KernelInterface;


abstract class AddBundleToKernelCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this
            ->setName('bwc:composer:bundles')
            ->setDescription('Adds bundles from composer to kernel')
            ->addArgument('bundles', InputArgument::IS_ARRAY)
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return string[];
     */
    protected function getBundlesArgument(InputInterface $input) {
        $arrBundles = $input->getArgument('bundles');
        return $arrBundles;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $arrBundles = $this->getBundlesArgument($input);
        $kernel = $this->getKernel();
        $alreadyAdded = array();
        $ok = $this->updateBundles($arrBundles, $kernel, $output, $alreadyAdded);
        $this->generateSummary($output, $ok, $arrBundles, $alreadyAdded);
    }


    /**
     * @return KernelInterface
     */
    protected function getKernel() {
        return $this->getContainer()->get('kernel');
    }


    /**
     * @param string[] $arrBundles  array of full class names, all with namespace, of the bundles to be added to the kernel
     * @param KernelInterface $kernel
     * @param OutputInterface $output
     * @param string[] $alreadyAdded
     * @return bool
     */
    protected function updateBundles(array $arrBundles, KernelInterface $kernel, OutputInterface $output, &$alreadyAdded) {
        $manipulator = new KernelManipulator($kernel);
        $alreadyAdded = array();
        $result = true;
        foreach ($arrBundles as $bundle) {
            try {
                $ok = $manipulator->addBundle($bundle);
                $result = $result && $ok;
            } catch (\RuntimeException $ex) {
                $alreadyAdded[] = $bundle;
            }
        }
        return $result;
    }


    /**
     * @param OutputInterface $output
     * @param bool $ok
     * @param string[] $arrBundles
     * @param string[] $alreadyAdded
     */
    protected function generateSummary(OutputInterface $output, $ok, array $arrBundles, array $alreadyAdded) {
        if (!$ok) {
            $output->writeln('FAIL - Was not able to add all bundles automatically');
            $output->writeln('Ensure following bundles are added to AppKernel::registerBundles()');
            foreach ($arrBundles as $bundle) {
                $output->writeln("    $bundle");
            }
        } else {
            $count = count($arrBundles);
            $output->writeln("OK - Kernel updated with $count bundles defined in composer");
        }
        if (!empty($alreadyAdded)) {
            $output->writeln('Following bundles were already added to the kernel:');
            foreach ($alreadyAdded as $bundle) {
                $output->writeln("    $bundle");
            }
        }
    }
}