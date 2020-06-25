<?php

namespace Lazada\SearchAutocomplete\Plugin;

use \Magento\Framework\App\RequestInterface;

class AddPromotionToConfigPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Lazada\SearchAutocomplete\Helper\Data
     */
    protected $helper;

    /**
     * AddPromotionToConfigPlugin constructor.
     *
     * @param RequestInterface $request
     * @param \Lazada\SearchAutocomplete\Helper\Data $helper
     */
    public function __construct(
        RequestInterface $request,
        \Lazada\SearchAutocomplete\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->helper  = $helper;
    }

    /**
     * @param \Magento\Config\Block\System\Config\Edit $subject
     * @param string $result
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterToHtml(\Magento\Config\Block\System\Config\Edit $subject, $result)
    {
        if ($this->request->getFullActionName() == 'adminhtml_system_config_edit'
            && $this->request->getParam('section') == 'mageworx_searchsuite'
            && !$this->helper->isModuleOutputEnabled('MageWorx_SearchSuiteSphinx')
        ) {
            $renderer = $subject
                ->getLayout()
                ->createBlock(\Magento\Framework\View\Element\Template::class)
                ->setTemplate('Lazada_SearchAutocomplete::promotion.phtml');

            return $renderer->toHtml() . $result;
        }

        return $result;
    }
}