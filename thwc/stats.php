<?php
 /* $Id: stats.php,v 1.1 2003/07/01 16:33:40 master_mario Exp $ */
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
 
 $r_stats = db_query("SELECT
     user,
	 posts,
	 threads,
	 polls,
	 posts_del,
	 threads_del,
	 polls_del
 FROM ".$pref."stats");
 $a_stats = db_result( $r_stats );
 
 $TStat = str_replace( '[usercount]', $a_stats['user'], $TStat );
 $TStat = str_replace( '[threadcount]', '<b>'.$a_stats['threads'].( P_SHOWDELETED == 1 ? '<font color="[col_link]">/'.$a_stats['threads_del'].'</font>' : '' ).'</b>', $TStat );
 $TStat = str_replace( '[pollcount]', '<b>'.$a_stats['polls'].( P_SHOWDELETED == 1 ? '<font color="[col_link]">/'.$a_stats['polls_del'].'</font>' : '' ).'</b>', $TStat );
 $TStat = str_replace( '[postcount]', '<b>'.$a_stats['posts'].( P_SHOWDELETED == 1 ? '<font color="[col_link]">/'.$a_stats['posts_del'].'</font>' : '' ).'</b>', $TStat );

 mysql_free_result( $r_stats );
 unset( $a_stats );
 
 $r_aktiv = db_query("SELECT
     COUNT(user_name)
 FROM ".$pref."user WHERE user_lastacttime>'".($board_time-30*86400)."'");
 $a_aktiv = db_result( $r_aktiv );
 list(, $aktiv ) = each( $a_aktiv );
 $pre = 'sind';
 if( $aktiv == 1 )
     $pre = 'ist';
 $TStat = str_replace( '[aktivuser]', $pre.' <b>'.$aktiv.'</b>', $TStat );
 
 mysql_free_result( $r_aktiv );
 unset( $a_aktiv );
?>