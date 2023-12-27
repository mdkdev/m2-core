<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_Core
 */

declare(strict_types=1);

namespace Mdkdev\Core\Block\Adminhtml\Form\Field\Option;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class FieldSource
 * @package Mdkdev\Core\Block\Adminhtml\Form\Field\Option
 */
class FieldSource extends Select
{
    /**
     * @param Context $context
     * @param OptionSourceInterface $sourceModel
     * @param bool $multi
     * @param int $size
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly OptionSourceInterface $sourceModel,
        private readonly bool $multi = false,
        private readonly int $size = 10,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $options = $this->sourceModel->toOptionArray();
            $this->setOptions($options);
        }

        if ($this->multi) {
            $this->setExtraParams(\sprintf('multiple="multiple" size="%s"', $this->size));
        }

        return parent::_toHtml();
    }

    /**
     * @param $value
     * @return FieldSource
     */
    public function setInputName($value): self
    {
        return $this->setName($value . ($this->multi ? '[]' : ''));
    }
}
