<?php

namespace AUS\AusDriverAmazonS3\Cache;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class MetaInfoCache extends AbstractCache
{
    /**
     * @param string $identifierPrefix
     */
    public static function getInstance($identifierPrefix = null)
    {
        return GeneralUtility::makeInstance(static::class, 's3_metainfo', $identifierPrefix);
    }
}