# Full text search

This extension add support for various full text search engines to Yeswiki.
It use the https://github.com/php-cmsig/search project to ensure a maximum of compatibility.
It implements only a few subset of the search engines available in this project, but if you want to add a new one feel free to add a PR or open an issue.

## Features
- Advanced search using tokenization, stemming, typo tolerance...
- Search in the content of bazar entry and pages in the wiki
- Exclude some pages from the search
- Filter result using user permissions
- Search in PDF attachments of bazar entries


## Installation

This extension is designed to be used out of the box without any configuration for simple use cases.
- Install the extension using the YesWiki extension manager
- Access the admin page of the extension using the {{ FullTextSearchAdmin }} action and click on the "Initialize" button
- The extension will automatically index the wiki pages and bazar entries on the database
- Add a search box in your wiki using the {{ FullTextSearchSearch }} action

## Configuration
Configuration is done under the `fulltextsearch` section of the `wakka.config.php` file.
```
'fulltextsearch' => [
    'import_batch_size' => 100, // Number of entries to index at once
    'engine_config' => [
        'driver' => 'loupe', // Search engine to use (loupe or typesense)
        'typesense_config' => [ // Configuration for the typesense engine. Not needed if you use another engine
            'api_key' => 'xyz',
            'host' => 'typesense',
            'port' =>  8108,
            'protocol' =>  'http',
        ],
     ] 
]
```

## Drivers

### Loupe
Loupe (https://github.com/loupe-php/loupe) is the default search engine used by this extension. It is based on sqlite so you need the sqlite3 extension to be installed on your server.
This is the case for almost all shared hosting providers.

### Typesense
Typesense is a fast open source search engine. You need to install it on your server or use https://cloud.typesense.org/ and configure the `typesense_config` section of the configuration file.

## Performance issue
This extension may use a lot of resource server side, especially when indexing attachments and with loupe search engine.
In case of error, try increasing the memory limit of your PHP configuration.
You can also decrease the `import_batch_size` parameter in the configuration file to reduce the number of entries indexed at once.
If you have more than a few thousand pages, you should consider using another driver than the default one (loupe) for the index.
