<?php
namespace Lazada\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;
class Topmenu implements ObserverInterface
{
    // public function __construct(
        
    // )
    // {
    
    // }
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();       
        $tree = $menu->getTree();
        $data = [
            'name'      => __('Home'),
            'id'        => 'home-page-id',
            'url'       => 'http://127.0.0.1/Lazada/'
        ];

        $data1 = [
            'name'      => __('Contact Us'),
            'id'        => 'contact-page-id',
            'url'       => 'http://127.0.0.1/Lazada/contact/'
        ];

        $data2 = [
            'name'      => __('About Us'),
            'id'        => 'about-page-id',
            'url'       => 'http://127.0.0.1/Lazada/aboutus/index/aboutus'
        ];
        $node = new Node($data, 'id', $tree, $menu);
        $node1 = new Node($data1, 'id', $tree, $menu);
        $node2 = new Node($data2, 'id', $tree, $menu);

        $menu->addChild($node);
        $menu->addChild($node1);
        $menu->addChild($node2);
        return $this;
    }
}