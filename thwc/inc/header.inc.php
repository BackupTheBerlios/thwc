<?php
/* $Id: header.inc.php,v 1.3 2003/06/13 11:47:54 master_mario Exp $ */
 /*
          ThWClone - PHP/MySQL Bulletin Board System
        ==============================================
          (c) 2003 by
           Mario Pischel         <mario@aqzone.de>

          download the latest version:
          https://developer.berlios.de/projects/thwc/

          This  program is  free  software;  you can
          redistribute it and/or modify it under the
          terms of the GNU General Public License as
          published by the Free Software Foundation;
          either  version 2 of  the License,  or (at
          your option) any later version.

        ==============================================
 */
 error_reporting(E_ALL);
 
 if( isset($HTTP_GET_VARS) ) extract($HTTP_GET_VARS);
 if( isset($HTTP_PUT_VARS) ) extract($HTTP_PUT_VARS);
 if( isset($HTTP_POST_VARS) ) extract($HTTP_POST_VARS);
		
 include ( 'inc/config.inc.php' );
 include ( 'inc/functions.inc.php' );
 /* DB_connect */
 $mysql = @mysql_connect($m_host, $m_user, $m_pw);
 $db    = @mysql_select_db($m_db);
 $m_host = ''; $m_user = ''; $m_pw = ''; $m_db = '';
 if( !$mysql || !$db )
 {
     print '<b>Sorry</b><br><br>Es gibt momentan leider ein kleines Datenbank-Problem,
     bitte versuche es sp&#xE4;ter noch einmal.';
     exit;
 }
 
 //  leere Variablen definieren
 
 
 //  read registry
 $r_registry = db_query("SELECT
     keyname, keyvalue
 FROM ".$pref."registry");
 while( $a_registry = db_result( $r_registry ) )
 {
     $config[$a_registry['keyname']] = $a_registry['keyvalue'];
 }
 mysql_free_result( $r_registry );
 unset( $a_registry );
 
 // Systemzeit
 $board_time = time()+$config['diff_board_time'];
 
 
 // session_start() and usercheck
 session_start(); 
 $sid = session_id();
 	 
 $r_user = db_query("SELECT
     user_id,
	 user_name,
	 user_ismod,
	 user_isadmin,
	 groupids,
	 is_uradmin,
	 user_styleid
 FROM ".$pref."user WHERE user_session='$sid'");
 if( db_rows( $r_user ) == 1 )
 {
     $a_user = db_result( $r_user );
     define( "U_ID", $a_user['user_id'] );
     define( "U_NAME", $a_user['user_name'] );
     define( "U_ISMOD", $a_user['user_ismod'] );
     define( "U_ISADMIN", $a_user['user_isadmin'] );
     define( "U_ISURADMIN", $a_user['is_uradmin'] );
     define( "U_GROUPIDS", $a_user['groupids'] );
     define( "U_STYLEID", $a_user['user_styleid'] );
	 $data['login'] = '';
 }
 else
 {
     define( "U_ID", '0' );
     define( "U_NAME", 'Gast' );
     define( "U_ISMOD", '0' );
     define( "U_ISADMIN", '0' );
     define( "U_ISURADMIN", '0' );
     define( "U_GROUPIDS", ','.$config['guest_groupid'].',' );
     define( "U_STYLEID", '0' );
 }
 
 
 // _groups lesen und Rechtestring erstellen
 if( isset( $boardid ) )
 {
    boardPermissions ( U_GROUPIDS, $boardid );
 	define('P_VIEW', $P[30]);
 	define('P_REPLY', $P[29]);
 	define('P_POSTNEW', $P[28]);
 	define('P_CLOSE', $P[27]);
 	define('P_DELTHREAD', $P[26]);
 	define('P_OMOVE', $P[25]);
 	define('P_DELPOST', $P[24]);
 	define('P_EDIT', $P[23]);
 	define('P_OCLOSE', $P[22]);
 	define('P_ODELTHREAD', $P[21]);
 	define('P_ODELPOST', $P[20]);
 	define('P_OEDIT', $P[19]);
 	define('P_TOP', $P[18]);
 	define('P_EDITCLOSED', $P[17]);
 	define('P_IP', $P[16]);
 	define('P_EDITTOPIC', $P[15]);
 	define('P_NOFLOODPROT', $P[14]);
 	define('P_NOEDITLIMIT', $P[13]);
 	define('P_CANSEEINVIS', $P[12]);
 	define('P_NOPMLIMIT', $P[11]);
 	define('P_INTEAM', $P[10]);
 	define('P_CEVENT', $P[9]);
 	define('P_SHOWDELETED', $P[8]);
 	define('P_POLLNEW', $P[7]);
 	define('P_DELPOLL', $P[6]);
 	define('P_CLOSEPOLL', $P[5]);
 	define('P_EDITPOLL', $P[4]);
 	define('P_ODELPOLL', $P[3]);
 	define('P_OCLOSEPOLL', $P[2]);
 	define('P_OEDITPOLL', $P[1]);
 	define('P_OMOVEPOLL', $P[0]);
 }
 else
 {
    $P = globalPermissions ( U_GROUPIDS );
 	define('P_CANSEEINVIS', $P[12]);
 	define('P_NOPMLIMIT', $P[11]);
 	define('P_INTEAM', $P[10]);
 	define('P_CEVENT', $P[9]);
 	define('P_SHOWDELETED', $P[8]);
	unset( $P );
 }
 
 // create head options
 $data['board_name'] = $config['board_name'];
 if( U_ID == 0 )
 {
     $data['headoption'] = '|| <a href="register.php">Registrieren</a> ';
 }
 else
 {
     $data['headoption'] = '|| <a href="pm.php">Private Messages</a> || <a href="profil.php">Profil</a> ||
     <a href="logout.php">Logout</a> || <a href="memberlist.php">Memberlist</a> ||
     <a href="calendar.php">Kalender</a> || <a href="team.php">Team</a> ';
     if( U_ISADMIN == 1 )
         $data['headoption'] .= '|| <a href="mod/index.php" target="blank">Modcenter</a> || <a href="admin/index.php" target="blank">Admincenter</a> ';
     if( U_ISMOD == 1 )
         $data['headoption'] .= '|| <a href="mod/index.php" target="blank">Modcenter</a> ';     
 }
 $data['headoption'] .= '|| <a href="help.php">FAQ</a> || <a href="search.php">Suche</a> ||
 <a href="'.$config['board_url'].'">Home</a> || <a href="stat.php">Statistik</a> ||';
 // Nav_path
 $data['nav_path'] = '&nbsp;<a href="'.$config['board_url'].'" class="bg">'.$config['board_name'].'</a>';
 // Board Style
 $where = "styleid='".U_STYLEID."'";
 if( U_STYLEID == 0 )
 {
     $where = "styleisdefault='1'";	
     if( isset( $boardid ) )
	 {
	     $r_boardstyle = db_query("SELECT
		     styleid
	     FROM ".$pref."board WHERE board_id='$boardid'");
		 $a_boardstyle = db_result( $r_boardstyle );
		 if( $a_boardstyle['styleid'] != 0 )
		     $where = "styleid='".$a_boardstyle['styleid']."'";
	 }
 }
 $r_style = db_query("SELECT
     *
 FROM ".$pref."style WHERE ".$where." ");
 $style = db_result( $r_style );
 $style['smallfont'] = '<font size="1">';
 $style['smallfontend'] = '</font>';
 
 if( U_ID == 0 )
	 $data['login'] = Get_Template( 'templates/'.$style['styletemplate'].'/login.html' );
 $data['javascript'] = '';
?>