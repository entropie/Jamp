<?
// $Id: en.php,v 1.23 2004/02/23 16:11:03 entropie Exp $ //
$s = 'en';

#
#                [VARNAME]					              'VALUE_OF'										          #arguments in order
#

$cfg['lang'][$s] ['title']                      = 'Jamp &mdash; %1'; #none
$cfg['lang'][$s] ['titleBrowser']               = 'Browser'; #none
$cfg['lang'][$s] ['titleHelp']                  = 'Help11'; #none
$cfg['lang'][$s] ['titleSearch']                = 'Search: %1'; #searchstring
$cfg['lang'][$s] ['titlePlaylist']              = 'Playlist: %1'; #searchstring
$cfg['lang'][$s] ['titleAdmin']                 = 'Admin'; #none
$cfg['lang'][$s] ['wrongAuth']                  = 'Login was not successful.';            #none
$cfg['lang'][$s] ['folderNotExist']             = 'The folder "%1" does not exist.';      #foldername
$cfg['lang'][$s] ['mainError']                  = 'A main error has occured. Dunno what is todo.';	#none
$cfg['lang'][$s] ['nothingHere']                = 'The folder is Empty.';							    #none
$cfg['lang'][$s] ['nothingHereTitle']           = 'empty.';											#none
$cfg['lang'][$s] ['noSearchResult']             = 'No search results for \'<b>%1</b>\'.';			#searstring
$cfg['lang'][$s] ['dieError']                   = 'died!';											#none
$cfg['lang'][$s] ['nothingHere']                = 'The Database is currently empty.';				#none
$cfg['lang'][$s] ['countAllStr']                = 'Currently we have <b>%2</b> music files in <b>%1</b> folders.';
$cfg['lang'][$s] ['loginLoginTitle']            = 'Login';
$cfg['lang'][$s] ['loginLoginTopic']            = 'Login';
$cfg['lang'][$s] ['loginLoginString']           = 'String';
$cfg['lang'][$s] ['logSongLogFormat']           = date('d/m/Y-H:i') . ' *** %2 *** %1';			#songame,IP
$cfg['lang'][$s] ['logFileLogStringSmall']      = date('d/m/Y-H:i') . ' *** %1 *** %2';			#IP,string
$cfg['lang'][$s] ['logFileLogString']           = '%1';												#logstring
$cfg['lang'][$s] ['logDevLogString']            = date('H:i:s')     . '--%1';						#logstring
$cfg['lang'][$s] ['logCachedContent']           = 'PERF:     Get content from cached file[%1].'; 	#pid
$cfg['lang'][$s] ['logMysqlContent']            = 'PERF:     Get content from MySQL[%1].';			#pid
$cfg['lang'][$s] ['pageProccessing']            = 'PERF:     Last Page processing[%1] in %2.';		#pid
$cfg['lang'][$s] ['logCookieDefined']           = 'COOKIE:   Cookie already defined [%1]';			#cookie_id
$cfg['lang'][$s] ['logCookieNew']               = 'COOKIE:   Creating new cookie [%1]';				#cookie_id
$cfg['lang'][$s] ['logCanFileNotOpen']          = 'ERROR:    Can file [%1] not open; exit!';		#filename
$cfg['lang'][$s] ['logTemplateNotFound']        = 'ERROR:    Template %1 not found; exit!';			#filename
$cfg['lang'][$s] ['logDatabaseEmpty']           = 'SYSTEM:   Type [COMMAND_STRING]updatedb in the command line.';#none
$cfg['lang'][$s] ['logDeleted']                 = 'SYSTEM:   Log "%1" deleted.';					#logfile
$cfg['lang'][$s] ['logSearchQuery']             = 'SEARCH:   %1 - "%2[%3]" in %4.';			#username,searchstring,extsearchstring,time
$cfg['lang'][$s] ['loginSuccessful']            = 'SECURITY: Login successful for %1@%2.';			#username,userlevel
$cfg['lang'][$s] ['loginNotSuccessful']         = 'SECURITY: Login NOT successful[%1].';			#username
$cfg['lang'][$s] ['logSbEntry']                 = 'SHOUTB:   Entry written by %1.';					#username
$cfg['lang'][$s] ['logSbDelEntry']              = 'SHOUTB:   Entry[%1] erased by %2.';				#ID,username
$cfg['lang'][$s] ['udbLogging']                 = '%1@%2';											#username,IP
$cfg['lang'][$s] ['fileIsNotPlayable']          = 'UDB: !!! File is not playable - %1.';			#filename
$cfg['lang'][$s] ['fileIsPlayable']             = 'UDB: File is playable - %1';						#filename
$cfg['lang'][$s] ['udbTime']                    = 'UDB:      UpdateDb in %1.';						#time
$cfg['lang'][$s] ['udbFolder']                  = 'UDB:      Initialisation: %1.';					#folder
$cfg['lang'][$s] ['udbDbDown']                  = 'UDB:      Databases deleted.';					#none
$cfg['lang'][$s] ['udbSymlinkDel']              = 'UDB:      Symlinkfolder deleted.';				#none
$cfg['lang'][$s] ['udbSymlinkCreateFolder']     = 'UDB:      Symlinkfolder created.';				#none
$cfg['lang'][$s] ['udbSymlinkCreate']           = 'UDB:      Creating Symlinks.';					#none
$cfg['lang'][$s] ['udbSymlinkCreateDone']       = 'UDB:      Done with creating Symlinks.';			#none
$cfg['lang'][$s] ['udbCleardbDenied']           = 'SECURITY: Updatedb denied for %1.';				#user
$cfg['lang'][$s] ['sbLongLinkDesc']             = 'Read entire Shout!'; #none

$cfg['lang'][$s] ['mailBody']                   = "-- Critical error\n\n- Error Message:\n %1"; #errorMessage
$cfg['lang'][$s] ['mailSubject']                = 'Jamp - error'; #none

$cfg['lang'][$s] ['STR_playentirefolder']       = 'Play entire folder.'; #none
$cfg['lang'][$s] ['STR_enqueueentirefolder']    = 'Enqueue entire folder.'; #none
$cfg['lang'][$s] ['STR_xmldump']                = 'XML dumping.'; #none
$cfg['lang'][$s] ['STR_submitform']             = 'Submit form.'; #none
$cfg['lang'][$s] ['STR_hplink']                 = 'Homepage'; #none
$cfg['lang'][$s] ['STR_checkalldirs']           = 'Check'; #none
$cfg['lang'][$s] ['STR_checkallfiles']          = 'Check'; #none
$cfg['lang'][$s] ['STR_uncheckalldirs']         = 'uncheck'; #none
$cfg['lang'][$s] ['STR_uncheckallfiles']        = 'uncheck'; #none
$cfg['lang'][$s] ['STR_uncheckendfiles']        = 'all Files'; #none
$cfg['lang'][$s] ['STR_uncheckenddirs']         = 'all Dirs'; #none


$cfg['lang'][$s] ['STR_pllink']                 = 'Playlists'; #none
$cfg['lang'][$s] ['STR_logoutlink']             = 'Logout'; #none
$cfg['lang'][$s] ['STR_adminlink']              = 'Administration'; #none
$cfg['lang'][$s] ['STR_randlink']               = 'Random song'; #none
$cfg['lang'][$s] ['STR_helplink']               = 'Help'; #none
$cfg['lang'][$s] ['STR_plaction']               = '* Action'; #none
$cfg['lang'][$s] ['STR_plplayall']              = '&nbsp; Play all'; #none
$cfg['lang'][$s] ['STR_plplayselected']         = '&nbsp; Play selected'; #none
$cfg['lang'][$s] ['STR_plrmsel']                = '&nbsp; Remove selected'; #none
$cfg['lang'][$s] ['STR_plsave']                 = '&nbsp; Save playlist (name: textfield)'; #none
$cfg['lang'][$s] ['STR_plnew']                  = '&nbsp; New playlist (erase)'; #none
$cfg['lang'][$s] ['STR_plselectlink']           = 'Select '; #none
$cfg['lang'][$s] ['STR_pldeselectlink']         = ' Deselect'; #none
$cfg['lang'][$s] ['STR_plformlabel']            = 'GO'; #none
$cfg['lang'][$s] ['STR_loginuser']              = 'Username'; #none
$cfg['lang'][$s] ['STR_loginpw']                = 'Password'; #none
$cfg['lang'][$s] ['STR_loginsubmit']            = 'Login'; #none
$cfg['lang'][$s] ['STR_mpldelone']              = 'Del selected'; #none
$cfg['lang'][$s] ['STR_mpldelall']              = 'Erase playlist'; #none
$cfg['lang'][$s] ['STR_mplnothing']             = 'Do nothing'; #none
$cfg['lang'][$s] ['STR_mplmp3blaster']          = 'Mp3Blaster'; #none
$cfg['lang'][$s] ['STR_mplplay']                = 'Play entire list'; #none
$cfg['lang'][$s] ['STR_sbsubmit']               = 'Shout!'; #none
$cfg['lang'][$s] ['STR_sbtitle']                = 'S h o u t b o x'; #none
$cfg['lang'][$s] ['STR_maintabletitle_sb']      = 'F u l l &nbsp; &nbsp; S h o u t b o x'; #none
$cfg['lang'][$s] ['STR_maintabletitle_browser'] = 'B r o w s e r'; #none
$cfg['lang'][$s] ['STR_maintabletitle_help']    = 'S e a r c h: %1'; #searchstring
$cfg['lang'][$s] ['STR_maintabletitle_pl']      = 'P l a y l i s t: %1'; #playlist
$cfg['lang'][$s] ['STR_logtopic']               = 'L o g f i l e'; #none

$cfg['lang'][$s] ['botline']                    = 'Rendertime about %1.';
$cfg['lang'][$s] ['botlineStats']               = '%1';


$cfg['lang'][$s] ['noLoginHead']                = '404 Forbidden'; #none
$cfg['lang'][$s] ['noLoginBody']                = 'You\'re not allowed to visit this site without a valid login.'; #none

$cfg['lang'][$s] ['STR_directory']              = 'directory';
$cfg['lang'][$s] ['STR_directories']            = 'directories';
$cfg['lang'][$s] ['STR_file']                   = 'file';
$cfg['lang'][$s] ['STR_files']                  = 'files';



?>
