<?php
 /* $Id: index.php,v 1.9 2003/07/01 16:49:18 master_mario Exp $ */
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
  
 if( U_ID != 0 )
 {
     if( !isset( $_SESSION['newpost'] ) )
         setNewposts( U_OLDTIME );
	 if( U_PM_OVER == 1 )
	 {
	     $data['message'] = "onLoad=\"javascript:alert('Jemand hat versucht Dir ein Message zu senden.\\nDeine Massagebox ist aber leider voll.')\"";
	 }
	 if( U_PM_NEW == 1 && U_NO_PM == 1 )
	 {
	     $data['message'] = "onLoad=\"javascript:alert('Du hast ".U_PM_COUNT." neue Nachricht/en')\"";
	 }
 } 
	  
  
 $TCatrow = Get_Template( 'templates/'.$style['styletemplate'].'/catrow.html' );
 $TBoardrow = Get_Template( 'templates/'.$style['styletemplate'].'/index_b_row.html' );
 $TIndex = Get_Template( 'templates/'.$style['styletemplate'].'/boardtable.html' ); 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 
 $onlinebox = '';
 if( $config['onlinebox'] )
 {
     $TOnline = Get_Template( 'templates/'.$style['styletemplate'].'/online.html' ); 
	 include( 'online.php' );
	 $pre = 'ist';
	 if( $onlinecount > 1 || $onlinecount < 1 )
	     $pre = 'sind';
	 $TOnline = str_replace( '[onlinecount]', $pre.' <b>'.$onlinecount.'</b>', $TOnline );
	 $TOnline = str_replace( '[onlineuser]', $usercount, $TOnline );
	 $post = 'G&auml;ste';
	 if( $guestcount == 1 )
	     $post = 'Gast';
	 $TOnline = str_replace( '[onlineguest]', '<b>'.$guestcount.'</b> '.$post, $TOnline );
	 $TOnline = str_replace( '[onlinelist]', $onlinelist, $TOnline );
	 $TOnline = str_replace( '[onlinecount24]', $onlinecount24, $TOnline );
	 $TOnline = str_replace( '[onlinelist24]', $onlinelist24, $TOnline );
	 $TOnline = str_replace( '[rek]', $rek, $TOnline );
	 $TOnline = str_replace( '[rekorttime]', $rekorttime, $TOnline );
	 $onlinebox = $TOnline;
 }
 
 $statbox = '';
 if( $config['statbox'] )
 {
     $TStat = Get_Template( 'templates/'.$style['styletemplate'].'/stats.html' );
	 include( 'stats.php' );
	 $statbox = $TStat;
 }
 
 $r_category = db_query("SELECT      
     category_id,
	 category_name,
	 category_is_open
 FROM ".$pref."category ORDER BY category_order");
 if( db_rows( $r_category ) < 1 )
     $data['boards'] = '<tr><td class="cella" colspan="5"><center>Keine Kategorien angelegt.</center></td></tr>';
 else
 {
     if( !isset( $_SESSION['openclose'] ) ) // openclose schon erzeugt ?
	     $_SESSION['openclose'] = '0';
     $boards = '';
     while( $a_category = db_result( $r_category ) )
	 {
	     $new_cat = 0;
		 $session_cat = 'c'.$a_category['category_id']; // openclose Variable
	     if( $_SESSION['openclose'] == 0 ) // wenn nicht dann erzeugen
		 {
			 $_SESSION[$session_cat] = $a_category['category_is_open'];
			 // SESSION_vars für openclose setzen
		 }
	     $board_count = 0; 
         $r_boards = db_query("SELECT
	         board_id,
		     board_name,
			 board_under,
			 last_act_time,
			 last_post_id,
			 last_act_user, 
			 last_thread_id, 
			 last_act_thread,
			 threads,
			 posts,
			 threads_del,
			 posts_del
	     FROM ".$pref."board WHERE disabled='0' AND category='".$a_category['category_id']."' ORDER BY board_order ASC");
		 if( db_rows( $r_boards ) > 0 )
		 {
		     $category = '';
		     while( $board = db_result( $r_boards ) )
			 {
                 $P = boardPermissions ( U_GROUPIDS, $board['board_id'] );
				 if( $P[0] == 1 )
				 {
			         $board_count++;
				 
				     $session_var = 'b'.$board['board_id'];
				     $new_topic = 0;
			    	 $gif = '';
		    		 if( U_ID != 0 )
			    	 {
				         if( $board['last_post_id'] > $_SESSION[$session_var] && $board['last_post_id'] != 0 )
				         {
						     $gif = '_new'; 
							 $new_topic = 1;    
							 $new_cat = 1;
						 } 
				     }
		    		 $boardname = '<a href="board.php?boardid='.$board['board_id'].'"><b>'.$board['board_name'].'</b></a>';
			    	 $boardname .= '<br />[smallfont]'.$board['board_under'].'[smallfontend]';
				     $threads = $board['threads'];
				     $posts = $board['posts'];
				     if( P_SHOWDELETED == 1 )
				     {
				         $threads .= '/<font color="'.$style['col_link'].'">[smallfont]'.$board['threads_del'].'[smallfontend]</font>';
				         $posts .= '/<font color="'.$style['col_link'].'">[smallfont]'.$board['posts_del'].'[smallfontend]</font>';
				     }
				     if( $board['last_act_time'] != 0 )
				     {
				         $last = datum ( $board['last_act_time'] );
					     if( strlen( $board['last_act_thread'] ) > 50 )
					         $board['last_act_thread'] = substr ( $board['last_act_thread'], 0, 46 ).'...';
					     $last .= '<br /><a href="showtopic.php?threadid='.$board['last_thread_id'].'&boardid='.$board['board_id'].'&page=last">'.$board['last_act_thread'].'</a>';
					     $last .= '&nbsp;von <a href="s_profile.php?username='.$board['last_act_user'].'">'.$board['last_act_user'].'</a>';
				     }
				     else
				         $last = '<center>unbekannt</center>';
				 
				     $row = $TBoardrow;
		    		 $row = str_replace( '[board_gif]', '<img src="templates/'.$style['styletemplate'].'/images/board'.$gif.'.gif" border="0">', $row );
			    	 $row = str_replace( '[forum_name]', $boardname, $row );
				     $row = str_replace( '[threads]', $threads, $row );
     				 $row = str_replace( '[posts]', $posts, $row );
		    		 $row = str_replace( '[last]', $last, $row );
			    	 $category .= $row; 
				 } // if
			 } // while
		 } // if
		 $catblock = '';
		 if( $board_count > 0 )
		 {
	         $catname = '<a href="category.php?catid='.$a_category['category_id'].'"><b>'.$a_category['category_name'].'</b></a>';	
			 $catname .= '&nbsp;<font size="1">[<a href="open_cat.php?catid='.$a_category['category_id'].'">open/close</a>]</font>'; 
		     $catimage = '';
			 if( $new_cat == 1 ) $catimage = '_new';
		 
		     $row = $TCatrow;
			 $row = str_replace( '[category_name]', $catname, $row );
			 $row = str_replace( '[catimage]', '<img src="templates/'.$style['styletemplate'].'/images/board'.$catimage.'.gif" border="0" width="40" height="16">', $row );
			 $catblock = $row;
			 if( $_SESSION[$session_cat] == 1 )
			 {
			     $catblock .= $category;
			 }			
			 $boards .= $catblock;
		 } // if
	 } // while
	 if( $_SESSION['openclose'] == 0 )
	     $_SESSION['openclose'] = 1; // openclose Variablen als gesetzt makieren
 } // else
 
 $data['boards'] = $boards;
 $data['user'] = U_NAME;
 $data['PMreport'] = '';
 if( U_ID > 0 )
 {
     if( U_PM_NEW == 0 )
	     $data['PMreport'] = 'Du hast keine neuen Nachrichten.';
     if( U_PM_NEW == 1 && U_PM_COUNT == 1 )
	     $data['PMreport'] = 'Du hast eine neue Nachricht.';
     if( U_PM_NEW == 1 && U_PM_COUNT > 1 )
	     $data['PMreport'] = 'Du hast '.U_PM_COUNT.' neue Nachrichten.';
 }
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Foren&uuml;bersicht';
 $data['time'] = 'Serverzeit: '.date( "d.M.Y\, H:i \U\h\\r", $board_time );
 $data['onlinebox'] = Template( $onlinebox );
 $data['statbox'] = Template( $statbox );
 $data['boardtable'] = Template( $TIndex );
 echo Output( Template ( $TBoard ) );
?>