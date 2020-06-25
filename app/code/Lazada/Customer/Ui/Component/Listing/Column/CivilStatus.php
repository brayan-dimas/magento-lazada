<?php

namespace Lazada\Customer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class CivilStatus extends Column
{
    /** Url path */
    // const ROW_EDIT_URL = 'sample_module/index/edit';
    /** @var UrlInterface */
    // protected $_urlBuilder;

    /**
     * @var string
     */
    // private $_editUrl;
    protected $_resourceConnection;
    protected $connection;
    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param array              $components
     * @param array              $data
     * @param string             $editUrl
     */
    protected $_urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        \Magento\Framework\App\ResourceConnection $resourceConnection
        // $editUrl = self::ROW_EDIT_URL
    ) {
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function getFirstTableName()
    {
        return $this->_resourceConnection->getTableName('customer_entity');
    }

    public function getSecondTableName()
    {
        return $this->_resourceConnection->getTableName('customer_entity_varchar');
    }

    // public function getThirdTableName()
    // {
    //     return $this->_resourceConnection->getTableName('customer_address_entity_varchar');
    // }

    public function prepareDataSource(array $dataSource)
    {
        
        // echo '<pre>';
        //     print_r($result);
        // echo '</pre>';
        // die;
        // return $result;
        
        if (isset($dataSource['data']['items'])) {
            $firsttablename = $this->getFirstTableName();
            $secondtablename = $this->getSecondTableName();
            // $thirdtablename = $this->getThirdTableName();
            $connection = $this->_resourceConnection->getConnection();
                       
            foreach ($dataSource['data']['items'] as &$item) {     
                $query = 'SELECT second_tbl.value FROM '.$firsttablename.' as main_tbl
                LEFT join '.$secondtablename.' as second_tbl 
                ON main_tbl.default_billing = second_tbl.entity_id
                where main_tbl.entity_id = '.$item['entity_id'];            

                $result = $connection->fetchRow($query);   
                // Yung billing_mobile dito ay sa fields name sa ui_component para dun sya mag lagay ng value.
                $item['billing_mobile'] = $result['value'];   
                // echo '<pre>';
                //     print_r($item['billing_mobile']);
                // echo '</pre>';
                // die;

            }
        }
        return $dataSource;
    }
}
