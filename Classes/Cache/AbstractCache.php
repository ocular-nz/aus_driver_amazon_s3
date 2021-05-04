<?php

namespace AUS\AusDriverAmazonS3\Cache;

use ArrayAccess;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract class AbstractCache implements ArrayAccess {
        
    /** @var FrontendInterface */
    protected $cache;

    /** @var string */
    protected $prefix = '';

    /** @var int */
    protected $ttl = 0;

    /** 
     * memory buffer
     * @var array
     */
    protected $buffer = [];

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
        $hash = $this->hashOffset($offset);
        return isset($this->buffer[$hash]) || $this->cache->has($hash);
    }
    
    public function offsetGet($offset)
    {
        $hash = $this->hashOffset($offset);
        
        if (empty($this->buffer[$hash])) {
            $this->buffer[$hash] = $this->cache->get($hash);
        }
        return $this->buffer[$hash];
    }
    
    public function offsetSet($offset, $value)
    {
        $hash = $this->hashOffset($offset);

        $this->buffer[$hash] = $value;
        return $this->cache->set($hash, $value, [], $this->getTtl());
    }
    
    public function offsetUnset($offset)
    {
        $hash = $this->hashOffset($offset);

        unset($this->buffer[$hash]);
        return $this->cache->remove($hash);
    }

    protected function getPrefix() {
        return $this->prefix;
    }

    protected function getTtl() {
        return $this->ttl;
    }

    protected function hashOffset($offset) {
        return hash('sha1', $this->getPrefix().$offset);
    }
}