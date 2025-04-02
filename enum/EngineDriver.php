<?php

namespace YesWiki\FullTextSearch\Enum;

enum EngineDriver: string
{
    case LOUPE = 'loupe';
    case TYPESENSE = 'typesense';
}
