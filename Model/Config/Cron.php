<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_Core
 */

declare(strict_types=1);

namespace Mdkdev\Core\Model\Config;

use Exception;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Cron
 * @package Mdkdev\Core\Model\Config
 */
class Cron extends Value
{
    private const PATH = 'path';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param ValueFactory $configValueFactory
     * @param string $cronStringPath
     * @param string $cronModelPath
     * @param string $runModelPath
     * @param AbstractDb|null $resourceCollection
     * @param AbstractResource|null $resource
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        private readonly ValueFactory $configValueFactory,
        private readonly string $cronStringPath = '',
        private readonly string $cronModelPath = '',
        private readonly string $runModelPath = '',
        AbstractDb $resourceCollection = null,
        AbstractResource $resource = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return Cron
     * @throws Exception
     */
    public function afterSave(): Cron
    {
        if ($this->validate()) {
            $value = $this->getValue();

            try {
                $this->configValueFactory
                    ->create()
                    ->load($this->cronStringPath, self::PATH)
                    ->setValue($value)
                    ->setPath($this->cronStringPath)
                    ->save();
                $this->configValueFactory
                    ->create()
                    ->load($this->cronModelPath, self::PATH)
                    ->setValue($this->runModelPath)
                    ->setPath($this->cronModelPath)
                    ->save();
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage());
            }
        }

        return parent::afterSave();
    }

    /**
     * @return bool
     */
    private function validate(): bool
    {
        return $this->cronStringPath && $this->cronModelPath;
    }
}
