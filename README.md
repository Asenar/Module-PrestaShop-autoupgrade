Module "Autoupgrade" for PrestaShop
# [DEV] HOW TO TEST 1.5 unstable
1 - get a prestashop 1.4 installation
2 - copy the whole content of that repos at the root of your prestashop
	- modules/autoupgrade contains the main files
	- override/classes/Upgrader.php allows to use the "unstable channel"
	- override/classes/Tools.php to make firephp info (and see debug message in firebug)
3 - test :
	- install the module
	- go in Tools > Upgrade
	- md5 checks, refresh

# et ici quelques notes pour comprendre git
# CQNFPO (ce qu'il ne faut pas oublier)
# 1 - toutes les actions sont faites "en local" sauf "git push"

test edition README

1) local edit, then
git add [myFile] # add a file or prepare modified file to be commited
2) local commit
git commit 
3) commit to github
git push origin master


- un problème avec git/github

git checkout master # on restaure le lien avec le dépot distant
git commit -a -m "// my comment" # commettre ses modifications en local
git push origin master # hop, on envoie ces modifications sur github (car on a précédemment configuré la branche "master" comme étant le dépot distant sur github )

# Faire une nouvelle fonctionnalité ? Créons une branche ! (merci Julien encore :) )
git branch deadlyRoomOfDeath # création de la branche
git checkout deadlyRoomOfDeath # travaillons sur cette branche
# working, 
git commit -a "// pouet pouet" # sauvons notre travail
git checkout master # retournons sur la branche principale

# pour aller plus loin : http://book.git-scm.com/3_basic_branching_and_merging.html


