<?php

namespace YesWiki\FullTextSearch\Services\Factory;

use CmsIg\Seal\Schema\Field;
use CmsIg\Seal\Schema\Index;
use CmsIg\Seal\Schema\Schema;

class SchemaFactory
{
    public const INDEX_NAME = 'pages';

    public function createSchema(): Schema
    {
        return new Schema([
            self::INDEX_NAME => new Index(self::INDEX_NAME, [
                'tag' => new Field\IdentifierField('tag'),
                // use tag twice to be able to search on it
                'tag_searchable' => new Field\TextField('tag_searchable', searchable: true, filterable: true),
                'title' => new Field\TextField('title', searchable: true),
                'fulltext' => new Field\TextField('fulltext', searchable: true),
            ]),
        ]);
    }
}
