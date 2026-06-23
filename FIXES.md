Ce document explique les derniers fix apportés à l'application, après le rendu officiel.

# Problème : pas possible d’effectuer une action directement après la création d’un sondage.
Diagnostic : méthodes store et update du controller retournent toutes les deux poll à la fin, mais store le fait sans retourner les options. Quand on essaye de modifier, PollForm tente de faire .map() sur undefined -> erreur.
Fix : load les options dans store aussi.

# Problème : Quand je modifie un sondage et que je vais « enregistrer les modifications », le sondage se remet en mode brouillon, même s’il était déjà publié auparavant.
Diagnostic : la fonction saveDraft() passe ‘true’ en dur comme ‘is_draft »
Fix : changer le true hardcodé avec la variable ’is_draft.value’ 

# Problème : le bouton « publier le sondage » est disponible dans la modification du sondage, même si le sondage est déjà publié
Diagnostic : Pas de check si le poll existe déjà avant d’afficher le bouton « publier le sondage »
Fix : Rajout d’un v-if avant le bouton « publier le sondage » qui vérifie qu’aucun poll n’existe ou qu’il est en draft                                                                  

# Problème :Quand on revient en arrière (retour navigateur) après avoir checké les résultats d’un sondage depuis le dashboard, le sondage qui vient d’être créé a disparu.
Diagnostic : $polls est calculé une seule fois par Laravel dans AppPollDashboard.vue au moment du chargement de la page, et encodé dans data-props. Les données viennent de data-props (HTML figé), pas de l'API.
Fix : Ajout appel API dans onMounted du dashboard pour recharger les sondages depuis le serveur à chaque montage du composant, au lieu de se fier uniquement aux données figées dans data-props au chargement initial de la page.
## -> problème persiste. Fix introuvable pour le moment.

# Problème : on peut pas choisir plusieurs options même si le choix multiple est activé.
Diagnostic : le type était de radio codé en dur. Il faut une condition pour checker lequel est le bon -> VotePage.vue:98. En plus, selectedOption est toujours ref(nulle). Si v-model pointe sur une valeur simple, il écrase chaque coche.
Fix : Quand allow_multiple_choices est activé, l'input bascule en type="checkbox" et v-model pointe sur un tableau (selectedOptions) au lieu d'une valeur unique (selectedOption). La soumission boucle alors sur chaque option cochée pour envoyer un vote par option, et toutes les vérifications de sélection passent par isSelected() qui gère les deux cas.

# Problème : changement de vote non géré
Diagnostic : 
1. Backend : vote() faisait toujours un PollVote::create(...) sans vérifier si l'utilisateur avait déjà voté. Recharger le vote ajoutait une deuxième entrée en base pour le même utilisateur.
    1. Fix  : avant de créer le nouveau PollVote, on supprime tous les votes existants de cet utilisateur pour ce sondage avec un ->delete(). Ça évite d'accumuler plusieurs entrées en bas si l'utilisateur revote. Ce delete s'exécute aussi lors d'un premier vote (il ne trouve rien à supprimer, donc aucun effet de bord). -> ApiController.php:159
2. Frontend : VotePage.vue ne lisait jamais poll.allow_vote_change. Une fois hasVoted = true, la page affichait les résulttats définitivement, sans possibilité de revenir au formulaire.
    1. Fix : dans la section résultats, un bouton "Changer mon vote" apparaît uniquement si poll.allow_vote_change est vrai. En cliquant, il remet hasVoted à false et vide les sélections — ce qui fait réapparaître le formulaire de vote grâce au v-else-if existant. ->VotePage.vue:187

# Problème : Choix multiple + changement de vote : seul le dernier vote est enregistré
Quand allow_multiple_choices est vrai, submitVote (VotePage.vue, ligne 44) boucle sur selectedOptions et appelle vote() une fois par option. Or vote() (ApiPollController.php, ligne 158) fait un delete() avant chaque create() — donc chaque appel efface le vote précédent. Pour corriger, il faudrait un endpoint dédié qui reçoit un tableau d'IDs, supprime les anciens votes une seule fois, puis crée tous les nouveaux dans un foreach.

# Limitation connue : allow_vote_change non vérifié côté backend
Le paramètre allow_vote_change est uniquement géré côté frontend (VotePage.vue:189 — le bouton "Changer mon vote" est masqué si false). Le backend (ApiPollController@vote) accepte toujours un nouveau vote, même si allow_vote_change est false. Un utilisateur pourrait contourner la restriction en appelant l'API directement (Postman, curl, DevTools).
Fix à faire : dans vote(), récupérer le poll via $option->poll, vérifier $poll->allow_vote_change, et si false, vérifier qu'aucun PollVote n'existe déjà pour cet utilisateur et ce sondage. Si un vote existe déjà, retourner une erreur 403.

# Problème : double vote non pris en compte dans le polling en temps réel
Diagnostic : Dans ApiController.php@vote, la méthode supprime tous les votes de l'utilisateur pour le sondage avant d'en créer un nouveau. Quand submitVote() côté Vue appelle l'endpoint en boucle (une fois par option), chaque appel efface le vote de l'appel précédent. Résultat : seule la dernière option envoyée survivait en BDD.
Fix :   - Ajout : $poll = $option->poll pour accéder au sondage via la relation Eloquent
  - Si allow_multiple_choices : on supprime uniquement le vote de cette option spécifique (évite les doublons si revoté, mais préserve les autres choix)
  - Si choix unique : comportement inchangé — on supprime tous les votes (pour permettre de changer de réponse)
