<?php
 /* $Id: stat.php,v 1.1 2003/07/05 17:10:08 master_mario Exp $ */
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
 function len( $string )
 {
     if( strlen( $string ) > 50 )
	     $string = substr ( $string, 0, 46 ).'...';
	 return $string;
 }

 include ( 'inc/header.inc.php' );
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TStat = Get_Template( 'templates/'.$style['styletemplate'].'/stat.html' );

 // navpat
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Statistikcenter';
 // Rechte
 if( !$config['statistik'] )
     message( 'Das Statistikcenter wurde vom Administrator deaktiviert.', 'Fehler', 0 );
 if( U_ID < 1 && !$config['guest_stats'] )
     message( 'Das Statistikcenter wurde vom Administrator f&uuml;r G&auml;ste deaktiviert.', 'Rechte', 0 );
 // persönliche Statistik
 if( U_ID < 1 )
     $data['personal'] = '&nbsp;Benutzer:   Gast';
 else
     $data['personal'] = '&nbsp;Benutzer:   '.U_NAME.'<br />&nbsp;Beitr&auml;ge: '.U_COUNT;
 // Boardübersicht
 $board = array();
 
 $r_board = db_query("SELECT
     user, posts, threads, polls, boards, cats, posts_del, threads_del, polls_del
 FROM ".$pref."stats");
 $a_board = db_result( $r_board );
 mysql_free_result( $r_board );
 
 $r_user = db_query("SELECT
     COUNT(user_id)
 FROM ".$pref."user WHERE user_lastacttime>'".($board_time-30*86400)."'");
 $a_user = db_result( $r_user );
 list(, $aktivuser ) = each( $a_user );
 mysql_free_result( $r_user );
 
 $r_views = db_query("SELECT
      SUM(thread_views)
 FROM ".$pref."thread");
 $a_views = db_result( $r_views );
 list(, $views ) = each( $a_views );
 mysql_free_result( $r_views );
 
 $r_admin = db_query("SELECT
     user_name, is_uradmin 
 FROM ".$pref."user WHERE user_isadmin='1'");
 $admin = array();
 while( $a_admin = db_result( $r_admin ) )
 {
     $admin[] = '<a href="s_profile.php?username='.$a_admin['user_name'].'">'.$a_admin['user_name'].'</a>';
	 if( $a_admin['is_uradmin'] == 1 )
	     $uradmin = '<a href="s_profile.php?username='.$a_admin['user_name'].'">'.$a_admin['user_name'].'</a>';
 }
 $admin = implode( ' || ', $admin );
 mysql_free_result( $r_admin );
 
 $r_last5 = db_query("SELECT
      user_name
 FROM ".$pref."user ORDER BY user_join DESC LIMIT 0, 5");
 $last5 = array();
 while( $a_last5 = db_result( $r_last5 ) )
 {
     $last5[] = '<a href="s_profile.php?username='.$a_last5['user_name'].'">'.$a_last5['user_name'].'</a>';
 }
 $last5 = implode( ' || ', $last5 );
 mysql_free_result( $r_last5 );
 
 
 $board[] = array( 'Das Forum läuft seit:', datum( $config['install'] ) );
 $board[] = array( 'Mitglieder insgesamt:', $a_board['user'] );
 $board[] = array( 'Aktive Mitglieder (im letzten Monat):', $aktivuser );
 $board[] = array( 'Beitr&auml;ge insgesamt:', $a_board['posts'].( P_SHOWDELETED ? '<font color="[col_link]">/'.$a_board['posts_del'].'</font>' : '' ) );
 $board[] = array( 'Themen insgesamt:', $a_board['threads'].( P_SHOWDELETED ? '<font color="[col_link]">/'.$a_board['threads_del'].'</font>' : '' ) );
 $board[] = array( 'Umfragen insgesamt:', $a_board['polls'].( P_SHOWDELETED ? '<font color="[col_link]">/'.$a_board['polls_del'].'</font>' : '' ) );
 $board[] = array( 'Views insgesamt:', $views );
 $board[] = array( 'Anzahl Kategorien:', $a_board['cats'] );
 $board[] = array( 'Anzahl Boards:', $a_board['boards'] );
 $board[] = array( 'Administratoren:',$admin  );
 $board[] = array( 'Uradmin:', $uradmin );
 $board[] = array( 'Die 5 neuesten Mitglieder:', $last5 );
 unset( $a_board ); unset( $aktivuser ); unset( $views ); unset( $admin ); unset( $uradmin ); unset( $last5 );
 
 $i=0;
 $data['board'] = '';
 foreach( $board as $value )
 {
     $data['board'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'">
	  <td class="blank">&nbsp;'.$value[0].'</td>
	  <td class="blank" style="text-align:right" width="300">[smallfont]'.$value[1].'[smallfontend]</td>
	 </tr>';   
	 $i++;
 }
 unset( $board );
 // DB und filesystem
 include "./inc/config.inc.php";
 $m_host = ''; $m_user = ''; $m_pw = '';
 $r_db = db_query("SHOW TABLE STATUS FROM $m_db");
 $m_db = '';
 $dbsize = 0;
 $dbentries = 0;
 while( $a_db = db_result( $r_db ) )
 {
     if ( substr($a_db['Name'], 0, strlen( $pref ) ) == $pref )
     {
         $dbentries += $a_db["Rows"];
         $dbsize += $a_db["Data_length"];
         $dbsize += $a_db["Index_length"];
     }
 }
 mysql_free_result( $r_db );
 $dbsize = round($dbsize/1024,1).' KB';
 if( $dbentries == 0 ) $dbentries = 'nicht verfügbar';
 if( $dbsize == 0 ) $dbsize = 'nicht verfügbar';
 $db = array();
 
 $db[] = array( 'Datenbank-Größe des Forums:', $dbsize ); 
 $db[] = array( 'Anzahl Datenbankeinträge des Forums:', $dbentries ); 
 
 $i=0;
 $data['db'] = '';
 foreach( $db as $value )
 {
     $data['db'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'">
	  <td class="blank">&nbsp;'.$value[0].'</td>
	  <td class="blank" style="text-align:right" width="300">[smallfont]'.$value[1].'[smallfontend]</td>
	 </tr>';   
	 $i++;
 }
 unset( $db );
 // Top 10 User nach Beiträgen
 $r_user = db_query("SELECT
     user_name, post_count
 FROM ".$pref."user ORDER BY post_count DESC LIMIT 0, 10");
 $i=1;
 $data['userbyposts'] = '';
 while( $a_user = db_result( $r_user ) )
 {
     $data['userbyposts'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'"><td class="blank" width="10" style="text-align:right">
	  [smallfont]'.$i.'[smallfontend]
	  </td><td class="blank">&nbsp;[smallfont]<a href="s_profile.php?username='.$a_user['user_name'].'">'.$a_user['user_name'].'[smallfontend]</a></td>
	  <td class="blank" style="text-align:right">[smallfont]'.$a_user['post_count'].'[smallfontend]</td>
	  </tr>';
	 $i++;
 } 
 mysql_free_result( $r_user );
 unset( $a_user );
 // Top 10 Boards nach Beiträgen
 $show_boards = '';
 $r_board = db_query("SELECT
     board_name, board_id, posts, posts_del
 FROM ".$pref."board WHERE category!='0' ORDER BY posts DESC");
 $i=1;
 $data['boardsbyposts'] = '';
 while( $a_board = db_result( $r_board ) )
 {
     if( $i == 10 ) break;
     $P = boardPermissions ( U_GROUPIDS, $a_board['board_id'] );
     if( $P[0] == 1 )
	 {
	     if( $show_boards == '' ) $show_boards = $a_board['board_id'];
	     else $show_boards .= ','.$a_board['board_id'];
         $data['boardsbyposts'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'"><td class="blank" width="10" style="text-align:right">
	      [smallfont]'.$i.'[smallfontend]
	      </td><td class="blank">&nbsp;[smallfont]<a href="board.php?boardid='.$a_board['board_id'].'">'.len($a_board['board_name']).'</a>[smallfontend]</td>
	      <td class="blank" style="text-align:right">[smallfont]'.$a_board['posts'].( P_SHOWDELETED ? '<font color="[col_link]">/'.$a_board['posts_del'].'</font>' : '' ).'[smallfontend]</td>
	      </tr>';
	     $i++;
     }
 } 
 mysql_free_result( $r_board );
 unset( $a_board );
 // Top 10 Threads nach Beiträgen
 $option = '';
 if( U_ID < 1 ) $option = "deleted='0' AND";
 if( U_ID > 0 && !P_SHOWDELETED ) $option = "deleted='0' AND"; 
 $r_thread = db_query("SELECT
     thread_topic, thread_id, board_id, replies, replies_del, deleted
 FROM ".$pref."thread WHERE ".$option." board_id IN (".$show_boards.") ORDER BY replies DESC LIMIT 0, 10");
 $i=1;
 $data['threadsbyposts'] = '';
 while( $a_thread = db_result( $r_thread ) )
 {
     $data['threadsbyposts'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'"><td class="blank" width="10" style="text-align:right">
	  [smallfont]'.$i.'[smallfontend]
	  </td><td class="blank">&nbsp;[smallfont]<a href="showtopic.php?threadid='.$a_thread['thread_id'].'&boardid='.$a_thread['board_id'].'">'.len($a_thread['thread_topic']).'</a>[smallfontend]
	  '.( $a_thread['deleted'] == 1 && P_SHOWDELETED ? '&nbsp;<img src="templates/'.$style['styletemplate'].'/images/saved_no.gif" width="10" height="10" border="0" />' : '' ).'</td>
	  <td class="blank" style="text-align:right">[smallfont]'.( $a_thread['deleted'] == 0 ? ($a_thread['replies']+1) : $a_thread['replies'] ).( P_SHOWDELETED ? '<font color="[col_link]">/'.( $a_thread['deleted'] == 1 ? ($a_thread['replies_del']+1) : $a_thread['replies_del'] ).'</font>' : '' ).'[smallfontend]</td>
	  </tr>';
	 $i++;
 } 
 mysql_free_result( $r_thread );
 unset( $a_thread );
 // Top 10 Threads nach Views
 $option = '';
 if( U_ID < 1 ) $option = "deleted='0' AND";
 if( U_ID > 0 && !P_SHOWDELETED ) $option = "deleted='0' AND"; 
 $r_thread = db_query("SELECT
     thread_topic, thread_id, board_id, thread_views, deleted
 FROM ".$pref."thread WHERE ".$option." board_id IN (".$show_boards.") ORDER BY thread_views DESC LIMIT 0, 10");
 $i=1;
 $data['threadsbyviews'] = '';
 while( $a_thread = db_result( $r_thread ) )
 {
     $data['threadsbyviews'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'"><td class="blank" width="10" style="text-align:right">
	  [smallfont]'.$i.'[smallfontend]
	  </td><td class="blank">&nbsp;[smallfont]<a href="showtopic.php?threadid='.$a_thread['thread_id'].'&boardid='.$a_thread['board_id'].'">'.len($a_thread['thread_topic']).'</a>[smallfontend]
	  '.( $a_thread['deleted'] == 1 && P_SHOWDELETED ? '&nbsp;<img src="templates/'.$style['styletemplate'].'/images/saved_no.gif" width="10" height="10" border="0" />' : '' ).'</td>
	  <td class="blank" style="text-align:right">[smallfont]'.$a_thread['thread_views'].'[smallfontend]</td>
	  </tr>';
	 $i++;
 } 
 mysql_free_result( $r_thread );
 unset( $a_thread );
 // Letzte 10 Posts
 $r_post = db_query("SELECT
     thread_topic, thread_id, last_post_id, last_act_time, board_id
 FROM ".$pref."thread WHERE board_id IN (".$show_boards.") ".( P_SHOWDELETED ? "" : "AND deleted='0'" )." ORDER BY last_act_time DESC LIMIT 0, 10");
 $i=1;
 $data['last10posts'] = '';
 while( $a_post = db_result( $r_post ) )
 {
     $data['last10posts'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'"><td class="blank" width="10" style="text-align:right">
	  [smallfont]'.$i.'[smallfontend]
	  </td><td class="blank">&nbsp;[smallfont]<a href="showtopic.php?threadid='.$a_post['thread_id'].'&boardid='.$a_post['board_id'].'#p'.$a_post['last_post_id'].'">'.len($a_post['thread_topic']).'</a>[smallfontend]</td>
	  <td class="blank" style="text-align:right">[smallfont]'.datum($a_post['last_act_time']).'[smallfontend]</td>
	  </tr>';
	 $i++;
 } 
 mysql_free_result( $r_post );
 unset( $a_post );
 // letze 12 Monate
 $month = date( "m", $board_time );
 $year  = date( "Y", $board_time );
 $monatsname = array(" ", "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August",
  "September", "Oktober", "November", "Dezember"); 
 $monate = array();
 $monate[] = array( $monatsname[intval($month)].'&nbsp;'.$year, mktime(0, 0, 0, intval($month), 1, $year), mktime(0, 0, -1, intval($month)+1, 1, $year) );
 for( $i=0; $i<12; $i++ )
 {
     $month--;
	 if( $month == 0 )
	 {
	     $month = 12;
		 $year--;
	 }
	 if( date( "m", $config['install'] ) > $month && date( "Y", $config['install'] ) <= $year )
	 {
	     break;
	 }
     $monate[] = array( $monatsname[intval($month)].'&nbsp;'.$year, mktime(0, 0, -1, intval($month), 1, $year), mktime(0, 0, 0, intval($month)+1, 1, $year) );
 }
 $data['last2years'] = '';
 $i=0;
 foreach( $monate as $value )
 {
     $r_montdata = db_query("SELECT
	     COUNT(post_id)
	 FROM ".$pref."post WHERE post_time>'$value[1]' AND post_time<'$value[2]'");
	 $a_montdata = db_result( $r_montdata );
	 list(, $monthposts ) = each( $a_montdata );
	 mysql_free_result( $r_montdata ); unset( $a_montdata );
	 
     $r_montdata = db_query("SELECT
	     COUNT(user_id)
	 FROM ".$pref."user WHERE user_join>'$value[1]' AND user_join<'$value[2]'");
	 $a_montdata = db_result( $r_montdata );
	 list(, $monthuser ) = each( $a_montdata );
	 mysql_free_result( $r_montdata ); unset( $a_montdata );
	 
     $data['last2years'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '[CellA]' : '[CellB]' ).'">
	  <td class="blank" width="33%">&nbsp;[smallfont]'.$value[0].'[smallfontend]</td>
	  <td class="blank" width="33%">&nbsp;[smallfont]'.$monthposts.'[smallfontend]</td>
	  <td class="blank" width="33%">&nbsp;[smallfont]'.$monthuser.'[smallfontend]</td>
	  </tr>';
	 $i++;
 }
 $data['zeichen'] = '';
 if( U_ID > 0 && P_SHOWDELETED )
     $data['zeichen'] = '&nbsp;<img src="templates/'.$style['styletemplate'].'/images/saved_no.gif" width="10" height="10" border="0" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[smallfont]Als gel&ouml;scht makiert.[smallfontend]<br />
	 &nbsp;<font color="[col_link]">/Wert</font>&nbsp;[smallfont]Gel&ouml;schte Beitr&auml;ge/Themen/Umfragen[smallfontend]';
 
 $data['boardtable'] = Template( $TStat );
 echo Output( Template ( $TBoard ) );
?>