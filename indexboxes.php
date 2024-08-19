<?php

use Module\IndexBoxes\Database\IndexBoxInstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}


class Indexboxes extends Module
{
    public function __construct()
    {
        $this->name = 'indexboxes';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Jakub Kondrat';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Boxes for index', [], 'Modules.Indexboxes.Admin');
        $this->description = $this->trans('Add image boxes for your index page that links to categories, products or cms', [], 'Modules.Indexboxes.Admin');

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall module?', [], 'Modules.Indexboxes.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.7', 'max' => '8.99.99');
    }

    public function install()
    {
        return $this->installTables() && parent::install() && $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        return $this->removeTables() && parent::uninstall();
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * @return bool
     */
    private function installTables()
    {
        /** @var QuoteInstaller $installer */
        $installer = $this->getInstaller();
        $errors = $installer->createTables();

        return empty($errors);
    }

    /**
     * @return bool
     */
    private function removeTables()
    {
        /** @var IndexBoxInstaller $installer */
        $installer = $this->getInstaller();
        $errors = $installer->dropTables();

        return empty($errors);
    }

    /**
     * @return IndexBoxInstaller
     */
    private function getInstaller()
    {
        try {
            $installer = $this->get('prestashop.module.indexboxes.boxes.install');
        } catch (Exception $e) {
            // Catch exception in case container is not available, or service is not available
            $installer = null;
        }

        // During install process the modules's service is not available yet so we build it manually
        if (!$installer) {
            $installer = new IndexBoxInstaller(
                $this->get('doctrine.dbal.default_connection'),
                $this->getContainer()->getParameter('database_prefix')
            );
        }

        return $installer;
    }

    public function getContent()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminIndexboxesBox')
        );
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }

    public function hookDisplayHome()
    {
        $repository = $this->get('prestashop.module.indexboxes.repository.box_repository');
        $langId = $this->context->language->id;
        $shopId = $this->context->shop->id;
        $boxes = $repository->getAllActive($langId, $shopId);

        $this->smarty->assign(['boxes' => $boxes]);
        return $this->display(__FILE__, 'views/templates/front/boxes-container.tpl');
    }
}
