<?php
/* $Id: team.php,v 1.1 2003/07/01 22:30:51 master_mario Exp $ */
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
 include ( 'inc/header.inc.php' );
 // nav_path ------------------------------------------
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Teampage';
 // global Templates ----------------------------------
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TTeam = Get_Template( 'templates/'.$style['styletemplate'].'/team.html' );
 $TTeamrow = Get_Template( 'templates/'.$style['styletemplate'].'/teamrow.html' );

 if( U_ID < 1 && $config['guest_team'] == 0 )
     message( 'Die Teampage ist wurde vom Administrator f&uuml;r G&auml;ste gesperrt.', 'Rechte', 0 );
 
 $r_team = db_query("SELECT
     user_name,
	 user_mail,
	 user_ismod,
	 user_isadmin,
	 user_lastacttime
 FROM ".$pref."user WHERE user_team='1' ORDER BY user_name");
 $teamrows = '';
 if( db_rows( $r_team ) > 0 )
 {
     $i = 0;
     while( $team = db_result( $r_team ) )
	 {
	     $level = '&nbsp;';
		 if( $team['user_ismod'] == 1 )
		     $level = 'Moderator';
		 if( $team['user_isadmin'] == 1 )
		     $level = 'Administrator';
		 $onlinetime = $board_time-600;
	 
	     $row = $TTeamrow;		 
		 $row = str_replace( '[rowcolor]', ( $i % 2 == 0 ? '[CellA]' : '[CellB]' ), $row );
		 $row = str_replace( '[level]', $level, $row );
		 $row = str_replace( '[name]', $team['user_name'], $row );
		 $row = str_replace( '[mail]', '<a href="mailto:'.$team['user_mail'].'">'.$team['user_mail'].'</a>', $row );
		 $row = str_replace( '[pm]', '<a href="pm.php?action=new&username='.$team['user_name'].'">An '.$team['user_name'].' senden</a>', $row );
		 $row = str_replace( '[online]', ( $team['user_lastacttime'] > $onlinetime ? 'Ja' : 'Nein' ), $row );
		 $teamrows .= $row; 
		 $i++;
	 }
 }
 
 $TTeam = str_replace( '[teamrows]', $teamrows, $TTeam );
 $data['boardtable'] = Template( $TTeam );
 echo Output( Template ( $TBoard ) );
?>