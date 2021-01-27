<?php

namespace Jhonathan\Seo\Model\Data;

use Magento\Framework\App\ResourceConnection;

/**
 * Class Data
 * @package Jhonathan\Seo\Model\Data
 */
class Data {
    /**
     * @var ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * Data constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection) {
        $this->_resourceConnection = $resourceConnection;
    }

    /**
     * retorna uma array como todas as store_id que tem a CMS page vinculada a loja.
     * @param int $pageId
     * @return array
     */
    function getCmsPage(int $pageId): array {
        $connection = $this->_resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['cp' => $this->_resourceConnection->getTableName('cms_page_store')],
            ['store_id']
        )->where(
            'cp.page_id = :page_id'
        );

        return $connection->fetchAll($select, ['page_id' => $pageId]);
    }

    /**
     * Retorna uma array como todas as store_id cadastrada no banco.
     * @return array
     */
    function getAllStores(): array {
        $connection = $this->_resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['s' => $this->_resourceConnection->getTableName('store')],
            ['store_id']
        )->where(
            's.store_id != :store_id'
        );

        return $connection->fetchAll($select, ['store_id' => 0]);
    }

}
