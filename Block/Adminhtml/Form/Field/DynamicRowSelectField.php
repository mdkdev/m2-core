<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_Core
 */

declare(strict_types=1);

namespace Mdkdev\Core\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class DynamicRowSelectField
 * @package Mdkdev\Core\Block\Adminhtml\Form\Field
 */
class DynamicRowSelectField extends DynamicRowField
{
    protected array $renderer = [];

    /**
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
            $fields,
            $data
        );

        $this->fields = $fields;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    protected function setCustomRenderer(): void
    {
        foreach ($this->fields as $key => &$field) {
            if (!isset($field['renderer'])) {
                continue;
            }

            $this->renderer[] = $key;
            $this->{$key} = $this->createBlock($field['renderer']);
            $field['renderer'] = $this->{$key};
        }

        unset($field);
    }

    /**
     * @param DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        foreach ($this->renderer as $renderer) {
            $data = $row->getData($renderer);

            if ($data && $this->{$renderer} !== null) {
                if (!\is_array($data)) {
                    $data = \explode(',', $data);
                }

                foreach ($data as $option) {
                    $options['option_' . $this->{$renderer}->calcOptionHash($option)] = 'selected="selected"';
                }
            }
        }

        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @param string $class
     * @return BlockInterface
     * @throws LocalizedException
     */
    private function createBlock(string $class): BlockInterface
    {
        return $this->getLayout()->createBlock(
            $class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );
    }
}
