<?php
/**
 * @author Marcel de Koning
 * @copyright Marcel de Koning, All rights reserved.
 * @package Mdkdev_Core
 */

declare(strict_types=1);

namespace Mdkdev\Core\Helper;

use Magento\Framework\Phrase;

/**
 * Class ConvertStringToPhrase
 * @package Mdkdev\Core\Helper
 */
class ConvertStringToPhrase
{
    /**
     * @param string $string
     * @param string $explodeCharacter
     * @return Phrase
     */
    public static function convert(
        string $string,
        string $explodeCharacter = '_'
    ): Phrase {
        $explodedString = \array_map(static function ($item) {
            return \ucfirst($item);
        }, \explode($explodeCharacter, $string));

        return __(\implode(' ', $explodedString));
    }
}
