<?php

namespace SpotApi;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Doctrine\ORM\Tools\SchemaTool;


class SpotApi extends Plugin{

    /**
     * Install-method of plugin
     *
     *
     * @throws Exception
     */
    public function install(InstallContext $context)
    {
        echo "ON INSTALLL!";
        try {
            $modelManager = Shopware()->Models();
            $schemaTool = new SchemaTool($modelManager);
            $models = ['Genre'];
            $schemaManager = $modelManager->getConnection()->getSchemaManager();
            foreach($models as $m) {
                $className = 'SpotApi\Models\\'.$m;
                $modelClass = $modelManager->getClassMetadata($className);
                if (!$schemaManager->tablesExist([$modelClass->getTableName()])) {
                    $schemaTool->createSchema(
                        [$modelClass]
                    );
                } else {
                    echo "IT was said that table exist!\n\n";
                }
            }

        } catch (Exception $e) {
            echo "EXCEPTION!\n\nclassName";
            throw  $e;
        }
    }
    /**
     * Uninstall-method of plugin
     */
    public function uninstall(UninstallContext $context)
    {

        try {

            $modelManager = Shopware()->Models();
            $schemaTool = new SchemaTool($modelManager);
            $models = ['Genre'];
            $schemaManager = $modelManager->getConnection()->getSchemaManager();
            foreach($models as $m) {
                $className = 'SpotApi\Models\\'.$m;
                $class = $modelManager->getClassMetadata($className);
                if ($schemaManager->tablesExist([$class->getTableName()])) {
                    $schemaTool->dropSchema(
                        [$class]
                    );
                }
            }

        } catch (Exception $e) {
            throw  $e;
        }
    }


}