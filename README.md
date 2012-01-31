Module "Autoupgrade" for PrestaShop
# [DEV] HOW TO TEST 1.5 unstable
- 1 - get a prestashop 1.4 installation
- 2 - copy the whole content of that repos at the root of your prestashop
-	- modules/autoupgrade contains the main files
-	- override/classes/Upgrader.php allows to use the "unstable channel"
-	- override/classes/Tools.php to make firephp info (and see debug message in firebug)
- 3 - test :
-	- install the module
-	- go in Tools > Upgrade
-	- md5 checks, refresh

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


- problem with git/github

git checkout master # restore the link with the remote repository

git commit -a -m "// my comment" # commit my local modification in the local repository

git push origin master # hop, we send modifs to the remote server (the configuration of the "master branch" is already done and defined as the github repository )


# Add a new feature ? Let's create a branch (thanks Julien again :) )

git branch deadlyRoomOfDeath # branch creation

git checkout deadlyRoomOfDeath # let's switch to that branch

# working, 

git commit -a "// pouet pouet" # save our work with a clear comment on our local repository

git checkout master # go back to the main branch to work again on the core


# go further : http://book.git-scm.com/3_basic_branching_and_merging.html


