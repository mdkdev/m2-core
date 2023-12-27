<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_Core
 */

declare(strict_types=1);

namespace Mdkdev\Core\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ScopeConfig
 * @package Mdkdev\Core\Config
 */
class ScopeConfig
{
    protected ?int $scopeId = null;

    /**
     * @param LoggerInterface $logger
     * @param ScopeConfigInterface $scopeConfig
     * @param JsonSerializer $jsonSerializer
     * @param DataObjectFactory $dataObjectFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly JsonSerializer $jsonSerializer,
        private readonly DataObjectFactory $dataObjectFactory,
        private readonly StoreManagerInterface $storeManager
    ) {}

    /**
     * @param string $path
     * @param string $scope
     * @param int|null $scopeId
     * @return string
     */
    public function getValue(
        string $path,
        string $scope = ScopeInterface::SCOPE_STORES,
        ?int $scopeId = null
    ): string {
        if (!$scopeId) {
            $scopeId = $this->getScopeId();
        }

        return (string)$this->scopeConfig->getValue($path, $scope, $scopeId);
    }

    /**
     * @param string $path
     * @param string $scope
     * @param int|null $scopeId
     * @return bool
     */
    public function isSetFlag(
        string $path,
        string $scope = ScopeInterface::SCOPE_STORES,
        ?int $scopeId = null
    ): bool {
        if (!$scopeId) {
            $scopeId = $this->getScopeId();
        }

        return $this->scopeConfig->isSetFlag($path, $scope, $scopeId);
    }

    /**
     * @return int|null
     */
    protected function getScopeId(): ?int
    {
        if ($this->scopeId === null) {
            try {
                $this->scopeId = (int)$this->storeManager->getStore()->getId();
            } catch (NoSuchEntityException $noSuchEntityException) {
                $this->logger->critical($noSuchEntityException->getMessage());
            }
        }

        return $this->scopeId;
    }

    /**
     * @param string $path
     * @param string $delimiter
     * @param string $scope
     * @param int|null $scopeId
     * @return array
     */
    public function getExplodedValue(
        string $path,
        string $delimiter = ',',
        string $scope = ScopeInterface::SCOPE_STORES,
        ?int $scopeId = null
    ): array {
        if ($data = $this->getValue(
            $path,
            $scope,
            $scopeId
        )) {
            return \explode($delimiter, $data);
        }

        return [];
    }

    /**
     * @param string $path
     * @param bool $objectify
     * @param string $scope
     * @param int|null $scopeId
     * @return array
     */
    public function getJsonDecodedValue(
        string $path,
        bool $objectify = true,
        string $scope = ScopeInterface::SCOPE_STORES,
        ?int $scopeId = null
    ): array {
        if ($configValue = $this->getValue(
            $path,
            $scope,
            $scopeId
        )) {
            try {
                $unserializedValues = $this->jsonSerializer->unserialize($configValue);
                if (!$objectify) {
                    return $unserializedValues;
                }

                foreach ($unserializedValues as $unserializedValue) {
                    if (!$unserializedValue) {
                        continue;
                    }

                    $objects[] = $this->dataObjectFactory
                        ->create()
                        ->addData($unserializedValue);
                }
            } catch (\InvalidArgumentException $invalidArgumentException) {
                $this->logger->critical($invalidArgumentException->getMessage());
            }
        }

        return $objects ?? [];
    }
}
