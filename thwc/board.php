<?php
 /* $Id: board.php,v 1.4 2003/06/20 10:40:04 master_mario Exp $ */
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
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TThreadtable = Get_Template( 'templates/'.$style['styletemplate'].'/threadtable.html' );
 $TThreadrow   = Get_Template( 'templates/'.$style['styletemplate'].'/threadrow.html' );

 if( !isset( $page ) )
     $page = 1;
 $data['thread_nav'] = '';

 $r_board = db_query("SELECT
     board_id,
     board_name,
     category,
     threads,
     threads_del
 FROM ".$pref."board WHERE board_id='$boardid'");
 if( db_rows( $r_board ) != 1 )
     message ( 'Sorry! Fehlerhafte Boardid.', 'Fehler', 0 );
 else
 {
     $board = db_result( $r_board );
     if( P_VIEW == 0 || $board['category'] == 0 )
         message ( 'Sorry! Du hast nicht die Berechtigung dieses Board zu &ouml;ffnen.', 'Zugriff verweigert', 0 );
     else
     {
         $r_category = db_query("SELECT
             category_id,
             category_name
         FROM ".$pref."category WHERE category_id='".$board['category']."'");
         $a_category = db_result( $r_category );
         // thread permissions
         if( P_POSTNEW == 1 )
             $data['thread_permission'] = 'Ja';
         else
             $data['thread_permission'] = 'Nein';
         // boardid
         $data['b_id'] = $board['board_id'];
         // JUMP
         $data['jump'] = Jump( $pref );
         // newthreadimage
         $data['newthtext'] = '<b>Neuen Thread er&ouml;ffnen</b>';
		 if( $style['newtopicimage'] != '' )
	         $data['newthtext'] = '<img src="'.$style['newtopicimage'].'" border="0" />';
         // polloptions
         $data['pollopt1'] = '';
         $data['pollopt2'] = '';
         if( $config['polls_enable'] == 1 )
         {
             $data['pollopt2'] = 'Neue Umfragen erstellen:';
             if( P_POLLNEW == 1 )
             {
	             $polltext = '<b>Neue Umfrage erstellen</b>';
				 if( $style['newpollimage'] != '' )
				     $polltext = '<img src="'.$style['newpollimage'].'" border="0" />';
                 $data['pollopt1'] .= '&nbsp;||&nbsp;<a href="newpoll.php?boardid='.$board['board_id'].'" class="bg">'.$polltext.'</a>';
				 $data['pollopt2'] .= '&nbsp;<b>Ja</b>';
             }
             else
                 $data['pollopt2'] .= '&nbsp;<b>Nein</b>';
         }
         // deleteicon        
		 $data['zeichen'] = '';
         if( P_SHOWDELETED == 1 )
             $data['zeichen'] = '&nbsp;<img src="templates/[styletemplate]/images/delete.gif" />&nbsp;
                                  Thread verschoben<br />';
         // thread_nav
         $r_linkcount = db_query("SELECT
             COUNT(thread_id)
         FROM ".$pref."thread WHERE link='".$board['board_id']."'");
         $a_linkcount = db_result( $r_linkcount );
         list(, $linked ) = each( $a_linkcount );
         $th_count = $linked;
         $th_count += $board['threads'];
         if( P_SHOWDELETED == 1 )
             $th_count += $board['threads_del'];
         $data['thread_nav'] = check_pages( $th_count, $config['thread_rows'], $page, 0, 'board.php?boardid='.$board['board_id'] );
         // define LIMIT
         if( $page == 'last' )
             $page = $a_board['threads'];
         $page  = intval( $page );
         if( $page<1 )
             $page = 1;
         $start = ($page-1)*$config['thread_rows'];
         $limit = $start.', '.$config['thread_rows'];
         // threadrows--------------------------------
         $r_thread = db_query("SELECT
             thread_id,
			 board_id,
             replies,
             thread_autor,
             thread_topic,
             last_act_time,
             last_post_id,
             last_act_user,
             thread_icon,
             thread_views,
			 thread_closed,
             sticky,
             deleted,
             replies_del,
             link
         FROM ".$pref."thread WHERE board_id='".$board['board_id']."' OR link='".$board['board_id']."' ORDER BY sticky DESC,last_act_time DESC LIMIT ".$limit." ");
         if( db_rows( $r_thread ) < 1 )
             $data['threads'] = '<tr><td class="cellb" colspan="6"><center><font color="[color_err]">Noch keine Threads angelegt.</font></center></td></tr>';
         else
         {
             $data['threads'] = '';
             while( $thread = db_result( $r_thread ) )
             {
                 if( $thread['deleted'] == 1 && P_SHOWDELETED != 1 )
                     continue;
                 else
                 {
                     $session_var = 'b'.$board['board_id'];
                     // icon
                     $iconadd = '';
                     $pic = 'icon/'.$thread['thread_icon'];
					 if( $thread['thread_closed'] == 1 )
					     $pic = 'closed';
                     if( U_ID != 0 )
                     {
                         if( $_SESSION[$session_var] < $thread['last_post_id'] )
                             $pic .= '_new';
                     }
                     if( $thread['link'] == $board['board_id'] )
                         $pic = 'moved';
                     if( $thread['deleted'] == 1 )
                         $pic = 'delete';
                     $icon = '<img src="templates/'.$style['styletemplate'].'/images/'.$pic.'.gif" border="0">';
                     // topic
                     $topic = '&nbsp;';
                     if( $thread['sticky'] == 1 )
                         $topic .= '<b>'.$config['sticky_word'].':</b>&nbsp;';
                     $topic .= '<a href="showtopic.php?threadid='.$thread['thread_id'].'&boardid='.$thread['board_id'].'">'.$thread['thread_topic'].'</a>';
                     // postnav ------------------------------
                     $post_count = $thread['replies']+1;
                     if( P_SHOWDELETED == 1 )
                         $post_count += $thread['replies_del'];
                     $thread_pages = $post_count/$config['post_rows'];
                     $thread_pages = bcadd( $thread_pages, 0, 0 );
                     if( bcmul ( $thread_pages, $config['post_rows'], 0 ) < $post_count )
                         $thread_pages++;
                     $topic_add = '';
                     if( $thread_pages > 1 )
                         $topic_add = '(Seiten: <a href="showtopic.php?threadid='.$thread['thread_id'].'&boardid='.$board['board_id'].'&page=1">1</a>';
                     for( $x=2; $x<4; $x++ )
                     {
                         if( $x <= $thread_pages )
                             $topic_add .= ' <a href="showtopic.php?threadid='.$thread['thread_id'].'&boardid='.$board['board_id'].'&page='.$x.'">'.$x.'</a>';
                     }
                     if( $thread_pages > 3 )
                     {
                         $topic_add .= ' ...';
                         $show_links_from = $thread_pages-2;
                         for( $x=$show_links_from; $x<=$thread_pages; $x++ )
                         {
                             if( $x > 3 )
                                 $topic_add .= ' <a href="showtopic.php?threadid='.$thread['thread_id'].'&boardid='.$board['board_id'].'&page='.$x.'">'.$x.'</a>';
                         }
                     }
                     if( $thread_pages > 1 )
                         $topic_add .= ' )';
                     if( $thread['link'] == 0 )
                         $topic .= '&nbsp;[smallfont]'.$topic_add.'[smallfontend]';
                     // if link and user has permission then link to delete link
                     if( $thread['link'] == $board['board_id'] && P_OMOVE == 1 )
                         $topic .= '&nbsp;[smallfont][<a href="threadopt.php?action=dellink&threadid='.$thread['thread_id'].'&boardid='.$board['board_id'].'">Link entfernen</a>][smallfontend]';
                     // replies
                     $replies = $thread['replies'];
                     if( P_SHOWDELETED == 1 )
                         $replies .= '/[smallfont]<font color="[col_link]">'.$thread['replies_del'].'</font>[smallfontend]';
                     // views
                     $views = $thread['thread_views'];
                     // autor
                     $autor = 'unbekannt';
                     if( $thread['thread_autor'] != '' )
                         $autor = '<a href="s_profile.php?username='.$thread['thread_autor'].'">'.$thread['thread_autor'].'</a>';
                     // last act
                     $last = date( "d.m.Y H:i\U\h\\r", $thread['last_act_time'] );
                     $last .= '<br />von&nbsp;<a href="s_profile.php?username='.$thread['last_act_user'].'">'.$thread['last_act_user'].'</a>';
                     // if link then delete infos
                     if( $thread['link'] == $board['board_id'] )
                     {
                         $replies = '-';
                         $views   = '-';
                         $last = '<center>---</center>';
                     }
					 // template
                     $row = $TThreadrow;
                     $row = str_replace( '[icon]', $icon, $row );
                     $row = str_replace( '[topic]', $topic, $row );
                     $row = str_replace( '[replies]', $replies, $row );
                     $row = str_replace( '[views]', $views, $row );
                     $row = str_replace( '[autor]', $autor, $row );
                     $row = str_replace( '[last]', $last, $row );
                     $data['threads'] .= $row;
                 } // else
             } // while
         } // else
     } // else
 } // else

 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="category.php?catid='.$a_category['category_id'].'" class="bg">'.$a_category['category_name'].'</a>';
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;'.$board['board_name'];
 $data['boardtable'] = Template( $TThreadtable );
 echo Output( Template ( $TBoard ) );
?>