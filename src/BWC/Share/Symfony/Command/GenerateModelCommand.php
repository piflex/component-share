<?php

namespace BWC\Share\Symfony\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;


class GenerateModelCommand extends ContainerAwareCommand
{
    protected function configure() {
        $this
            ->setName('bwc:share:generate:model')
            ->setDescription('Generates single model classes')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        $bundle = $this->pickBundle($dialog, $output);

        $modelName = $this->getModelName($dialog, $output, $bundle);

        $fields = $this->getFields($dialog, $output);

        $this->confirm($dialog, $output, $bundle, $modelName, $fields);

        $this->generate($output, $bundle, $modelName, $fields);
    }


    protected function generate(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln('');
        $this->generateModelInterface($output, $bundle, $modelName, $fields);
        $this->generateModel($output, $bundle, $modelName, $fields);
        $this->generateEntity($output, $bundle, $modelName, $fields);
        $this->generateManagerInterface($output, $bundle, $modelName, $fields);
        $this->generateManager($output, $bundle, $modelName, $fields);
        $this->generateOrmManager($output, $bundle, $modelName, $fields);
        $this->generateDoctrineMappings($output, $bundle, $modelName, $fields);
    }


    protected function generateDoctrineMappings(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Resources/config/doctrine/%s.orm.xml", $modelName));

        $txtFields = '';
        foreach ($fields as $name=>$type) {
            if ($name == 'id') {
                continue;
            }

            $n = preg_replace_callback(
                "|(.+)([A-Z])|",
                function($matches) {
                    return $matches[1].'_'.strtolower($matches[2]);
                },
                $name
            );

            $extra = '';

            if ($type == 'string') {
                $extra = 'length="200" ';
            } else if ($type == '\DateTime') {
                $type = 'datetime';
            } else if ($type == 'int') {
                $type = 'integer';
            } else if ($type == 'bool') {
                $type = 'boolean';
            }

            $txtFields .= <<<EOT
        <field name="{$name}" column="{$n}" type="{$type}" nullable="false" {$extra}/>


EOT;
        }

        $mn = preg_replace_callback(
            "|(.+)([A-Z])|",
            function($matches) {
                return $matches[1].'_'.strtolower($matches[2]);
            },
            $modelName
        );

        $ns = $bundle->getNamespace();

        $txt = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                    http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity  name="{$ns}\Entity\\{$modelName}" table="{$mn}">

        <!-- unique-constraints>
            <unique-constraint name="UDX_entity_first" columns="foo,bar"/>
            <unique-constraint name="UDX_entity_second" columns="bar,jazz"/>
        </unique-constraints -->

        <id name="id" column="id" type="integer">
            <generator strategy="AUTO" />
        </id>

        <!-- use this if id is other entity together with one-to-one association -->
        <!-- id name="account" column="account_id" association-key="true"/ -->

$txtFields

        <!-- many-to-one target-entity="Other" field="other" inversed-by?="entities">
            <join-column  name="other_id" referenced-column-name="id" nullable="false"/>
        </many-to-one -->

        <!-- many-to-many target-entity="Other" field="others" inversed-by="entities">
            <join-table name="other2entity">
                <join-columns>
                    <join-column name="entity_id" referenced-column-name="id"/>
                </join-columns>
                <inverse-join-columns>
                    <join-column name="other_id" referenced-column-name="id"/>
                </inverse-join-columns>
            </join-table>
        </many-to-many -->

        <!-- one-to-many target-entity="Other" mapped-by="entity" field="others" / -->

        <!-- one-to-one target-entity="Other" field="other">
            <join-column  name="other_id" referenced-column-name="id" nullable="false"/>
        </one-to-one -->

    </entity>

</doctrine-mapping>
EOT;

        $dir = sprintf("%s/Resources/config/doctrine", $bundle->getPath());
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Resources/config/doctrine/%s.orm.xml", $bundle->getPath(), $modelName);
        file_put_contents($fn, $txt);
    }


    protected function generateOrmManager(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Service/Model/%s/Orm/%sManager.php", $modelName, $modelName));

        $ns = $bundle->getNamespace();
        $txt = <<<EOT
<?php

namespace $ns\\Service\\Model\\{$modelName}\\Orm;

use Doctrine\ORM\EntityManager;
use $ns\\Model\\{$modelName}Interface;


class {$modelName}Manager extends \\$ns\\Service\\Model\\{$modelName}\\{$modelName}Manager
{
    /** @var EntityManager  */
    protected \$entityManager;

    /** @var string  */
    protected \$class;

    /** @var \Doctrine\Common\Persistence\ObjectRepository  */
    protected \$repository;



    /**
     * @param EntityManager \$entityManager
     * @param string|null \$class
     */
    public function __construct(EntityManager \$entityManager, \$class = null)
    {
        parent::__construct();

        if (!\$class) {
            \$class = '{$ns}\\Entity\\{$modelName}';
        }

        \$this->entityManager = \$entityManager;
        \$this->repository = \$entityManager->getRepository(\$class);

        \$metadata = \$entityManager->getClassMetadata(\$class);
        \$this->class = \$metadata->getName();
    }


    /**
     * @return string
     */
    public function getClass()
    {
        return \$this->class;
    }

    /**
     * @param {$modelName}Interface \$object
     * @param bool \$andFlush
     */
    public function delete({$modelName}Interface \$object, \$andFlush = true)
    {
        \$this->entityManager->remove(\$object);
        if (\$andFlush) {
            \$this->entityManager->flush();
        }
    }

    /**
     * @param array \$criteria
     * @return {$modelName}Interface|null
     */
    public function getBy(array \$criteria)
    {
        return \$this->repository->findOneBy(\$criteria);
    }

    /**
     * @param array \$criteria
     * @param array|null \$orderBy
     * @param int|null \$limit
     * @param int|null \$offset
     * @return {$modelName}Interface[]
     */
    public function find(array \$criteria, array \$orderBy = null, \$limit = null, \$offset = null)
    {
        return \$this->repository->findBy(\$criteria, \$orderBy, \$limit, \$offset);
    }

    /**
     * @param {$modelName}Interface \$object
     * @param bool \$andFlush
     * @return void
     */
    public function update({$modelName}Interface \$object, \$andFlush = true)
    {
        \$this->entityManager->persist(\$object);
        if (\$andFlush) {
            \$this->entityManager->flush();
        }
    }

}

EOT;

        $dir = sprintf("%s/Service/Model/%s/Orm", $bundle->getPath(), $modelName);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Service/Model/%s/Orm/%sManager.php", $bundle->getPath(), $modelName, $modelName);
        file_put_contents($fn, $txt);
    }


    protected function generateManager(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Service/Model/%s/%sManager.php", $modelName, $modelName));

        $ns = $bundle->getNamespace();
        $txt = <<<EOT
<?php

namespace $ns\\Service\\Model\\{$modelName};

use $ns\\Model\\{$modelName}Interface;


abstract class {$modelName}Manager implements {$modelName}ManagerInterface
{

    /**
     *
     */
    public function __construct()
    {
    }


    /**
     * @return string
     */
    public abstract function getClass();

    /**
     * @param {$modelName}Interface \$object
     */
    public abstract function delete({$modelName}Interface \$object);

    /**
     * @param array \$criteria
     * @return {$modelName}Interface|null
     */
    public abstract function getBy(array \$criteria);

    /**
     * @param array \$criteria
     * @param array|null \$orderBy
     * @param int|null \$limit
     * @param int|null \$offset
     * @return {$modelName}Interface[]
     */
    public abstract function find(array \$criteria, array \$orderBy = null, \$limit = null, \$offset = null);

    /**
     * @param {$modelName}Interface \$object
     * @return void
     */
    public abstract function update({$modelName}Interface \$object);



    /**
     * @return {$modelName}Interface
     */
    public function create()
    {
        \$class = \$this->getClass();
        \$result = new \$class;

        return \$result;
    }

    /**
     * @param int \$id
     * @return {$modelName}Interface|null
     */
    public function getById(\$id)
    {
        return \$this->getBy(array('id'=>\$id));
    }

}

EOT;

        $dir = sprintf("%s/Service/Model/%s", $bundle->getPath(), $modelName);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Service/Model/%s/%sManager.php", $bundle->getPath(), $modelName, $modelName);
        file_put_contents($fn, $txt);
    }


    protected function generateManagerInterface(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Service/Model/%s/%sManagerInterface.php", $modelName, $modelName));

        $ns = $bundle->getNamespace();
        $txt = <<<EOT
<?php

namespace $ns\\Service\\Model\\{$modelName};

use $ns\\Model\\{$modelName}Interface;


interface {$modelName}ManagerInterface
{
    /**
     * @return {$modelName}Interface
     */
    public function create();

    /**
     * @return string
     */
    public function getClass();

    /**
     * @param {$modelName}Interface \$object
     */
    public function delete({$modelName}Interface \$object);

    /**
     * @param array \$criteria
     * @return {$modelName}Interface|null
     */
    public function getBy(array \$criteria);

    /**
     * @param int \$id
     * @return {$modelName}Interface|null
     */
    public function getById(\$id);

    /**
     * @param array \$criteria
     * @param array|null \$orderBy
     * @param int|null \$limit
     * @param int|null \$offset
     * @return {$modelName}Interface[]
     */
    public function find(array \$criteria, array \$orderBy = null, \$limit = null, \$offset = null);


    /**
     * @param {$modelName}Interface \$object
     * @return void
     */
    public function update({$modelName}Interface \$object);
}

EOT;

        $dir = sprintf("%s/Service/Model/%s", $bundle->getPath(), $modelName);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Service/Model/%s/%sManagerInterface.php", $bundle->getPath(), $modelName, $modelName);
        file_put_contents($fn, $txt);
    }


    protected function generateModelInterface(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Model/%sInterface.php", $modelName));

        $txtMethods = '';
        foreach ($fields as $name=>$type) {

            $txtMethods .= $this->generateFieldGetterHeader($name, $type, ';');
            $txtMethods .= "    \n";

            if ($name == 'id') {
                continue;
            }

            $txtMethods .= $this->generateFieldSetterHeader($name, $type, $modelName, ';');
            $txtMethods .= "    \n";
        }

        $ns = $bundle->getNamespace();

        $txt = <<<EOT
<?php

namespace $ns\\Model;

interface {$modelName}Interface
{
$txtMethods
}
EOT;

        $dir = sprintf("%s/Model", $bundle->getPath());
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Model/%sInterface.php", $bundle->getPath(), $modelName);
        file_put_contents($fn, $txt);
    }


    protected function generateEntity(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Entity/%s.php", $modelName));

        $ns = $bundle->getNamespace();

        $txt = <<<EOT
<?php

namespace $ns\\Entity;

class $modelName extends \\{$ns}\\Model\\{$modelName}
{

}
EOT;

        $dir = sprintf("%s/Entity", $bundle->getPath());
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Entity/%s.php", $bundle->getPath(), $modelName);
        file_put_contents($fn, $txt);
    }

    protected function generateModel(OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln(sprintf("Generating Model/%s.php", $modelName));

        $txtFields = '';
        foreach ($fields as $name=>$type) {
            $txtFields .= "    /** @var $type */\n";
            $txtFields .= "    protected \${$name};\n";
            $txtFields .= "    \n";
        }

        $txtMethods = '';
        foreach ($fields as $name=>$type) {

            $txtMethods .= $this->generateFieldGetterHeader($name, $type);
            $txtMethods .= $this->generateFieldGetterBody($name);
            $txtMethods .= "    \n";

            if ($name == 'id') {
                continue;
            }

            $txtMethods .= $this->generateFieldSetterHeader($name, $type, $modelName);
            $txtMethods .= $this->generateFieldSetterBody($name);
            $txtMethods .= "    \n";
        }

        $ns = $bundle->getNamespace();

        $txt = <<<EOT
<?php

namespace $ns\\Model;

abstract class $modelName implements {$modelName}Interface
{
$txtFields


$txtMethods
}
EOT;

        $dir = sprintf("%s/Model", $bundle->getPath());
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fn = sprintf("%s/Model/%s.php", $bundle->getPath(), $modelName);
        file_put_contents($fn, $txt);
    }


    /**
     * @param $name
     * @param $type
     * @param $modelName
     * @param $extra
     * @return string
     */
    protected function generateFieldSetterHeader($name, $type, $modelName, $extra = '')
    {
        if (in_array($type, array('string', 'int', 'float', 'bool'))) {
            $s = '';
        } else {
            $s = $type.' ';
        }
        $n = ucfirst($name);
        $txtFields = '';
        $txtFields .= "    /**\n";
        $txtFields .= "     * @param $type \$value\n";
        $txtFields .= "     * @return {$modelName}|\$this\n";
        $txtFields .= "     */\n";
        $txtFields .= "    public function set{$n}({$s}\$value){$extra}\n";

        return $txtFields;
    }

    /**
     * @param $name
     * @return string
     */
    protected function generateFieldSetterBody($name)
    {
        $txtFields = '';
        $txtFields .= "    {\n";
        $txtFields .= "        \$this->{$name} = \$value;\n";
        $txtFields .= "        \n";
        $txtFields .= "        return \$this;\n";
        $txtFields .= "    }\n";

        return $txtFields;
    }

    /**
     * @param $name
     * @param $type
     * @param string $extra
     * @return string
     */
    protected function generateFieldGetterHeader($name, $type, $extra = '')
    {
        $n = ucfirst($name);
        $txtFields = '';
        $txtFields .= "    /**\n";
        $txtFields .= "     * @return $type\n";
        $txtFields .= "     */\n";
        $txtFields .= "    public function get{$n}(){$extra}\n";

        return $txtFields;
    }

    /**
     * @param $name
     * @return string
     */
    protected function generateFieldGetterBody($name)
    {
        $txtFields = '';
        $txtFields .= "    {\n";
        $txtFields .= "        return \$this->{$name};\n";
        $txtFields .= "    }\n";

        return $txtFields;
    }

    protected function confirm(DialogHelper $dialog, OutputInterface $output, BundleInterface $bundle, $modelName, array $fields)
    {
        $output->writeln('');
        $output->writeln($bundle->getName());
        $output->writeln($modelName);
        foreach ($fields as $name=>$type) {
            $output->writeln('    '.$name.' : '.$type);
        }

        $output->writeln('');
        $ok = $dialog->askConfirmation($output, "Confirm generation? [y] : ");
        if (!$ok) {
            throw new \RuntimeException('Aborted');
        }
    }


    protected function getFields(DialogHelper $dialog, OutputInterface $output)
    {
        $result = array();

        $output->writeln('');
        $output->writeln("Enter empty field name to stop.");

        while (true) {
            $field = $dialog->askAndValidate($output,
                "Field name? : ",
                function($a) {
                    if (!preg_match("/^(|[a-z][a-zA-Z0-9]*)$/", $a)) {
                        throw new \InvalidArgumentException('Invalid field name');
                    }
                    return $a;
                }
            );

            if (!$field) {
                break;
            }

            $type = $dialog->ask($output,
                "Type? [string] : ",
                "string"
            );

            $output->writeln('');

            $result[$field] = $type;
        }

        if (empty($result)) {
            throw new \RuntimeException('Must have at least one field');
        }

        return $result;
    }

    protected function getModelName(DialogHelper $dialog, OutputInterface $output, BundleInterface $bundle)
    {
        $modelName = $dialog->askAndValidate($output,
            "What's the model name? : ",
            function($a) {
                if (!preg_match("|^[A-Z][a-zA-Z0-9]*$|", $a)) {
                    throw new \InvalidArgumentException('Invalid model name');
                }
                return $a;
            }
        );

        $files = array(
            sprintf("%s/Model/%s.php", $bundle->getPath(), $modelName),
            sprintf("%s/Entity/%s.php", $bundle->getPath(), $modelName),
            sprintf("%s/Resources/config/doctrine/%s.orm.xml", $bundle->getPath(), $modelName),
            sprintf("%s/Service/Model/%s/%sManagerInterface.php", $bundle->getPath(), $modelName, $modelName),
            sprintf("%s/Service/Model/%s/%sManager.php", $bundle->getPath(), $modelName, $modelName),
        );
        $exist = array();
        foreach ($files as $fn) {
            if (is_file(($fn))) {
                $exist[] = substr($fn, strlen($bundle->getPath())+1);
            }
        }
        if ($exist) {
            throw new \RuntimeException(sprintf("Can not overwrite existing files:\n    %s", implode("\n    ", $exist)));
        }

        return $modelName;
    }

    /**
     * @param DialogHelper $dialog
     * @param OutputInterface $output
     * @return BundleInterface
     */
    protected function pickBundle(DialogHelper $dialog, OutputInterface $output)
    {
        $output->writeln("");
        $arr = $this->getContainer()->get('kernel')->getBundles();

        /** @var BundleInterface[] $arrBundles */
        $arrBundles = array();
        foreach ($arr as $bundle) {
            if (strpos($bundle->getNamespace(), 'Symfony') === 0 ||
                strpos($bundle->getNamespace(), 'Sensio') === 0 ||
                strpos($bundle->getNamespace(), 'Doctrine') === 0
            ) {
                continue;
            }

            if (strpos($bundle->getPath(), '/vendor/') > 0 ||
                strpos($bundle->getPath(), '\\vendor\\') > 0
            ) {
                continue;
            }

            $arrBundles[] = $bundle;
        }

        foreach ($arrBundles as $k=>$bundle) {
            $output->writeln(sprintf("  %s -  %s", str_pad($k, 3, ' '), $bundle->getNamespace()));
        }

        $output->writeln('');

        $max = count($arrBundles)-1;

        $k = $dialog->askAndValidate($output,
            "In which bundle you wish to generate new model? [0 - $max] : ",
            function($a) use($max) {
                if (trim($a) == '') {
                    throw new \InvalidArgumentException('You must pick a bundle');
                }
                $b = intval($a);
                if ($b < 0 || $b >  $max) {
                    throw new \InvalidArgumentException(sprintf('Enter a number between 1 and %s', $max));
                }
                return $b;
            }
        );

        return $arrBundles[$k];
    }

} 