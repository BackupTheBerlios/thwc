<?php
 /* $Id: modhead.inc.php,v 1.1 2003/06/12 13:59:20 master_mario Exp $ */
 error_reporting(E_ALL);

if( isset($HTTP_GET_VARS) )
        extract($HTTP_GET_VARS);
if( isset($HTTP_PUT_VARS) )
        extract($HTTP_PUT_VARS);
if( isset($HTTP_POST_VARS) )
        extract($HTTP_POST_VARS);

 function sigControl()
 {
     global $pref, $board_time;
     $r_log = db_query("SELECT
         user_ismod,
         user_isadmin,
         ad_sig,
         ad_time
     FROM ".$pref."user WHERE user_id='$_SESSION[userid]'");
     if( db_rows( $r_log ) != 1 )
         return 0;
     else
     {
         $a_log = db_result( $r_log );
         $ismod = 0;
         if( $a_log['user_ismod'] == 1 || $a_log['user_isadmin'] == 1 )
             $ismod = 1;
         $sigok = 0;
         if( $a_log['ad_sig'] == $_SESSION['adsig'] )
             $sigok = 1;
         $timeok = 0;
         if( $a_log['ad_time'] > $board_time-600 )
             $timeok = 1;
         if( $ismod == 1 && $sigok == 1 && $timeok == 1 )
             return 1;
         else
             return 0;
     }
 }

 include ( '../inc/config.inc.php' );
 include ( '../inc/functions.inc.php' );
 include ( '../inc/adfunc.inc.php' );
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

 $data['board_name'] = $config['board_name'];
 // Systemzeit
 $board_time = time()+$config['diff_board_time'];
 // login
 session_start();
 if( !isset($action) ) $action = '';
 if( $action == 'login' )
 {
     $r_admin = db_query("SELECT
         user_id,
         user_name,
         user_pw
     FROM ".$pref."user WHERE user_name='".addslashes($username)."'");
     if( db_rows( $r_admin ) == 1 )
     {
         $a_admin = db_result( $r_admin );
         if( md5($password) == $a_admin['user_pw'] )
         {
             $adsig = md5($board_time);
             db_query("UPDATE ".$pref."user SET
                 ad_sig='$adsig',
                 ad_time='$board_time'
             WHERE user_id='$a_admin[user_id]'");
             $_SESSION['adsig'] = $adsig;
             $_SESSION['userid'] = $a_admin['user_id'];
         }
     }
 }
 if( !isset( $_SESSION['adsig'] ) ) $_SESSION['adsig'] = 0;
 if( !isset( $_SESSION['userid'] ) ) $_SESSION['userid'] = 0;
 if( sigControl() == 1 )
 {
     $a_admin = db_query("SELECT
         user_id,
         user_name,
         user_isadmin,
         is_uradmin,
         user_styleid
     FROM ".$pref."user WHERE user_id='$_SESSION[userid]'");
     $admin = db_result( $a_admin );
     // style ---------------------------------------------------------
     if( $admin['user_styleid'] != 0 )
         $where = "styleid='$admin[user_styleid]'";
     else
         $where = "styleisdefault='1'";
     $r_style = db_query("SELECT
         *
     FROM ".$pref."style WHERE ".$where." ");
     $style = db_result( $r_style );
 }
 else
 {
     print Get_Template( 'login.html' );
     exit;
 }
 // modlog
 $basename = basename($HTTP_SERVER_VARS["SCRIPT_NAME"]);
 if( $action != '' )
 {
     db_query( "INSERT INTO ".$pref."modlog SET
         logtime='$board_time',
         loguser='".addslashes($admin['user_name'])."',
         logip='".getenv('REMOTE_ADDR')."',
         logfile='$basename',
         action='".addslashes($action)."'");
 }
 // create menu
 $data['menurows'] = Template( Get_Template( 'modmenu.html' ) );
?>