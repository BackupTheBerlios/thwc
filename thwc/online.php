<?php
 /* $Id: online.php,v 1.2 2003/07/01 22:30:31 master_mario Exp $ */
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
 $onlinelimit = $board_time-600;
 $onlinecount = 0;
 $usercount   = 0;
 $guestcount  = 0;
 $onuser      = array();
 $onlinelist  = ''; 
 
 $r_onuser = db_query("SELECT
     user_name,
	 user_ishidden
 FROM ".$pref."user WHERE user_lastacttime>'$onlinelimit' AND user_session!='' ORDER BY user_name ASC");
 if( db_rows( $r_onuser ) > 0 )
 {
     while( $a_onuser = db_result( $r_onuser ) )
	 {
		 if( $a_onuser['user_ishidden'] == 0 )
		 {
	         $onlinecount++;
	         $usercount++;
		     $onuser[] = '<a href="s_profile.php?username='.$a_onuser['user_name'].'">'.$a_onuser['user_name'].'</a>';
		 }
		 else
		 {
		     if( P_CANSEEINVIS == 1 )
			 {
	             $onlinecount++;
	             $usercount++;
			     $onuser[] = '<a href="s_profile.php?username='.$a_onuser['user_name'].'">'.$a_onuser['user_name'].'</a>(Versteckt)';
			 }
		 }
	 }
 }
 if( count( $onuser ) > 0 )
     $onlinelist = implode( ' ,', $onuser );
 unset( $onuser );
 mysql_free_result( $r_onuser );
 
 $r_onguest = db_query("SELECT
     COUNT(session_id)
 FROM ".$pref."guest WHERE last_act_time>'$onlinelimit'");
 $a_onguest = db_result( $r_onguest );
 list(, $guestcount ) = each( $a_onguest );
 $onlinecount = $onlinecount + $guestcount;
 mysql_free_result( $r_onguest );
 
 $onlinecount24 = 0;
 $onuser24      = array();
 $onlinelist24  = '';  
 
 $r_24user = db_query("SELECT
     user_name,
	 user_ishidden
 FROM ".$pref."user WHERE user_lastacttime>'".($board_time-86400)."'");
 if( db_rows( $r_24user ) > 0 )
 {
     while( $a_24user = db_result( $r_24user ) )
	 {
		 if( $a_24user['user_ishidden'] == 0 )
		 {
	         $onlinecount24++;
   		     $onuser24[] = '<a href="s_profile.php?username='.$a_24user['user_name'].'">'.$a_24user['user_name'].'</a>';
		 }
		 else
		 {
		     if( P_CANSEEINVIS == 1 )
			 {
			     $onlinecount24++;
			     $onuser24[] = '<a href="s_profile.php?username='.$a_24user['user_name'].'">'.$a_24user['user_name'].'</a>(Versteckt)';
			 }
		 }
	 }
 } 
 if( count( $onuser24 ) > 0 )
     $onlinelist24 = implode( ' ,', $onuser24 );
 unset( $onuser24 );
 mysql_free_result( $r_24user );
 
 $r_onstat = db_query("SELECT
     max_online,
	 max_online_time
 FROM ".$pref."stats");
 $a_onstat = db_result( $r_onstat );
 $rek = $a_onstat['max_online'];
 $rekorttime = datum( $a_onstat['max_online_time'] );
 if( $onlinecount > $rek )
 {
     $rek = $onlinecount;
     $rekorttime = datum( $board_time );
	 db_query("UPDATE ".$pref."stats SET
	     max_online='$rek',
		 max_online_time='$board_time'");
 }
 mysql_free_result( $r_onstat );
?>