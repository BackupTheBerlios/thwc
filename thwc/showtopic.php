<?php
 /* $Id: showtopic.php,v 1.3 2003/06/20 10:39:08 master_mario Exp $ */
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
 include ( 'inc/bcode.inc.php' );

 if( U_ID != 0 )
 {
     if( !isset( $_SESSION['newpost'] ) )
         setNewposts( U_OLDTIME );
 } 
 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TPosttab = Get_Template( 'templates/'.$style['styletemplate'].'/posttable.html' );
 $TPostrow = Get_Template( 'templates/'.$style['styletemplate'].'/postrow.html' );
 
 if( !isset( $page ) )
     $page = 1;
 $data['post_nav'] = '';
 
 $r_thread = db_query("SELECT
     board_id,
	 thread_topic,
	 thread_views,
	 thread_closed,
	 deleted,
	 replies,
	 thread_autor,
	 autor_id,
	 replies_del,
	 is_poll,
	 deleted
 FROM ".$pref."thread WHERE thread_id='$threadid' AND board_id='$boardid'");
 // Permission check ------------------------------------------
 if( db_rows( $r_thread ) != 1 )
     message ( 'Sorry! Fehlerhafter Link.', 'Fehler', 0 );
 $thread = db_result( $r_thread );
 mysql_free_result( $r_thread );
 if( P_VIEW != 1 )
     message ( 'Du bist nicht berechtigt dieses Board zu &ouml;ffnen.', 'Rechte', 0 );
 if( $thread['deleted'] == 1 && P_SHOWDELETED == 0 )
     message ( 'Du bist nicht berechtigt diesen Thread zu &ouml;ffnen.', 'Rechte', 0 );
 // Mini global -----------------------------------------------
 if( $config['ministat'] == 1 && $config['mini_global'] == 1 )
 {
     $r_global = db_query("SELECT
         posts,
	     posts_del
     FROM ".$pref."stats");
	 $a_global = db_result( $r_global );
	 mysql_free_result( $r_global );
 }
 // nav_path staff --------------------------------------------
 $r_board = db_query("SELECT
     board_id,
	 board_name,
	 category,
	 posts,
	 posts_del
 FROM ".$pref."board WHERE board_id='$thread[board_id]'");
 $board = db_result( $r_board );
 mysql_free_result( $r_board );
 if( strlen( $board['board_name'] ) > 50 )
     $board['board_name'] = substr( $board['board_name'], 0, 47 ).'...';
 // ------------- category ----------------------------
 $r_category = db_query("SELECT
     category_id,
	 category_name
 FROM ".$pref."category WHERE category_id='$board[category]'");
 $category = db_result( $r_category );
 mysql_free_result( $r_category );
 if ( strlen( $category['category_name'] ) > 30 )
     $category['category_name'] = substr( $category['category_name'], 0, 27 ).'...';
 // posts -------------------------------------------------
 $count = $thread['replies']+1;
 if ( P_SHOWDELETED == 1 )
     $count += $thread['replies_del'];
 // pages 
 $pages = $count/$config['post_rows'];
 $pages = bcadd( $pages, 0, 0 );
 if( bcmul ( $pages, $config['post_rows'], 0 ) < $count )
     $pages++;	 
 // thread_nav 
 $data['thread_nav'] = check_pages( $count, $config['post_rows'], $page, 1, 'showtopic.php?threadid='.$threadid.'&boardid='.$boardid );
 // define LIMIT
 if( $page == 'last' )
     $page = $pages;
 $page  = intval( $page );
 if( $page<1 )
     $page = 1;
 $start = ($page-1)*$config['post_rows'];
 $limit = $start.', '.$config['post_rows'];
	 
 $post_num = ($page-1)*$config['post_rows'];
 $session_var_name = 'b'.$boardid;
 
 $r_post = db_query("SELECT
     post_id,
	 user_id,
	 post_time,
	 post_text,
	 guest_name,
	 post_ip,
	 bcode,
	 post_smilies,
	 last_edit_by,
	 last_edit_time,
	 last_edit_ip,
	 edit_count,
	 deleted
 FROM ".$pref."post WHERE thread_id='$threadid' LIMIT ".$limit." ");
 if( db_rows( $r_post ) == 0 )
     $posts = '<tr><td colspan="2" class="cellb"><center>Keine Posts in diesem Thread</center></td></tr>'; 
 else
 {
     // update threadviews
	 $thread['thread_views']++;
	 db_query("UPDATE ".$pref."thread SET
	     thread_views='".$thread['thread_views']."'
	 WHERE thread_id='$threadid'");
	 // Ränge
	 $rank = '';
	 if( $config['ranks'] == 1 )
	 {
	     $r_rank = db_query("SELECT
		     *
		 From ".$pref."ranks ORDER BY post_counts DESC");
		 if( db_result( $r_rank ) > 0 )
		 {
		     $rank = array();
		     while( $a_rank = db_result( $r_rank ) )
			 {
			     $rank[] = array( 'count' => $a_rank['post_counts'],
				                  'title' => ( $a_rank['rankimage'] != '' ? '<img src="'.$a_rank['rankimage'].'" border="0" />' : $a_rank['ranktitle'] )
					        	);
			 }
			 mysql_free_result( $r_rank );
			 unset( $a_rank );
		 }
	 } 
	 $mini_user = array();
	 $mini_stats = array();
     $posts = '';
	 
     while( $post = db_result( $r_post ) )
	 {
	     if( $post['deleted'] == 1 && P_SHOWDELETED == 0 )
		     continue;
		 // anker --------
		 $anker = '<a name="p'.$post['post_id'].'">';	 
		 // post_num -----------
         $post_num_string = $post_num;
         if( strlen( $post_num ) < 3 )
         {
             for( $x=0; $x<3-strlen( $post_num ); $x++ )
                 $post_num_string = '0'.$post_num_string;
         }
		 $post_num++;
		 // ip
		 $ip = '';
		 if( P_IP == 1 )
		     $ip = 'IP:&nbsp;'.$post['post_ip'];
		 // gast? user?
		 $gast = 0;
		 if( $post['user_id'] == 0 )
		     $gast = 1;
		 else
		 {
		     $r_user = db_query("SELECT 
			     user_name,
				 user_title,
				 user_avatar,
				 user_hp,
				 user_join,
				 signatur,
				 post_count,
				 show_sig
			 FROM ".$pref."user WHERE user_id='".$post['user_id']."'");
			 if( db_rows( $r_user ) != 1 )
			     $gast = 1;
			 else
			 {
			     $user = db_result( $r_user );
				 mysql_free_result( $r_user );
			 }
		 }
		 $threadautor = '';
		 // user exist
		 if( $gast == 0 )
		 {
		     // user_name
			 $user_name = $user['user_name'];
			 // title
		     $user_title = ''; 
			 if( $user['user_title'] != '' )
			     $user_title = $user['user_title'];
			 else
			 {
			 // Ränge
				 if( $config['ranks'] == 1 )
				 {
				     if( is_array( $rank ) )
					 {
				         foreach( $rank as $value )
					     {
					         if( $user['post_count'] > $value['count'] )
						     {
						         $user_title = $value['title'];
							     break;
						     }
						 }   
					 }    
				 }
			 }
			 // avatar
			 $avatar = '';
			 if( $user['user_avatar'] != '' && $user['user_avatar'] != 'nicht erlaubt' ) // prüffen?
			 {
			     $http = '';
				 if( $user['user_hp'] != '' && $user['user_hp'] != 'http://' ) // prüffen?
				     $http = '<a href="'.$user['user_hp'].'">';
				 $avatar = $http.'<img src="'.$user['user_avatar'].'" border="0" />'.( $http != '' ? '</a>' : '' );
			 }
		     // statistik -----------------------------------------
		     if( $config['ministat'] == 1 )
		     {
			     $statik = '[smallfont]Userstatistik:[smallfontend]'; 
		         if( !in_array( $post['user_id'], $mini_user ) )
				 {
			         $mini_user[] = $post['user_id'];
					 $mini_stats[] = $user['post_count'];
				 }
			     if( $config['mini_global'] == 1 )
				 {
				     $statik .= '[mini_global'.$post['user_id'].']';
				 }
				 if( $config['mini_thread'] == 1 )
				 {
				     $statik .= '[mini_thread'.$post['user_id'].']';
				 }
		     }
			 else
			     $statik = '';
			 // join
			 $join = datum( $user['user_join'] );
			 // threadautor
		     if( $thread['autor_id'] == $post['user_id'] )
			     $threadautor = 'Threadautor';
		     // signatur
		     $signatur = '';
		     if( $user['signatur'] != '' && $user['show_sig'] == 1 )
		         $signatur = '<br />--------<br />'.parse_code($user['signatur'], 1, 1, 1, 1 );
		     // useroptionen
			 $options = '&nbsp;<a href="s_profile.php?username='.$user['user_name'].'">'.( $style['profileimage'] != '' ? '<img src="'.$style['profileimage'].'" border="0" />' : 'Profil' ).'</a>&nbsp;||';
			 if( $config['pm'] == 1 )
			     $options .= '&nbsp;<a href="pm.php?username='.$user['user_name'].'">'.( $style['messageimage'] != '' ? '<img src="'.$style['messageimage'].'" border="0" />' : 'Privat message' ).'</a>&nbsp;||';
		     $options .= '&nbsp;<a href="search.php?username='.$user['user_name'].'">'.( $style['searchimage'] != '' ? '<img src="'.$style['searchimage'].'" border="0" />' : 'Suchen' ).'</a>';
		 }
		 // guest post
		 else
		 {
		     // guest_name
		     $user_name = addslashes($post['guest_name']);
			 // title
			 $user_title = 'Gast';
			 // avatar
			 $avatar = '';
			 if( $config['guest_avatar'] != '' )
			     $avatar = '<img src="'.$config['guest_avatar'].'" border="0" />';
			 // statistik
			 $statik = '';
			 // join
			 $join = 'Nicht registriert';
			 // signatur 
			 $signatur = '';
			 // optionen
			 $options = '';
		 }
		 // post smilies
		 $smilies = 0;
		 if( $post['post_smilies'] == 1 && $config['smilies'] == 1 )
		     $smilies = 1;
		 // editline
		 $editline = '';
		 if( $post['edit_count'] > 0 )
		 {
		     $editline = '<hr style="height:1px color:[color1]">Dieses Posting wurde '.$post['edit_count'].' mal Editiert<br />';
			 $editline .= 'Das letzte mal von <b>'.$post['last_edit_by'].'</b>. Datum: '.datum( $post['last_edit_time'] );
			 $editline .= ( P_IP == 1 ? ' IP: '.$post['last_edit_ip'] : '' );
		 }	 
		 // icon
		 $icon = '';
		 if( $post['deleted'] == 1 )
		     $icon = 'templates/'.$style['styletemplate'].'/images/delete.gif';
		 else
		 {
		     $new = '';
			 if( U_ID != 0 )
			 {
			     if( $post['post_id'] > $_SESSION[$session_var_name] )
			         $new = '_new';
			 }
			 $icon = 'templates/'.$style['styletemplate'].'/images/icon/fullalpha'.$new.'.gif';
		 }
		 // verarbeitungsoptionen
		 $more = '';
		 $ownpost = 0;
		 if( U_ID != 0 )
		 {
		     if( U_ID == $post['user_id'] )
			     $ownpost = 1;
		 }
		 if( $post['deleted'] == 0 )
		 {
		     if( P_REPLY )
		         $more = '<a href="reply.php?action=quote&threadid='.$threadid.'&postid='.$post['post_id'].'&boardid='.$boardid.'&page='.$page.'">'.( $style['quoteimage'] != '' ? '<img src="'.$style['quoteimage'].'" border="0" />' : 'Zitatantwort' ).'</a>&nbsp;';
		     if( ( $ownpost == 1 && P_EDIT ) || ( $ownpost == 0 && P_OEDIT ) )
		         $more .= ( $more == '' ? '' : '||&nbsp;' ).'<a href="edit.php?action=edit&threadid='.$threadid.'&postid='.$post['post_id'].'&boardid='.$boardid.'&page='.$page.'">'.( $style['editimage'] != '' ? '<img src="'.$style['editimage'].'" border="0" />' : 'Editieren' ).'</a>&nbsp;';
		     if( ( $ownpost == 1 && P_DELPOST ) || ( $ownpost == 0 && P_ODELPOST ) )
		         $more .= ( $more == '' ? '' : '||&nbsp;' ).'<a href="edit.php?action=delete&threadid='.$threadid.'&postid='.$post['post_id'].'&boardid='.$boardid.'&page='.$page.'">'.( $style['deleteimage'] != '' ? '<img src="'.$style['deleteimage'].'" border="0" />' : 'L&ouml;schen' ).'</a>&nbsp;';
		 }
		 // noch mal optionen
		 if( U_ID != 0 && $config['report'] == 1 )
		 {
		     $options .= ( $options == '' ? '' : '&nbsp;||&nbsp;' ).'<a href="postopt.php?action=report&postid='.$post['post_id'].'">'.( $style['reportimage'] != '' ? '<img src="'.$style['reportimage'].'" border="0" />' : 'Melden' ).'</a>&nbsp;';    
		 }
		 
		 // TEMPLATE 
	     $row = $TPostrow;
		 $row = str_replace( '[anker]', $anker, $row );
		 $row = str_replace( '[post_num]', $post_num_string, $row );
		 $row = str_replace( '[post_time]', datum( $post['post_time'] ), $row );
		 $row = str_replace( '[user_name]', $user_name, $row );
		 $row = str_replace( '[user_title]', $user_title, $row );
		 $row = str_replace( '[avatar]', $avatar, $row );
		 $row = str_replace( '[stat]', $statik, $row );
		 $row = str_replace( '[join_time]', $join, $row );
		 $row = str_replace( '[threadautor]', $threadautor, $row );
		 $row = str_replace( '[ip]', $ip, $row );
         $row = str_replace( '[post]', parse_code($post['post_text'], 1, 1, $post['bcode'], $smilies ), $row );
		 $row = str_replace( '[signatur]', $signatur, $row );
		 $row = str_replace( '[editline]', $editline, $row );
		 $row = str_replace( '[new]', '<img src="'.$icon.'" border="0"  align="left" />', $row );
		 $row = str_replace( '[options]', $options, $row );
		 $row = str_replace( '[more_options]', $more, $row );
		 $posts .= $row;
	 }
	 // statik ------------
	 if( $config['ministat'] == 1 )
	 {
	     foreach( $mini_user as $key=>$value )
	     {
	         if( $config['mini_global'] == 1 )
				 $posts = str_replace( '[mini_global'.$value.']', createStat( 'Global', $a_global['posts'], $a_global['posts_del'], $mini_stats[$key], P_IP ), $posts ); 
			 if( $config['mini_thread'] == 1 )
			 {
			     $r_this_user = db_query("SELECT
				     count(post_id)
				 FROM ".$pref."post WHERE thread_id='$threadid' AND user_id='$value'");
				 $a_this_user = db_result( $r_this_user );
				 mysql_free_result( $r_this_user );
				 list(, $post_count ) = each( $a_this_user );
				 $replies = $thread['replies']+1;
				 if( $thread['deleted'] == 1 )
				     $replies = 0;
				 $posts = str_replace( '[mini_thread'.$value.']', createStat( 'Thread', $replies, ( $thread['deleted'] == 1 ? $thread['replies_del']+1 : $thread['replies_del'] ), ( P_SHOWDELETED == 0 && $post_count > $replies ? $post_count-$thread['replies_del'] : $post_count ), P_IP ), $posts ); 			     
			 } 
	     }
	 }
 } 
     
 $data['posts'] = $posts;
 $data['jump'] = Jump( $pref );
 $data['b_id'] = $boardid;
 $data['t_id'] = $threadid;
 $data['page'] = $page;
 $data['zeichen'] = '';
 if( P_SHOWDELETED == 1 )
     $data['zeichen'] = '&nbsp;<img src="templates/[styletemplate]/images/delete.gif" />&nbsp;
       Post ist als gel&ouml;scht makiert.<br />';
 $data['thread_permission'] = 'nein';
 if( P_REPLY )
     $data['thread_permission'] = 'ja';
 $data['doreply'] = '';
 if( ( $thread['thread_closed'] == 0 && P_REPLY ) || ( $thread['thread_closed'] == 1 && P_REPLY && P_EDITCLOSED ) )
 {
     $data['doreply'] = '<b>[ <a href="reply.php?action=reply&threadid='.$threadid.'&boardid='.$boardid.'" class="bg">Antworten</a> ]</b>&nbsp;<br>';
	 if( $style['replyimage'] != '' )
	     $data['doreply'] = '<a href="reply.php?action=reply&threadid='.$threadid.'&boardid='.$boardid.'" class="bg">'.$style['replyimage'].'</a>&nbsp;<br>';
 }
 // nav_path ----------------------------------------------------
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="category.php?catid='.$category['category_id'].'" class="bg">'.$category['category_name'].'</a>';
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="board.php?boardid='.$board['board_id'].'" class="bg">'.$board['board_name'].'</a>';
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;'.$thread['thread_topic'];
 $data['small_nav'] = '<b>[ <a href="board.php?boardid='.$board['board_id'].'" class="head">'.$board['board_name'].'</a> ]</b>';
 $data['boardtable'] = Template( $TPosttab );
 echo Output( Template ( $TBoard ) );
?>