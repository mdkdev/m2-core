<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_Core
 */

declare(strict_types=1);

namespace Mdkdev\Core\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class DynamicRowField
 * @package Mdkdev\Core\Block\Adminhtml\Form\Field
 */
class DynamicRowField extends AbstractFieldArray
{
    protected const SORT_ORDER = 'sort_order';

    protected array $fields = [];

    /**
     * DynamicRowField constructor.
     * @param Context $context
     * @param array $fields
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $fields,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->fields = $fields;
    }

    /**
     * @return void
     */
    protected function _prepareToRender(): void
    {
        \uasort($this->fields, [$this, 'sortFields']);

        $this->setCustomRenderer();

        foreach ($this->fields as $field => $data) {
            $this->addColumn($field, $data);
        }

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add row');
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function sortFields(
        array $a,
        array $b
    ): int {
        return isset($a[self::SORT_ORDER]) && isset($b[self::SORT_ORDER])
            ? $a[self::SORT_ORDER] - $b[self::SORT_ORDER]
            : 0;
    }

    /**
     * @return void
     */
    protected function setCustomRenderer(): void
    {}
}
