Tutoriel Full Text Search

Améliorer le moteur de recherche du wiki ;-)

 - Installer l'extension comme d'habitude : Roue crantée en haut à droite > Gestion du site > MAJ/Extensions > Tools (extensions) > fulltextsearch > Installer
 - Créer une page FullTextSearchAdmin et y mettre l'action {{FullTextSearchAdmin}}
 - Cliquer sur "initialiser" ou "réindexer"
 - Créer la page RechercheTexte et y mettre l'action {{FullTextSearchSearch}}
 - Personnaliser {{FullTextSearchSearch limit="100"}} pour spécifier combien de résultats afficher : par défaut ce sera 10, dans cet exemple on passe à 100
 - Modifier la PageMenuHaut > remplacer {{moteurrecherche template="moteurrecherche_button.tpl.html"}} par {{button class="btn btn-default navbar-btn" icon="fas fa-search" link="RechercheTexte" text=" " }}

