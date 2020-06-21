<p align="center"><img src="../storage/app/public/images/logo_footer.png" width="400"></p>

# My Starting Five , Une fantasy league NBA






###                1.1.        Présentation du projet



L'application MS5 est un simulateur de championnat de basket s'appuyant sur les statistiques en temps réel de la NBA.

Elle permet de créer une ligue fictive de 2 à 12 utilisateurs maximum qui font chacun 2 matchs (aller/retour) contre tous les adversaires de la ligue, à raison d’un match par semaine. La saison dure donc 11 semaines maximum.

Chaque ligue, publique ou privée, commence par une phase de draft ou les joueurs dépensent un salary cap fixé à  200 M $ sur un système d'enchères par joueurs. Chaque utilisateur doit avoir 12 joueurs dans son effectif.

MS5 est un jeu qui se veut réaliste et équilibré dans sa gestion des statistiques, tout en restant fun pour l’utilisateur.

Laravel is accessible, powerful, and provides tools required for large, robust applications.

##               1.2.        Concept et objectifs

L’objectif du projet est de fournir à un public francophone et amateur de basket américain la possibilité de bénéficier d’une simulation sportive à la hauteur de leur passion.

Nous avons essayé de mettre l’accent sur le réalisme, aussi bien dans le salary cap que dans le calcul des cotes des joueurs et des scores des équipes.

Une de nos principales préoccupations a été de placer l’expérience utilisateur au coeur de la navigation, orientée principalement vers le mobile. En effet, nous considérons notre application comme un divertissement quotidien, mais avec des session de jeu de faible durée.


##               1.3.        Présentation des fonctionnalités

<ul>
    <li>Utilisation API externe</li>
</ul>


Afin de réaliser notre fantasy league MyStarting 5 il nous a fallu récupérer les véritables données de la saison NBA en cours. Les données dont nous avons eu besoin étant principalement : les statistiques des joueurs, sur leurs saisons en cours et passées, leur statut, blessé ou disponible.

Toutes ces données ont été récoltées via une API externe gratuite http://nbasense.com, qui fournit un composant permettant de réaliser simplement les requêtes php. 

Nous avons également utiliser l’API https://nba-players.herokuapp.com pour récupérer les photos des joueurs NBA. 

<ul>
    <li>Stockage en Json </li>
</ul>
 

Pour ne pas surcharger notre base de données avec de nombreuses colonnes contenant l’ensemble des données statistiques nécessaires, nous avons fait le choix de stocker ces informations dans une seule colonne sous format Json dans la table players.

<ul>
    <li>Automatisation des tâches</li>
</ul>

MyStarting5 est une fantasy league basée sur la NBA, qui est une ligue de basketball dans laquelle les matchs se déroulent à un rythme soutenu ( 82 matchs par équipe sur une saison de 6 mois).  
Les statistiques des joueurs nba varient donc très régulièrement et nous devons actualiser tout aussi régulièrement nos données pour rester à jour.

De plus, nos utilisateurs ont des matchs prévus chaque semaine, les résultats de ces matchs étant basés sur les statistiques réelles des joueurs NBA. Il nous a donc été nécessaire de rendre automatique la mise à jour de l’ensemble des données relatives à la ligue et ses joueurs. Pour ce faire, nous avons eu recours au système de planification de tâches de Laravel. Travaillant sur serveur local, nous n’avons pas pu configurer de tâches Cron, nous avons donc dû faire en sorte de simuler cette automatisation. Les différentes tâches à réaliser de façon programmées sont  alors stockées dans des classes php qui leur sont dédiées. Les classes en question ayant pour parent la Class Command, elles peuvent être lancées directement depuis le terminal. 
Comme nous utilisons le système de planification fourni par Laravel, une fois le serveur en ligne paramétré, il sera possible de mettre en place des tâches Cron. 

Nous avons mis en place des tâches automatisées qui ont deux utilités : stocker des données externes en base de données et en Storage ainsi que l'exécution de fonctions qui permettent l’avancement du jeu.

<ul>
    <li>Mises à jour automatique de la BDD</li>
</ul>

En ce qui concerne le stockage en Storage, nous avons fait le choix de stocker le fichier json qui comprend l’ensemble des identifiants (player_external_id dans la table players) des joueurs de la NBA provenant de l’api externe ainsi que le fichier qui stock l’état du joueur ( blessé ou disponible) afin de limiter nos appels à l’api.

<ul>
    <li>Mise à jour automatique des Statistiques dans la Base de Données</li>
</ul>

Pour ce qui est de l’insertion automatique des statistiques NBA dans notre table player, nous avons également mis en place une tâche programmée qui s'exécute chaque semaine peu avant que les matchs de nos utilisateurs aient lieu. 
Cela nous permet d’avoir les dernières statistiques à jour juste avant les matchs.


##               1.4.        Présentation du parcours utilisateur

<ul>
    <li>Inscription / connexion</li>
</ul>

L’authentification est gérée par une fonctionnalité de Laravel appelé auth, celle-ci générée grâce à une ligne de commande tous les dossiers nécessaires au fonctionnement et à la vérification de l’inscription et de la connexion. 

L’utilisateur devra remplir les champs du formulaire, le choix de son équipe favorite lui est demandé à ce moment là, cela lui affichera par la suite dans le dashboard le tweet feed de son équipe favorite ainsi que son logo. Si l’utilisateur n’a aucune équipe favorite, le tweet feed de la NBA et le logo du site MS5 lui est directement attribué. 
Une fois le formulaire rempli, il suffit de cliquer sur le bouton “s’inscrire” , ce qui génère automatiquement l’envoi d’un mail de confirmation.

Pour faire les test de mails nous avons utilisé  https://mailtrap.io/ .


Sur la page de connexion l’utilisateur a la possibilité de cliquer sur “mot de passe oublié”:  il est alors redirigé sur une autre page où il doit renseigner son adresse mail.
Il recevra un mail avec un bouton “Changer votre mot de passe”, ce qui le redirigera sur une page avec un formulaire, URL qui sera composé d’un token pour plus de sécurité.


<ul>
    <li>Dashboard évolutif selon état d’avancement de l’utilisateur dans le jeu</li>
</ul>

Le tableau de bord de l’utilisateur est son interface principale avec les différentes fonctionnalités du jeu. Il accompagne dynamiquement sa progression depuis la création de son compte jusqu’à la fin de sa league.

Après avoir créé son compte, l’utilisateur voit sur son tableau deux cards. L’une est constituée de l’actualité Twitter de son équipe favorite, et l’autre le guide pour ses premiers pas dans le jeu. Il y trouve explicitées les différentes étapes à suivre, ainsi qu’un bouton de redirection vers les différentes pages concernées.

Ainsi, le premier contenu de cette card invite l’utilisateur à créer ou rejoindre une league. Ensuite, le tableau lui recommande de créer une équipe, puis l’informe qu’il doit activer sa league s’il en est le créateur, ou consulter sa league jusqu’à l’activation de celle-ci. Ensuite, il est invité à participer à la draft. Une fois celle-ci achevée, le tableau de bord devient le meilleur atout de l’utilisateur, puisqu’il résume tout ce qui le concerne, tout en lui permettant d’accéder à des informations plus détaillées.

L’utilisateur y trouvera une card lui indiquant le temps avant le prochain match, lui rappelant son adversaire et lui permettant de faire ses choix en termes de composition d’équipe.

De plus il trouvera une card lui rappelant les pourcentages de victoire de l’ensemble des participants à la league, une card équipe lui rappelant son dernier 5, et enfin une card résumant le résultat du dernier match.

<ul>
    <li>Gestion des ligues </li>
</ul>

La gestion de la partie leagues se découpe principalement autour de trois vues. 

La première constitue un portail d’entrée laissant à l’utilisateur le choix de créer sa propre league en lui choisissant un nom, en lui définissant un statut ainsi qu’un nombre d’équipes maximum.

Si le nom est avant tout là pour un effet cosmétique, les autres éléments sont importants. Le statut, privé ou public, permet à loisir de restreindre ou d’ouvrir l’accession à la league privée. Si l'utilisateur fait le choix de créer une league privée, il reçoit un mot de passe alphanumérique dans le mail de confirmation de création de la league. Ce mot de passe est nécessaire pour qu’un autre utilisateur rejoigne sa league. Dans le cas d’une league publique, l’utilisateur est également notifié par mail de la création de sa league, mais celle-ci devient visible par tous dans la vue public, sur laquelle nous reviendrons par la suite.

Si l’utilisateur ne souhaite pas créer de league mais rejoindre une league privée, le créateur doit au préalable lui avoir communiqué le mot de passe, sans quoi il lui sera impossible de la rejoindre.

Si l'utilisateur souhaite rejoindre une league publique, il est redirigé depuis la page index (le portail), vers une deuxième vue qui est la page public, listant les leagues publiques. L’utilisateur peut en choisir une n’étant pas en cours (il ne pourrait pas la rejoindre et un message le prévient) et dans laquelle il reste des places disponibles. Quand une league est complète, le message ‘complet’ apparaît sur le tableau, en lieu et place du bouton rejoindre.

Quel que soit le choix de l'utilisateur, il lui est impossible de rejoindre plus d’une league à la fois. Le profil de l’utilisateur est associé à sa league (enregistrée dans la table leagues) via la table pivot league_user. Il lui est possible de consulter les informations relatives à sa league à l’aide d’une vue dédiée, à laquelle il peut accéder depuis son tableau de bord, dès lors qu’il a créé son équipe. Tant que la league n’est pas activée par le créateur, seuls les pseudos des participants apparaissent, avec le nom de leur équipe s'ils l’ont créée. Le créateur de la league peut décider de supprimer la league à tout moment, tant qu’il ne l’a pas activée. en revanche, il ne peut l’activer que si tous les participants ont créé leur équipe et si celle-ci compte un nombre pair de participants, ce qui rend possible le calcul du calendrier des matchs.

Tant que la league est active, les participants peuvent voir leur classement par le biais du calcul d’un pourcentage de victoire prenant en compte le résultat de leurs matchs, s’ils en ont disputé. Si ce n’est pas le cas, la vue affiche un message indiquant que l’équipe n’a pas disputé de match.

<ul>
    <li>Gestion des équipes</li>
</ul>

La gestion des teams s’articule autour de deux vues, l’une ayant pour vocation la création des teams (create) et l’autre un récapitulatif dynamique des informations de l’équipe (show).

La vue associée à la création de la team permet à l’utilisateur de choisir un nom pour son équipe, de définir un nom pour son stade et lui affiche le logo de son équipe favorite. Ces informations sont ensuite stockées dans la table teams de la base de données.

La vue show de sa team liste le roster à la disposition de l’utilisateur à l’issue de la draft, reprenant le portrait des joueurs, leur nom, leur cote (en M de $), leur poste, le nombre de matchs joués dans la league, ainsi que leur dernier score.

Par ailleurs, trois cards résument les informations associant la team à la league en cours :
le dernier 5 aligné,
des statistiques sur les matchs
le classement du joueur via son pourcentage de victoires.

<ul>
    <li>Système de Draft/ Enchère en temps réel</li>
</ul>


La phase de Draft débute une fois la ligue lancée par son créateur. Cette action du créateur de la ligue va provoquer l’enregistrement dans la table draft de l’id de la ligue, la date de fin de la ligue, défini par défaut à j+1 20h à partir du moment où le créateur de la ligue lance celle-ci. Nous enregistrons également un booleen is_over qui nous permet de réaliser facilement des vérifications concernant le statut de la draft (en cours ou non). (notamment pour la réalisation d’affichage conditionnel sur le dashboard)

Lorsque la draft est en cours, l’utilisateur peut accéder à la page ci-dessous qui récapitule l’ensemble des informations relatives à son équipe et à la draft en cours. Il peut également réaliser les diverses actions qui lui sont demandées au cours de cette phase de gameplay.

Le décompte draft indique en temps réel le temps restant avant la fermeture de la draft. L’utilisateur peut décider d’enchérir sur n’importe quel joueur de la NBA. Il dispose d’une somme totale de 200 millions de dollars, son salary cap, à dépenser et il lui est imposé de remplir son roster avec 12 joueurs : 5 arrières, 5 ailiers et 2 centres. Pour cela, il dispose d’un tableau qui reprend l’ensemble des joueurs, avec des tri possibles selon des critères de prix et de postes. Chaque joueur a un prix qui est déterminé en amont par ses stats réelles et est mis à jour automatiquement et régulièrement.

Pour remplir son effectif, l’utilisateur doit enchérir sur les joueurs qu’il veut acquérir. Au moment où l’utilisateur clique sur le bouton “enchérir”, le joueur apparaît sur le tableau de droite qui permet de suivre les différentes enchères en cours. 
Cette action va enregistrer dans notre table auctions toutes les enchères placées par les joueurs. Le temps limite de l’enchère est fixé à 2 minutes. Le temps imparti pour l’enchère est stocké dans la colonne auction_time_limit de la table auction. Si aucun autre utilisateur de la ligue ne vient enchérir avant la fin du temps imparti, le joueur nba va être attribué à l'utilisateur via un enregistrement dans la table pivot player_team. Si un autre joueur enchéri, le chrono de 2 minutes est réinitialisé et ainsi de suite.
Pour réaliser l’enregistrement automatique dans la table player_team, nous avons mis en place une tâche programmée qui boucle sur l’ensemble des drafts en cours pour déterminer si les enchères réalisées dans la ligue correspondante doivent être enregistrées. La condition étant que le temps imparti pour l’enchère soit atteint ou dépassé.

Si l’enchère est validée le joueur est alors “drafté” par l’équipe et n’est plus disponible pour les autres utilisateurs de la ligue. 
Si l’utilisateur n’a pas encore participé à une enchère sur un joueur il peut voir dans le tableau récapitulatif le prix actuel du joueur ainsi que son prix initial. 
La possibilité d’enchérir ou de drafter un joueur est bien sûr conditionnée par le salary cap actuel de l’utilisateur. 

<ul>
    <li>Fin de la draft</li>
</ul>

A la fin de la phase de draft, la page n’est plus accessible pour l’utilisateur. S’il n’a pas rempli son effectif avant la fin de la draft, des joueurs lui seront attribués aléatoirement parmi les joueurs les moins chers de la ligue. Cette action est générée via une tâche automatisée qui compare le timestamp actuel au timestamp ends_at de la draft et procède à la répartition aléatoire des joueurs s’il lui est égal ou supérieur.

<ul>
    <li>Génération du calendrier des matchs de la ligue</li>
</ul>

La même tâche automatisée va générer le calendrier des matchs, les équipes de la ligue se rencontrent aléatoirement 2 fois pendant la saison, au rythme de 1 match par semaine pour chaque équipe.

<ul>
    <li>Composition de l’équipe</li>
</ul>

Avant chaque rencontre, l’utilisateur dispose d’un temps imparti pour constituer son équipe. Il doit choisir 5 joueurs : 2 arrières, 2 ailiers et 1 pivot. 

Il peut modifier sa composition à tout moment tant que le temps imparti n’est pas atteint, en cliquant sur l'icône “X” sur la carte du joueur. 
C’est également ici que l’utilisateur peut savoir si son joueur est actuellement blessé ou non, et s’il veut malgré tout l’aligner sur la feuille de match. 
Pour inciter les utilisateurs à participer activement à MyStarting5 nous avons fait le choix, contrairement à la phase de draft, de ne pas sélectionner aléatoirement des joueurs dans la composition du match. Si l’utilisateur ne choisit pas de joueurs son score sera simplement de 0 lors du prochain match.  

<ul>
    <li>Match et résultats</li>
</ul>

Chaque dimanche à 20h00, une tâche enregistrée vient définir le résultat des matchs de la semaine. Les stats des joueurs NBA sur les matchs joués pendant la semaine sont enregistrées puis une moyenne est faite en pondérant les différentes lignes de stats, ce qui nous donne un score global pour chaque joueur. les scores sont ensuite attribués à chaque équipe, l’équipe qui a le plus grand score l’emporte. 

<ul>
    <li>Fin de ligue</li>
</ul>

Lorsqu’il n’y a plus aucun match à venir dans une ligue, celle-ci prend fin, les résultats de la ligue restent accessibles pendant une semaine puis sont supprimés et les utilisateurs qui en faisaient partie peuvent en rejoindre une autre. 


##               1.5.        Pour jouer à MS5 quelque lignes de commandes sont nécessaires

## 1.5.1  Installation du projet 

<ul>
    <li>cloner le repository</li>
</ul>

        git clone https://github.com/royjerem/MS5.git
        

 <ul>
    <li>Installer les dépendances du projet</li>
 </ul>      
 
    composer install
    
  <ul>
     <li>Installation des packedges et compilation du css et js</li>
  </ul>       

    npm install && npm run dev
    
   <ul>
        <li>Lancer le seed "RolesTableSeeder.php" pour attribuer les roles dans la bdd</li>
   </ul>
   
      php artisan db:seed
    
   ## 1.5.2  Installation des CustomClass 
   
   <ul>
     <li>Pour récupérer tous les joueurs de la NBA en fichier json</li>
   </ul>
   
    php artisan StoreAllNbaPlayers   
    
   <ul>
      <li>Pour calculer et stocker le fichier json des joueurs blessés</li>
   </ul>
   
    php artisan StoreAllNbaPlayersInjuryData 
   
   <ul>
      <li>Pour récupérer tous les teams de la NBA en fichier json</li>
   </ul>
   
    php artisan StoreAllNbaTeams  
      
   <ul>
      <li>Pour récupérer tous les données de chaque joueurs de la NBA en fichier json (500 requete)</li>
   </ul>
    
    php artisan StoreNbaPlayerData  
    
   <ul>
     <li>Pour vérifier si le joueurs n’est pas blessé en un fichier json</li>
   </ul>
   
    php artisan StoreNbaPlayerInjuryData

   <ul>
        <li>Pour récupérer tous les url des photos sur chaque joueurs de la nba en un  fichier json</li>
   </ul>
   
    php artisan StoreNbaPlayerPhotoUrl
   
   <ul>
      <li>Pour calculer le score de chaque joueurs de la nba </li>
   </ul>
   
    php artisan UpdateNbaPlayersScores

   ## 1.5.3  Lancer la fin de la draft
   
   <ul>
      <li>Pour enregistrer les choix de draft</li>
   </ul>
   
    php artisan SaveDraftPick
   
   <ul>
    <li>Pour générer la fin de la draft et attribuer des joueurs en automatique si l'utilisateur n’as pas fini ça draft</li>
   </ul>
   
    php artisan GenerateMatchesCalender
     
   ## 1.5.4  Lancer la fin d'un match 
   
   <ul>
     <li>Pour la fin du match calculer le score</li>
   </ul>
   
    php artisan PlayWeeklyMatches  
   

 ##               1.6.        Fin d'une League
 
 
 <ul>
    <li>Pour supprimer une league</li>
 </ul>
 
    php aritsan EndOfLeague

   
