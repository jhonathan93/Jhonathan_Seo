<?php

namespace Jhonathan\Seo\Block;

use Jhonathan\Seo\Model\Data\Data;
use Magento\Cms\Model\Page;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class Metatag extends Template {
    /**
     * @var Page
     */
    protected $_page;

    /**
     * @var Data
     */
    protected $_db;

    /**
     * Metatag constructor.
     * @param Template\Context $context
     * @param Page $page
     * @param Data $db
     * @param array $data
     */
    public function __construct(Template\Context $context, Page $page, Data $db, array $data = []) {
        parent::__construct($context, $data);
        $this->_page = $page;
        $this->_db = $db;
    }

    /**
     * @return array|false
     */
    public function getInforMeta() {
        $pageId = $this->_page->getId();
        if (!is_null($pageId)) { //Verifica ser o ID da CMS page existe
            return $this->createMeta($this->_db->getCmsPage($pageId));
        }
        return false;
    }

    /**
     * @param array $stores
     * @return array
     */
    private function createMeta(array $stores): array {
        if (in_array(0, $stores[0])) { //Verifica ser a CMS page está vinculada a loja de ID 0, Se verdade significar que a página está vinculada a todas as lojas..
            $stores = $this->_db->getAllStores();
            if (sizeof($stores) > 1) { //Verifica ser o array tem mais de 1 elemento, se sim temos uma página vinculada a varias lojas...
                return $this->createArray($stores);
            }
        }

        if (sizeof($stores) > 1) {
            return $this->createArray($stores);
        }
        return [];
    }

    /**
     * Tem a responsabilidade de criar um array de todas as CMS pages, que estão vinculada a mais de uma loja, Contendo a URL base, URL page e o idioma da loja...
     * @param array $storesId
     * @return array
     */
    private function createArray(array $storesId): array {
        try {
            $options = array();
            foreach ($storesId as $key => $value) {
                $options[] = [
                    'locale' => $this->getLocaleStores($value['store_id']),
                    'PageUrl' => str_replace('/default', '', $this->_storeManager->getStore($value['store_id'])->getBaseUrl()).str_replace('home', '', $this->_page->getIdentifier())
                ];
            }
            return $options;
        } catch (NoSuchEntityException $e) {
            $this->_logger->alert(__('The informed store does not exist'));
            return [];
        }
    }

    /**
     * Pega o Idioma da loja pelo o ID da mesma.
     * @param int $id
     * @return mixed
     */
    private function getLocaleStores(int $id) {
        return $this->_scopeConfig->getValue('general/locale/code', 'store', $id);
    }
}
