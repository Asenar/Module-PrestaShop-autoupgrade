Module "Autoupgrade" for PrestaShop
## [DEV] HOW TO TEST 1.5 unstable
### 1 - get a prestashop 1.4 installation
### 2 - copy the whole content of that repos at the root of your prestashop
** modules/autoupgrade contains the main files
** override/classes/Upgrader.php allows to use the "unstable channel"
** (optionnal) rename _Tools.php to Tools.php in override/classes/ make firephp info (and see debug message in firebug)
### 3 - test :
** install the module
** go in Tools > Upgrade
** md5 checks, refresh
### 4 - feedback
** what's good ?
** what's bad ? 
** what's missing ?

#### some tips related to svn
The simple guide : http://rogerdudler.github.com/git-guide/
svn/git : http://git.or.cz/course/svn.html
http://book.git-scm.com/3_basic_branching_and_merging.html


