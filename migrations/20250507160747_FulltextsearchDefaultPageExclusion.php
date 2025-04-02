<?php

use YesWiki\Core\YesWikiMigration;
use YesWiki\FullTextSearch\Services\Repository\PageExclusionRepository;

class FulltextsearchDefaultPageExclusion extends YesWikiMigration
{
    public const DEFAULT_TAGS = [
        'DerniersChangementsRSS',
        'FacetteRessource',
        'GererConfig',
        'GererDroits',
        'GererDroitsActions',
        'GererDroitsHandlers',
        'GererMisesAJour',
        'GererSauvegardes',
        'GererSite',
        'GererThemes',
        'GererUtilisateurs',
        'LookWiki',
        'MesContenus',
        'MotDePassePerdu',
        'PageColonneDroite',
        'PageCss',
        'PageFooter',
        'PageHeader',
        'PageLogin',
        'PageMenu',
        'PageMenuHaut',
        'PagePrincipale',
        'PageRapideHaut',
        'PageTitre',
        'ParametresUtilisateur',
        'RechercheTexte',
        'ReglesDeFormatage',
        'SaisirAgenda',
        'SaisirAnnuaire',
        'SaisirBlog',
        'SaisirRessource',
        'TableauDeBord',
        'WikiAdmin',
        'YesWiki',
        'ListeType',
    ];

    public function run()
    {
        /** @var PageExclusionRepository $pageExclusionRepo */
        $pageExclusionRepo = $this->getService(PageExclusionRepository::class);
        foreach (self::DEFAULT_TAGS as $tag) {
            $pageExclusionRepo->addExclusion($tag);
        }
    }
}
