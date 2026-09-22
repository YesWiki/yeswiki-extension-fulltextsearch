# Extension fulltextsearch

Ajoute à YesWiki la recherche plein texte servie par différents moteurs. Elle s'appuie
sur le projet <https://github.com/php-cmsig/search> pour rester compatible avec le plus
grand nombre d'entre eux. Seuls quelques moteurs de ce projet sont implémentés ici ;
pour en ajouter un, ouvrez une issue ou proposez une PR.

**L'extension PHP sqlite3 est nécessaire au fonctionnement.**

## Ce qu'elle apporte

- Recherche avancée : découpage en tokens, racinisation, tolérance aux fautes de frappe.
- Recherche dans le contenu des fiches bazar et des pages du wiki.
- Exclusion de certaines pages de la recherche.
- Filtrage des résultats selon les droits de la personne connectée.
- Recherche dans les PDF joints aux fiches bazar.

## Installation

L'extension est prévue pour fonctionner sans configuration dans les cas simples.

1. Installer l'extension par le gestionnaire d'extensions de YesWiki.
2. Ouvrir la page d'administration de l'extension avec l'action
   `{{fulltextsearchadmin}}` et cliquer sur « Initialiser ».
3. L'extension indexe alors les pages du wiki et les fiches bazar.
4. Ajouter un champ de recherche avec l'action `{{fulltextsearchsearch}}`.

| Paramètre de `{{fulltextsearchsearch}}` | Défaut | Rôle |
|---|---|---|
| `limit` | `10` | nombre maximum de résultats affichés |
| `placeholder` | `What you search` | texte affiché dans le champ vide |
| `buttonside` | `left` | côté du bouton par rapport au champ, `left` ou `right` |

## Mises à jour

Certaines mises à jour demandent une réindexation complète. Elle n'est pas automatique,
pour ne pas consommer les ressources du serveur sans prévenir. Le `CHANGELOG.md` indique
quand elle est nécessaire.

## Configuration

Dans la section `fulltextsearch` de `wakka.config.php`.

```php
'fulltextsearch' => [
    'import_batch_size' => 100, // nombre de fiches indexées d'un coup
    'entries_pdf_indexing' => true, // false désactive l'indexation des PDF des fiches
    'engine_config' => [
        'driver' => 'loupe', // moteur utilisé : loupe ou typesense
        'typesense_config' => [
            // configuration du moteur typesense
            // inutile si vous utilisez un autre moteur
            'api_key' => 'xyz',
            'host' => 'typesense',
            'port' => 8108,
            'protocol' => 'http',
        ],
    ],
    // rendu des résultats de recherche
    // le moteur peut rendre des valeurs inférieures à celles demandées ici
    'rendering' => [
        'length_crop' => 50, // longueur du rognage, 50 caractères par défaut
        'length_excerpt_max' => 200, // longueur maximale de l'extrait, 200 par défaut
    ],
]
```

## Pourquoi `doctrine/lexer` est bridé en `^2.0`

Cette contrainte n'est pas là par hasard et ne doit pas être élargie. Le cœur de
YesWiki charge ses routes avec `doctrine/annotations`, dont le `DocParser` accède aux
jetons comme à des tableaux. `Doctrine\Common\Lexer\Token` implémente `ArrayAccess`
en 2.x, plus en 3.x.

YesWiki charge l'autoloader de chaque extension. Si celui de `fulltextsearch` apporte
`doctrine/lexer` 3.x, c'est cette classe qui l'emporte, et le cœur tombe au démarrage
sur `Cannot use object of type Doctrine\Common\Lexer\Token as array` : le wiki entier
ne répond plus, pas seulement la recherche.

`loupe/loupe` accepte `^2.0 || ^3.0`, donc brider en `^2.0` ne coûte rien ici.

## Moteurs

### Loupe

Loupe (<https://github.com/loupe-php/loupe>) est le moteur utilisé par défaut. Il
s'appuie sur sqlite, d'où le besoin de l'extension PHP sqlite3, présente chez presque
tous les hébergeurs mutualisés.

### Typesense

Typesense est un moteur de recherche libre et rapide. Il faut l'installer sur votre
serveur ou passer par <https://cloud.typesense.org/>, puis renseigner la section
`typesense_config`.

## Consommation de ressources

L'extension peut demander beaucoup au serveur, en particulier à l'indexation des pièces
jointes et avec le moteur loupe. En cas d'erreur, augmenter la limite mémoire de PHP.
Baisser `import_batch_size` réduit le nombre de fiches indexées d'un coup. Au-delà de
quelques milliers de pages, mieux vaut choisir un autre moteur que loupe.
