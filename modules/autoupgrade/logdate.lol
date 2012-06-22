Tue Jun 12 09:56:35 2012 +0200 | fix bug #PNM-277
Thu Jun 7 18:22:18 2012 +0200 | man svn ...
Thu Jun 7 18:18:40 2012 +0200 | main modifications asked monday are done
Wed Jun 6 20:04:38 2012 +0200 | add error box + ui improvements
Wed Jun 6 19:34:57 2012 +0200 | get back the autoupgrade last version informations
Wed Jun 6 17:01:21 2012 +0200 | still bruno's changes
Tue Jun 5 19:02:01 2012 +0200 | moving block for bling bling, start ...
Tue Jun 5 15:21:24 2012 +0200 | norms, "all files upgraded" end message, bind beforeunload event
Mon Jun 4 17:25:46 2012 +0200 | maj tab name + tab emplacement
Mon Jun 4 13:18:21 2012 +0200 | 0.5.6
Mon Jun 4 13:16:02 2012 +0200 | fix bug when local dir is used + timezone warning in ajax_upgradetab.php
Fri Jun 1 16:02:16 2012 +0200 | Merge branch 'rescue' of marinetti.fr:autoupgrade
Tue May 29 14:22:23 2012 +0200 | // remove not used anymore files
Tue May 29 14:19:42 2012 +0200 | // fix test_dir no longer checks . and .. (and .svn)
Tue May 29 14:03:16 2012 +0200 | // svn rev...
Mon May 28 14:56:19 2012 +0200 | fix backupdb no longer remove html tags + rm debug + norms
Wed May 23 18:02:07 2012 +0200 | maj translations + 0.5.4
Wed May 23 16:51:54 2012 +0200 | rev.
Wed May 23 16:40:11 2012 +0200 | bugfix if en.php is missing
Wed May 23 15:34:52 2012 +0200 | tag 0.5.2
Wed May 23 15:34:05 2012 +0200 | svn rev.
Wed May 23 15:32:13 2012 +0200 | now deprecated files are removed + bug fix warning_exists
Wed May 23 15:31:00 2012 +0200 | bugfix at module installation
Wed May 23 11:50:10 2012 +0200 | bugfix issest
Wed May 23 10:48:45 2012 +0200 | tag 0.5.1
Wed May 23 10:46:38 2012 +0200 | module now modify PS_UPGRADE_CHANNEL Configuration in database if available
Wed May 23 10:39:47 2012 +0200 | Merge branch 'master' of /home/michael/git/autoupgrade
Tue May 22 18:18:33 2012 +0200 | merge + private now uses link and md5
Tue May 22 15:38:58 2012 +0200 | now private release requires link and md5 (and no more private_key)
Mon May 21 21:00:36 2012 +0200 | fix backup/restore steps
Mon May 21 19:54:15 2012 +0200 | minor fix, by dm
Fri May 18 18:03:39 2012 +0200 | tag 0.4.13
Fri May 18 18:02:46 2012 +0200 | add warnings
Fri May 18 14:26:16 2012 +0200 | // maj from dm
Wed May 16 18:08:16 2012 +0200 | upgradeModules bugging ... 7 modules will be upgraded timeout ?
Wed May 16 11:29:10 2012 +0200 | deactivate custom module fix
Wed May 16 09:45:27 2012 +0200 | maj svn rev number oO
Tue May 15 20:01:55 2012 +0200 | more accurate description for PS_AUTOUP_KEEP_TRAD
Tue May 15 19:56:39 2012 +0200 | set to 0.4.13, and new feature mergeTranslation done git diff --cached autoupgrade.php
Tue May 15 11:45:19 2012 +0200 | dm modifs
Tue May 15 10:01:29 2012 +0200 | now test_dir skip img/p dir
Tue May 15 10:00:26 2012 +0200 | now configure button exists and redirect to autoupgrade page (by dMetzger)
Tue May 15 09:59:07 2012 +0200 | now autoupgrade is excluded from "checkModifiedFiles"
Mon May 14 19:41:39 2012 +0200 | 0.4.12 !
Mon May 14 19:38:03 2012 +0200 | design + trad
Mon May 14 19:13:10 2012 +0200 | fix expressions, + lang addslashes in js
Mon May 14 18:49:19 2012 +0200 | translations fix
Mon May 14 17:19:07 2012 +0200 | DEFINER=CURRENT_USER (instead of removing it)
Mon May 14 16:20:57 2012 +0200 | tag 0.4.11
Mon May 14 15:53:06 2012 +0200 | shop deactivated display
Mon May 14 11:48:00 2012 +0200 | now all .zip are allowed in download dir (for archive channel)
Mon May 14 11:32:31 2012 +0200 | upgrading db classes ...
Mon May 14 11:27:05 2012 +0200 | 0.4.11-dev
Mon May 14 11:21:37 2012 +0200 | additionnal check : cache MUST be deactivated
Fri May 11 19:15:36 2012 +0200 | tag 0.4.10
Fri May 11 18:40:21 2012 +0200 | fix Cache used
Fri May 11 17:56:36 2012 +0200 | fix big tables drop all tables
Fri May 11 17:12:03 2012 +0200 | prepare 0.4.9
Fri May 11 17:11:13 2012 +0200 | executeS now deactivate the cache
Fri May 11 15:32:21 2012 +0200 | now restore has a separate list of files to skip
Fri May 11 14:38:38 2012 +0200 | revert x > w for fopen, bzopen + fix THEME_NAME missing in 1.5 (no more retrocompatibility -_-)
Fri May 11 12:47:58 2012 +0200 | prepare to 0.4.8
Fri May 11 12:47:30 2012 +0200 | new way to handle keepTrad / keepMails
Fri May 11 12:17:51 2012 +0200 | new static max_written_allowed, config load fix, some expresssions more accurates
Wed May 9 17:25:28 2012 +0200 | removed Tools::file_exists_cache, does not exists in 1.2.5
Mon May 7 17:35:06 2012 +0200 | traductions, PNM-154
Mon May 7 16:02:23 2012 +0200 | translation workaround
Thu May 3 18:06:10 2012 +0200 | bugfix in ConfigurationTest test_dir is writable
Thu May 3 17:53:29 2012 +0200 | fix check root status
Thu May 3 17:02:43 2012 +0200 | preparation for 0.4.7 + translations fr
Thu May 3 16:24:07 2012 +0200 | fixed directory created during/after upgrade was not removed when restore
Thu May 3 12:42:10 2012 +0200 | add new checkbox to allow (or not) major upgrade in private channel
Wed May 2 17:31:41 2012 +0200 | set to 0.4.6
Wed May 2 16:12:46 2012 +0200 | - images are now saved - removed fake link for option fieldset
Mon Apr 30 18:56:59 2012 +0200 | maj for tag
Mon Apr 30 18:49:15 2012 +0200 | config is now correctly displayed
Thu Apr 19 19:04:21 2012 +0200 | fix cacheFs end of upgrade bug
Thu Apr 19 18:51:24 2012 +0200 | colors in console are now more subtiles
Thu Apr 19 18:50:32 2012 +0200 | deleteDirectory is now a method inside the class
Thu Apr 19 18:49:28 2012 +0200 | config advanced small fix + design
Thu Apr 19 18:46:24 2012 +0200 | change variable name for keepImage
Thu Apr 19 14:41:18 2012 +0200 | now refreshing the page use Tools::redirectAdmin()
Thu Apr 19 14:40:22 2012 +0200 | Warning "please update your configuration" now correctly displayed
Thu Apr 19 12:27:51 2012 +0200 | use _PS_ROOT_DIR_ for db() method
Thu Apr 19 12:18:33 2012 +0200 | commit only getConfig modification
Thu Apr 19 12:13:44 2012 +0200 | fix date_timezone warning
Thu Apr 19 11:18:01 2012 +0200 | [-] MO : autoupgrade now admin/autoupgrade is cleared on uninstallation
Wed Apr 18 19:45:30 2012 +0200 | for tag 0.4.4
Wed Apr 18 19:38:29 2012 +0200 | listFilesInDir bugfix simplification of displayForm bugfix option status is now correctly displayed
Wed Apr 18 19:05:41 2012 +0200 | [-] can now delete backup from back-office ( PNM-167 )
Tue Apr 17 19:52:27 2012 +0200 | change version to 0.4.3
Tue Apr 17 19:44:19 2012 +0200 | Now directory to remove for restoreProcess is listed (by listFilesToRemove )
Tue Apr 17 18:52:37 2012 +0200 | [*] now restoreFiles delete new files [-] Fix notice information abs/rel path
Tue Apr 17 18:31:49 2012 +0200 | now we can choose archive_file channel with a custom filename for zip
Thu Apr 12 15:10:51 2012 +0200 | change version number to 0.4.3-dev
Thu Apr 12 15:00:37 2012 +0200 | [-] MO : autoupgrade - prevent usage of undefined constants related to Cache
Wed Apr 11 15:48:37 2012 +0200 | // set version to 0.4.2
Wed Apr 11 11:18:58 2012 +0200 | gitignore
Tue Apr 10 20:08:41 2012 +0200 |  remove svn for dev unstable of the doom
Fri Apr 6 17:04:29 2012 +0200 | merge from master into advanced_mode + norms
Fri Apr 6 17:04:29 2012 +0200 | // merge from master into advanced_mode
Fri Mar 30 17:14:03 2012 +0200 | rollback next_params/nextParams, TODO remove directories
Fri Mar 30 16:33:11 2012 +0200 | tmp, norms
Fri Mar 30 16:32:42 2012 +0200 | norms
Fri Mar 30 14:55:12 2012 +0200 | norms
Fri Mar 30 14:33:00 2012 +0200 | norms
Fri Mar 30 12:51:34 2012 +0200 | silly fix debug forgotten
Fri Mar 30 12:04:25 2012 +0200 | fix INSTALL_PATH _PS_INSTALL_PATH_
Fri Mar 30 11:38:15 2012 +0200 | // no dev, use public
Fri Mar 30 11:35:52 2012 +0200 | // minor fixes, add private_exclude_major
Fri Mar 30 11:33:52 2012 +0200 | minor fix from 1.4 class ...
Wed Mar 28 17:13:04 2012 +0200 | stop at first match on channel.xml
Tue Mar 13 15:00:30 2012 +0100 | mod todo
Tue Mar 13 15:00:08 2012 +0100 | expressions
Mon Mar 12 18:02:32 2012 +0100 | config.xml and channel.xml updated
Mon Mar 12 17:59:40 2012 +0100 | better algorith for choosing the correct version to follow related to branch / channel hierarchy
Mon Mar 12 16:28:04 2012 +0100 | cleanning code
Mon Mar 12 16:27:24 2012 +0100 | trads
Mon Mar 12 16:03:04 2012 +0100 | tab parent is now default to AdminModules
Fri Mar 9 16:45:16 2012 +0100 | correct xml
Fri Mar 9 16:40:42 2012 +0100 | channel.xml improvements
Fri Mar 9 16:40:25 2012 +0100 | config.xml
Fri Mar 9 16:39:57 2012 +0100 | refresh system for Upgrader
Fri Mar 9 16:39:06 2012 +0100 | this->getConfig replaces Configuration::get, ergonomy expert mode
Fri Mar 9 16:36:54 2012 +0100 | fix 1.5 PS_ADMIN undefined in AdminSelfTab
Wed Mar 7 18:29:38 2012 +0100 | All bug fixed: restoreProcess delete all files (but not directories .. yet) all channel (including directory and archive) works upgrade with warning in upgradeDb does do restoration if an error happen (even fatal error), restoration is called automatically next version available in branch (1.4/1.5) is handled
Mon Mar 5 17:34:38 2012 +0100 | // maj TODO list
Mon Mar 5 17:32:04 2012 +0100 | bugfix in 1.5
Mon Mar 5 17:02:27 2012 +0100 | set to official channel.xml file
Mon Mar 5 17:01:38 2012 +0100 | bugfix related to advanced mode and saving conf when no conffile exists
Mon Mar 5 00:42:15 2012 +0100 | CHANGELOG
Mon Mar 5 00:37:20 2012 +0100 | bugfix + fix md5 in channel.xml
Sun Mar 4 23:49:39 2012 +0100 | fix
Sun Mar 4 23:49:00 2012 +0100 | bugfix
Sun Mar 4 23:14:03 2012 +0100 | // configuration to choose channel is working
Sat Mar 3 11:43:56 2012 +0100 | // remove unused var
Sat Mar 3 01:33:55 2012 +0100 | by the way, version 0.4 !
Sat Mar 3 01:31:37 2012 +0100 | add todo-list
Fri Mar 2 23:55:03 2012 +0100 | add channel and branch features in Upgrader.php
Fri Mar 2 16:59:05 2012 +0100 | // disable non working yet functiosn
Fri Mar 2 16:49:25 2012 +0100 | interface ready ... next step is saving the configuration and removing version.xml references
Fri Mar 2 00:00:36 2012 +0100 | Advanced mode interface (or kind of ...) :)
Wed Feb 29 19:01:03 2012 +0100 | fix infinite loop in restoreQuery step
Wed Feb 29 17:56:04 2012 +0100 | // fix Damien's weird thing
Wed Feb 29 17:19:19 2012 +0100 | - bug fix if md5 is missing - upgrader.xml replaced by upgrader-1.5.xml
Wed Feb 29 16:44:35 2012 +0100 | cleaning ...
Wed Feb 29 16:08:32 2012 +0100 | dev version bis
Wed Feb 29 16:08:20 2012 +0100 | dev version
Mon Feb 27 18:25:11 2012 +0100 | 1) - display warnings if skipActions is used (this has to be merged in main branch) - error and success handling (display description) - 2 modes created normal/advanced. Now we need to handle configuration
Tue May 22 18:18:33 2012 +0200 | merge + private now uses link and md5
Tue May 22 15:38:58 2012 +0200 | now private release requires link and md5 (and no more private_key)
Mon May 21 21:00:36 2012 +0200 | fix backup/restore steps
Mon May 21 19:54:15 2012 +0200 | minor fix, by dm
Fri May 18 18:03:39 2012 +0200 | tag 0.4.13
Fri May 18 18:02:46 2012 +0200 | add warnings
Fri May 18 14:26:16 2012 +0200 | // maj from dm
Wed May 16 18:08:16 2012 +0200 | upgradeModules bugging ... 7 modules will be upgraded timeout ?
Wed May 16 11:29:10 2012 +0200 | deactivate custom module fix
Wed May 16 09:45:27 2012 +0200 | maj svn rev number oO
Tue May 15 20:01:55 2012 +0200 | more accurate description for PS_AUTOUP_KEEP_TRAD
Tue May 15 19:56:39 2012 +0200 | set to 0.4.13, and new feature mergeTranslation done git diff --cached autoupgrade.php
Tue May 15 11:45:19 2012 +0200 | dm modifs
Tue May 15 10:01:29 2012 +0200 | now test_dir skip img/p dir
Tue May 15 10:00:26 2012 +0200 | now configure button exists and redirect to autoupgrade page (by dMetzger)
Tue May 15 09:59:07 2012 +0200 | now autoupgrade is excluded from "checkModifiedFiles"
Mon May 14 19:41:39 2012 +0200 | 0.4.12 !
Mon May 14 19:38:03 2012 +0200 | design + trad
Mon May 14 19:13:10 2012 +0200 | fix expressions, + lang addslashes in js
Mon May 14 18:49:19 2012 +0200 | translations fix
Mon May 14 17:19:07 2012 +0200 | DEFINER=CURRENT_USER (instead of removing it)
Mon May 14 16:20:57 2012 +0200 | tag 0.4.11
Mon May 14 15:53:06 2012 +0200 | shop deactivated display
Mon May 14 11:48:00 2012 +0200 | now all .zip are allowed in download dir (for archive channel)
Mon May 14 11:32:31 2012 +0200 | upgrading db classes ...
Mon May 14 11:27:05 2012 +0200 | 0.4.11-dev
Mon May 14 11:21:37 2012 +0200 | additionnal check : cache MUST be deactivated
Fri May 11 19:15:36 2012 +0200 | tag 0.4.10
Fri May 11 18:40:21 2012 +0200 | fix Cache used
Fri May 11 17:56:36 2012 +0200 | fix big tables drop all tables
Fri May 11 17:12:03 2012 +0200 | prepare 0.4.9
Fri May 11 17:11:13 2012 +0200 | executeS now deactivate the cache
Fri May 11 15:32:21 2012 +0200 | now restore has a separate list of files to skip
Fri May 11 14:38:38 2012 +0200 | revert x > w for fopen, bzopen + fix THEME_NAME missing in 1.5 (no more retrocompatibility -_-)
Fri May 11 12:47:58 2012 +0200 | prepare to 0.4.8
Fri May 11 12:47:30 2012 +0200 | new way to handle keepTrad / keepMails
Fri May 11 12:17:51 2012 +0200 | new static max_written_allowed, config load fix, some expresssions more accurates
Wed May 9 17:25:28 2012 +0200 | removed Tools::file_exists_cache, does not exists in 1.2.5
Mon May 7 17:35:06 2012 +0200 | traductions, PNM-154
Mon May 7 16:02:23 2012 +0200 | translation workaround
Mon May 7 12:10:02 2012 +0200 | On advanced_mode: workaround related to translations
Mon May 7 12:10:02 2012 +0200 | index on advanced_mode: b7f0ba2 bugfix in ConfigurationTest test_dir is writable
Thu May 3 18:06:10 2012 +0200 | bugfix in ConfigurationTest test_dir is writable
Thu May 3 17:53:29 2012 +0200 | fix check root status
Thu May 3 17:02:43 2012 +0200 | preparation for 0.4.7 + translations fr
Thu May 3 16:24:07 2012 +0200 | fixed directory created during/after upgrade was not removed when restore
Thu May 3 12:42:10 2012 +0200 | add new checkbox to allow (or not) major upgrade in private channel
Wed May 2 17:31:41 2012 +0200 | set to 0.4.6
Wed May 2 16:12:46 2012 +0200 | - images are now saved - removed fake link for option fieldset
Mon Apr 30 18:56:59 2012 +0200 | maj for tag
Mon Apr 30 18:49:15 2012 +0200 | config is now correctly displayed
Thu Apr 19 19:04:21 2012 +0200 | fix cacheFs end of upgrade bug
Thu Apr 19 18:51:24 2012 +0200 | colors in console are now more subtiles
Thu Apr 19 18:50:32 2012 +0200 | deleteDirectory is now a method inside the class
Thu Apr 19 18:49:28 2012 +0200 | config advanced small fix + design
Thu Apr 19 18:46:24 2012 +0200 | change variable name for keepImage
Thu Apr 19 14:41:18 2012 +0200 | now refreshing the page use Tools::redirectAdmin()
Thu Apr 19 14:40:22 2012 +0200 | Warning "please update your configuration" now correctly displayed
Thu Apr 19 12:27:51 2012 +0200 | use _PS_ROOT_DIR_ for db() method
Thu Apr 19 12:18:33 2012 +0200 | commit only getConfig modification
Thu Apr 19 12:13:44 2012 +0200 | fix date_timezone warning
Thu Apr 19 11:18:01 2012 +0200 | [-] MO : autoupgrade now admin/autoupgrade is cleared on uninstallation
Wed Apr 18 19:45:30 2012 +0200 | for tag 0.4.4
Wed Apr 18 19:38:29 2012 +0200 | listFilesInDir bugfix simplification of displayForm bugfix option status is now correctly displayed
Wed Apr 18 19:05:41 2012 +0200 | [-] can now delete backup from back-office ( PNM-167 )
Tue Apr 17 19:52:27 2012 +0200 | change version to 0.4.3
Tue Apr 17 19:44:19 2012 +0200 | Now directory to remove for restoreProcess is listed (by listFilesToRemove )
Tue Apr 17 18:52:37 2012 +0200 | [*] now restoreFiles delete new files [-] Fix notice information abs/rel path
Tue Apr 17 18:31:49 2012 +0200 | now we can choose archive_file channel with a custom filename for zip
Mon Apr 16 10:41:53 2012 +0200 | Merge branch 'advanced_mode' of marinetti.fr:autoupgrade into advanced_mode
Thu Apr 12 15:10:51 2012 +0200 | change version number to 0.4.3-dev
Thu Apr 12 15:00:37 2012 +0200 | [-] MO : autoupgrade - prevent usage of undefined constants related to Cache
Thu Apr 12 10:27:10 2012 +0200 | remove deprecated files
Thu Apr 12 09:50:47 2012 +0200 | // remove deprecated files
Wed Apr 11 15:48:37 2012 +0200 | // set version to 0.4.2
Wed Apr 11 11:18:58 2012 +0200 | gitignore
Tue Apr 10 20:08:41 2012 +0200 |  remove svn for dev unstable of the doom
Fri Apr 6 17:04:29 2012 +0200 | merge from master into advanced_mode + norms
Tue Apr 10 12:05:47 2012 +0200 | Merge branch 'master' of github.com:Asenar/Module-PrestaShop-autoupgrade
Tue Apr 10 12:02:53 2012 +0200 | // forgot something
Thu Apr 5 12:17:19 2012 +0200 | forgot to update config.xml ...
Thu Apr 5 11:26:45 2012 +0200 | fixed bug in upgradeThisFile / skipFiles methods excludeAbsoluteFilesFromUpgrade is now correctly working
Tue Apr 3 16:55:30 2012 +0200 | bugfix, +2012 config.xml updates
Wed Apr 4 11:44:32 2012 +0200 | you can now delete previous backup
Tue Apr 3 18:07:06 2012 +0200 | // bugfix AdminSelfUpgrade final restore step
Tue Apr 3 16:56:50 2012 +0200 | config.xml updates
Tue Apr 3 16:55:30 2012 +0200 | bugfix, +2012
Thu Mar 22 15:57:20 2012 +0100 | maj 2011 2012
Tue Mar 13 17:42:46 2012 +0100 | 2012
Sat Mar 3 01:03:20 2012 +0100 | add CHANGELOG
Wed Feb 29 19:01:03 2012 +0100 | fix infinite loop in restoreQuery step
Sun Feb 26 14:23:30 2012 +0100 | Update README.md
Sun Feb 26 14:16:52 2012 +0100 | updates README (make it more clear) (+ 3 in 1)
Tue Apr 10 10:45:00 2012 +0200 | Merge branch 'rescue' of github.com:Asenar/Module-PrestaShop-autoupgrade into rescue
Tue Apr 10 10:43:44 2012 +0200 | merge master into rescue :)
Fri Apr 6 17:04:29 2012 +0200 | // merge from master into advanced_mode
Thu Apr 5 12:17:19 2012 +0200 | forgot to update config.xml ...
Thu Apr 5 11:26:45 2012 +0200 | fixed bug in upgradeThisFile / skipFiles methods excludeAbsoluteFilesFromUpgrade is now correctly working
Wed Apr 4 16:47:17 2012 +0200 | Merge branch 'tmp_branch' (restoring #%µ$££¤ previously made) 	- you can now delete previous backup 	- bugfix AdminSelfUpgrade final restore step 	- bugfix 1,23s/2011/2012 (from svn)
Wed Apr 4 11:44:32 2012 +0200 | you can now delete previous backup
Tue Apr 3 18:07:06 2012 +0200 | // bugfix AdminSelfUpgrade final restore step
Tue Apr 3 16:55:30 2012 +0200 | bugfix, +2012 config.xml updates
Wed Apr 4 15:16:30 2012 +0200 | Merge branch 'bugfix0.3.1'
Wed Apr 4 15:05:15 2012 +0200 | Merge branch 'bugfix0.3.1' of git@marinetti.fr:autoupgrade into bugfix0.3.1
Thu Mar 22 15:57:20 2012 +0100 | maj 2011 2012
Wed Apr 4 11:44:32 2012 +0200 | you can now delete previous backup
Tue Apr 3 18:07:06 2012 +0200 | // bugfix AdminSelfUpgrade final restore step
Tue Apr 3 16:56:50 2012 +0200 | config.xml updates
Tue Apr 3 16:55:30 2012 +0200 | bugfix, +2012
Fri Mar 30 17:14:03 2012 +0200 | rollback next_params/nextParams, TODO remove directories
Fri Mar 30 16:33:11 2012 +0200 | tmp, norms
Fri Mar 30 16:32:42 2012 +0200 | norms
Fri Mar 30 14:55:12 2012 +0200 | norms
Fri Mar 30 14:33:00 2012 +0200 | norms
Fri Mar 30 12:51:34 2012 +0200 | silly fix debug forgotten
Fri Mar 30 12:04:25 2012 +0200 | fix INSTALL_PATH _PS_INSTALL_PATH_
Fri Mar 30 11:38:15 2012 +0200 | // no dev, use public
Fri Mar 30 11:35:52 2012 +0200 | // minor fixes, add private_exclude_major
Fri Mar 30 11:33:52 2012 +0200 | minor fix from 1.4 class ...
Wed Mar 28 17:13:04 2012 +0200 | stop at first match on channel.xml
Thu Mar 22 15:57:20 2012 +0100 | maj 2011 2012
Tue Mar 13 17:42:46 2012 +0100 | 2012
Tue Mar 13 15:00:30 2012 +0100 | mod todo
Tue Mar 13 15:00:08 2012 +0100 | expressions
Mon Mar 12 18:02:32 2012 +0100 | config.xml and channel.xml updated
Mon Mar 12 17:59:40 2012 +0100 | better algorith for choosing the correct version to follow related to branch / channel hierarchy
Mon Mar 12 16:28:04 2012 +0100 | cleanning code
Mon Mar 12 16:27:24 2012 +0100 | trads
Mon Mar 12 16:03:04 2012 +0100 | tab parent is now default to AdminModules
Fri Mar 9 16:45:16 2012 +0100 | correct xml
Fri Mar 9 16:40:42 2012 +0100 | channel.xml improvements
Fri Mar 9 16:40:25 2012 +0100 | config.xml
Fri Mar 9 16:39:57 2012 +0100 | refresh system for Upgrader
Fri Mar 9 16:39:06 2012 +0100 | this->getConfig replaces Configuration::get, ergonomy expert mode
Fri Mar 9 16:36:54 2012 +0100 | fix 1.5 PS_ADMIN undefined in AdminSelfTab
Wed Mar 7 18:29:38 2012 +0100 | All bug fixed: restoreProcess delete all files (but not directories .. yet) all channel (including directory and archive) works upgrade with warning in upgradeDb does do restoration if an error happen (even fatal error), restoration is called automatically next version available in branch (1.4/1.5) is handled
Mon Mar 5 17:34:38 2012 +0100 | // maj TODO list
Mon Mar 5 17:32:04 2012 +0100 | bugfix in 1.5
Mon Mar 5 17:02:27 2012 +0100 | set to official channel.xml file
Mon Mar 5 17:01:38 2012 +0100 | bugfix related to advanced mode and saving conf when no conffile exists
Mon Mar 5 10:33:03 2012 +0100 | resolved conflict :) Merge remote-tracking branch 'marinetti/advanced_mode' into advanced_mode
Mon Mar 5 00:42:15 2012 +0100 | CHANGELOG
Mon Mar 5 00:37:20 2012 +0100 | bugfix + fix md5 in channel.xml
Sun Mar 4 23:49:39 2012 +0100 | fix
Sun Mar 4 23:49:00 2012 +0100 | bugfix
Sun Mar 4 23:14:03 2012 +0100 | // configuration to choose channel is working
Sat Mar 3 11:43:56 2012 +0100 | // remove unused var
Sat Mar 3 01:33:55 2012 +0100 | by the way, version 0.4 !
Sat Mar 3 01:31:37 2012 +0100 | add todo-list
Sat Mar 3 01:03:20 2012 +0100 | add CHANGELOG
Sat Mar 3 01:03:20 2012 +0100 | add CHANGELOG
Fri Mar 2 23:55:03 2012 +0100 | add channel and branch features in Upgrader.php
Fri Mar 2 16:59:05 2012 +0100 | // disable non working yet functiosn
Fri Mar 2 16:49:25 2012 +0100 | interface ready ... next step is saving the configuration and removing version.xml references
Fri Mar 2 00:00:36 2012 +0100 | Advanced mode interface (or kind of ...) :)
Thu Mar 1 22:32:05 2012 +0100 | test
Wed Feb 29 19:01:03 2012 +0100 | fix infinite loop in restoreQuery step
Wed Feb 29 19:01:03 2012 +0100 | fix infinite loop in restoreQuery step
Wed Feb 29 18:20:28 2012 +0100 | Merge branch 'master' of marinetti.fr:autoupgrade
Wed Feb 29 17:56:04 2012 +0100 | // fix Damien's weird thing
Wed Feb 29 17:56:04 2012 +0100 | // fix Damien's weird thing
Wed Feb 29 17:19:19 2012 +0100 | - bug fix if md5 is missing - upgrader.xml replaced by upgrader-1.5.xml
Wed Feb 29 17:19:19 2012 +0100 | - bug fix if md5 is missing - upgrader.xml replaced by upgrader-1.5.xml
Wed Feb 29 16:44:35 2012 +0100 | cleaning ...
Wed Feb 29 16:08:32 2012 +0100 | dev version bis
Wed Feb 29 16:08:20 2012 +0100 | dev version
Mon Feb 27 18:25:11 2012 +0100 | 1) - display warnings if skipActions is used (this has to be merged in main branch) - error and success handling (display description) - 2 modes created normal/advanced. Now we need to handle configuration
Sun Feb 26 16:40:06 2012 +0100 | (test)
Sun Feb 26 15:58:08 2012 +0100 | Merge branch 'rescue' of github.com:Asenar/Module-PrestaShop-autoupgrade into rescue
Sun Feb 26 14:23:30 2012 +0100 | Update README.md
Sun Feb 26 14:23:17 2012 +0100 | Update README.md
Sun Feb 26 14:19:06 2012 +0100 | Update README.md
Sun Feb 26 14:16:52 2012 +0100 | updates README (make it more clear)
Wed Feb 22 10:57:10 2012 +0100 | fixed notice in autoupgrade install + check if deleteDirectory function exists
Fri Feb 17 16:58:04 2012 +0100 | // fix some errors in Upgrader.php if xml are unavailables
Fri Feb 17 16:57:22 2012 +0100 | // added some errors messages, just in case
Fri Feb 17 13:42:33 2012 +0100 | // fix backup db process
Fri Feb 17 11:37:56 2012 +0100 | much better backupdb and restoredb system \!\!
Wed Feb 15 19:17:41 2012 +0100 | fix restoration, ps_connections, ps_statssearch, and others stats table ignored
Wed Feb 15 18:59:22 2012 +0100 | improved rollback process + fix
Wed Feb 15 18:45:21 2012 +0100 | force ajax request in ajax-upgradetab.php
Wed Feb 15 17:52:18 2012 +0100 | // fix cookie path
Wed Feb 15 15:49:43 2012 +0100 | // cleaning, + fix thanks to Olivier Le Corre
Fri Feb 10 17:19:55 2012 +0100 | backup is not used anymore as class
Fri Feb 10 17:09:01 2012 +0100 | // unselect backup after rollback click
Fri Feb 10 16:54:26 2012 +0100 | fix a lot, related to restoration
Thu Feb 9 19:31:29 2012 +0100 | // add comments, clean code
Thu Feb 9 19:21:22 2012 +0100 | comments
Thu Feb 9 19:18:58 2012 +0100 | delete directory autoupgrade
Thu Feb 9 19:18:35 2012 +0100 | // restoreFiles improved, refresh now clears config/xml/versions.xml
Thu Feb 9 17:28:50 2012 +0100 | fix autoupgrade installation module + mkdir
Wed Feb 8 18:28:29 2012 +0100 | // clean code related to diff between versions code
Wed Feb 8 18:26:10 2012 +0100 | // fix bug in listFilesToRestore // now extra files & dir are removed before restoration // cleanning code
Tue Feb 7 20:05:43 2012 +0100 | Merge branch 'master' of github.com:Asenar/Module-PrestaShop-autoupgrade
Tue Feb 7 20:05:28 2012 +0100 | during module installation, create config/xml directory if not exists
Tue Feb 7 20:04:57 2012 +0100 | added methods to compare 2 versions of prestashop + backup improved (now backups also views)
Tue Feb 7 20:04:19 2012 +0100 | added methods to compare 2 versions of prestashop
Mon Feb 6 14:01:07 2012 +0100 | Update README.md
Mon Feb 6 13:56:44 2012 +0100 | Update README.md
Mon Feb 6 13:55:10 2012 +0100 | comments on skipActions
Fri Feb 3 14:20:48 2012 +0100 | more retrocompatibility fix (1.5)
Fri Feb 3 14:18:37 2012 +0100 | removed dead code
Thu Feb 2 18:53:05 2012 +0100 | update config.xml
Thu Feb 2 18:52:42 2012 +0100 | added error message in module installation for a better support
Thu Feb 2 18:25:24 2012 +0100 | // now you can delete old backup. @TODO : it's currently available to select 1 backup for files and 1 other for db (made at an other time). Should that stay like this ?
Thu Feb 2 17:26:30 2012 +0100 | // cleaning code
Wed Feb 1 19:29:25 2012 +0100 | bug fix about backup, some improvements in skipFiles
Wed Feb 1 16:34:23 2012 +0100 | fix
Wed Feb 1 15:59:49 2012 +0100 | lot of code cleaning / optimization, restoration new system
Wed Feb 1 11:38:42 2012 +0100 | fix restoration
Wed Feb 1 11:34:55 2012 +0100 | added global var ajax for ajax mode detected in AdminSelfUpgrade constructor
Tue Jan 31 18:31:17 2012 +0100 | Merge branch 'master' of github.com:Asenar/Module-PrestaShop-autoupgrade
Tue Jan 31 18:30:58 2012 +0100 | add enable / disable rollback button
Tue Jan 31 17:26:18 2012 +0100 | add info on die for wrong dir name
Tue Jan 31 17:24:52 2012 +0100 | fix backup
Tue Jan 31 17:24:31 2012 +0100 | fix backup
Tue Jan 31 10:03:36 2012 +0100 | format readme
Mon Jan 30 18:45:32 2012 +0100 | // maj filenames for backup, added TODO for restore button
Mon Jan 30 15:29:07 2012 +0100 | fix AdminSelfTab requirement
Mon Jan 30 15:08:44 2012 +0100 | // cleaning code
Mon Jan 30 15:06:11 2012 +0100 | cleaning code
Mon Jan 30 14:34:35 2012 +0100 | s/can't/cannot
Fri Jan 27 15:03:36 2012 +0100 | fix restoreDb, todo test restoreFiles
Fri Jan 27 14:44:19 2012 +0100 | db/Db.php
Fri Jan 27 10:53:40 2012 +0100 | now Smarty v2 usage display a warning
Wed Jan 25 21:36:50 2012 +0100 | fix smarty 3 usage
Wed Jan 25 21:13:08 2012 +0100 | +AddConfToFile is now in module dir, No more skipAction
Wed Jan 25 20:57:09 2012 +0100 | // last fix
Wed Jan 25 20:30:50 2012 +0100 | fix new feature + message copied / preserved / directory created
Wed Jan 25 20:11:19 2012 +0100 | // bug fix
Wed Jan 25 20:02:29 2012 +0100 | Upgrade complete !!! + new feature "Keep default mails / keep default translations improved + ergonomy + display detailled upgradeDb informations
Tue Jan 24 18:45:12 2012 +0100 | upgrade database classes, + path and minor bug in AdminSelfUpgrade
Tue Jan 24 18:15:15 2012 +0100 | removed alert yo
Tue Jan 24 17:14:33 2012 +0100 | // improvements
Fri Jan 20 14:28:30 2012 +0100 | // maj, upgradeDb complete with cacheFs
Wed Jan 18 09:59:33 2012 +0100 | removed undefined constant (MAGIC_QUOTES)
Tue Jan 17 10:27:03 2012 +0100 | update Backup.php
Mon Jan 16 14:25:41 2012 +0100 | upgradeDb process improved (still deleteCacheFS missing )
Mon Jan 16 11:14:08 2012 +0100 | fix Db.php
Fri Jan 13 18:07:25 2012 +0100 | improvements (sorry for the non pertinence of the changelog), I will continue at home this week end
Fri Jan 13 10:23:29 2012 +0100 | Merge branch 'master' of github.com:Asenar/Module-PrestaShop-autoupgrade
Fri Jan 13 10:22:36 2012 +0100 | Update README.md
Fri Jan 13 10:21:54 2012 +0100 | Update README.md
Thu Jan 12 15:59:34 2012 +0100 | // go to the line for each quickInfo
Thu Jan 12 15:55:02 2012 +0100 | // fix
Thu Jan 12 15:44:14 2012 +0100 | set version to 0.3 (major improvements) handle old/new upgrade sql files directory goes back to test 1.4 upgrade / preparation for upgrade channel
Thu Jan 12 10:56:29 2012 +0100 | // improvement : we now have the filename version related to the update query
Thu Jan 12 00:41:24 2012 +0100 | rename to md to see some magic
Wed Jan 11 23:02:20 2012 +0100 | HOWTO in readme
Wed Jan 11 22:56:40 2012 +0100 | add better error message
Wed Jan 11 19:43:53 2012 +0100 | warn message moved
Wed Jan 11 19:08:42 2012 +0100 | no error in module (but error in sql files todo : be really ObjectModel free in phpfiles used from 1.3.x
Tue Jan 10 18:22:24 2012 +0100 | Fix parse
Tue Jan 10 18:18:10 2012 +0100 | Final corrections about upgradeDb
Tue Jan 10 18:17:14 2012 +0100 | use upgrader-1.5.xml
Tue Jan 10 17:04:13 2012 +0100 | removing last references to PS_ROOT_DIR and such
Tue Jan 10 16:21:57 2012 +0100 | jbrx tips
Tue Jan 10 11:33:19 2012 +0100 | // others tips, merci jbrx
Tue Jan 10 11:26:30 2012 +0100 | // some git tips
Tue Jan 10 11:08:27 2012 +0100 | // restoration of the AdminSelfUpgrade.php lost in the git darkness process AdminSelfUpgrade upgrade process is now ObjectModel free
Tue Jan 10 11:02:43 2012 +0100 | test
Mon Jan 9 16:09:31 2012 +0100 | commit temporaire
Fri Jan 6 17:30:48 2012 +0100 | maj après modification des fichiers d'upgrade (svn rev 12241 )
Thu Jan 5 21:47:40 2012 +0100 | on va tout faire en grand
Thu Jan 5 19:35:34 2012 +0100 | Merge branch 'master' of github.com:Asenar/Module-PrestaShop-autoupgrade
Thu Jan 5 19:33:19 2012 +0100 | voir precedent message de log
Thu Jan 5 19:31:43 2012 +0100 | removed a lot of functionnality in order to work really as 'standalone' see where 'error()' are located to add the missing features
Wed Dec 21 11:42:46 2011 +0100 | test
Wed Dec 21 10:55:26 2011 +0100 | add autoupgrade module, version 0.2.2 (Prestashop 1.4.6.2)
Wed Dec 21 10:52:15 2011 +0100 | first commit