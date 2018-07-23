<?php
namespace Thai\S3\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * @inheritdoc
 */
class Headers extends AbstractFieldArray
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->addColumn('header', ['label' => __('Header')]);
        $this->addColumn('value', ['label' => __('Value')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Custom Header');

        parent::_construct();
    }
}
