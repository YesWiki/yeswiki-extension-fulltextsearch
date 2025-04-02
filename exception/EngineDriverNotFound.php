<?php

namespace YesWiki\FullTextSearch\Exception;

class EngineDriverNotFound extends \RuntimeException
{
    public function __construct(
    )
    {
        parent::__construct(
            _t('FULLTEXTSEARCH_EXCEPTION_DRIVER_NOT_FOUND'),
        );
    }
}
