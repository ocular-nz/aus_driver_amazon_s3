<?php

namespace AUS\AusDriverAmazonS3\Cache;

use ArrayAccess;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractCache implements ArrayAccess {
        
    /** @var FrontendInterface */
    protected $cache;

    /** @var string */
    protected $prefix = '';

    /** @var int */
    protected $ttl = 0;

    /**
     * @param string $cacheIdentifier
     * @param string $identifierPrefix
     */
    public function __construct($cacheIdentifier, $identifierPrefix, $ttl = 0)
    {
        $this->cache = GeneralUtility::makeInstance(CacheManager::class)->getCache($cacheIdentifier);
        $this->prefix = $identifierPrefix;
        $this->ttl = $ttl;
    }
    
    public function offsetExists($offset)
    {
        return $this->cache->has($this->hashOffset($offset));
    }
    
    public function offsetGet($offset)
    {
        return $this->cache->get($this->hashOffset($offset));
    }
    
    public function offsetSet($offset, $value)
    {
        return $this->cache->set($this->hashOffset($offset), $value, [], $this->getTtl());
    }
    
    public function offsetUnset($offset)
    {
        return $this->cache->remove($this->hashOffset($offset));
    }

    protected function getPrefix() {
        return $this->prefix;
    }

    protected function getTtl() {
        return $this->ttl;
    }

    protected function hashOffset($offset) {
        return hash('sha256', $this->getPrefix().$offset);
    }
}