<?php
 /* $Id: reply.php,v 1.3 2003/06/20 10:39:39 master_mario Exp $ */
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
 include( 'inc/tagbar.inc.php' );
 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 
 // Flood protection
 if( U_LAST != 0 && U_LAST+$config['flood_protection'] > $board_time )
     message ( 'Du kannst nur alle '.$config['flood_protection'].' Sekunden einen neuen Beitrag senden.', 'Flood protection', 0 );

 // replyForm --------------------
 function replyForm( $post, $boardid, $threadid, $postid, $abbonieren, $smi, $code, $method )
 {
	 global $style, $config, $tagbar;
     $TReply = Get_Template( 'templates/'.$style['styletemplate'].'/reply.html' );
	 
     $formula = '<form action="reply.php" method="post" name="form">
	  <input type="hidden" name="send" value="1" />';
	 switch( $method )
	 {
	     case 0:
		 {
		     $formula .= '<input type="hidden" name="action" value="new" />
			              <input type="hidden" name="boardid" value="'.$boardid.'" />';
			 $topic = '<tr><td class="cella">&nbsp;Topic</td>
			 <td class="cellb"><input type="text" size="60" maxlength="'.$config['max_topic_len'].'" name="new[topic]" id="border-tab" value="'.( isset($post['topic']) ? $post['topic'] : '' ).'" /></td></tr>';
			 $icon = Get_Template ( 'templates/'.$style['styletemplate'].'/icon.html' ); 
			 $icon = str_replace( '[icon_list]', Create_Smillist( ( isset($post['icon']) ? $post['icon'] : 'fullalpha' ) ), $icon );
			 $poll = '';
			 $top = 'Neuer Thread';
			 if( U_ID == 0 )
			     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" id="border-tab" value="'.( isset($post['aztor']) ? $post['autor'] : '' ).'" />';
			 else
			     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" value="'.U_NAME.'" readonly id="border-tab" />';
		          
			 if( isset( $post['text'] ) )
			     $text = $post['text'];
			 else
			     $text = '';
		 }
		 break;
		 case 1:
		 {
		     $formula .= '<input type="hidden" name="action" value="reply" />
			              <input type="hidden" name="boardid" value="'.$boardid.'" />
						  <input type="hidden" name="threadid" value="'.$threadid.'" />';
			 $topic = '';
			 $icon = '';
			 $poll = '';
			 $top = 'Reply';
			 if( U_ID == 0 )
			     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" id="border-tab" value="'.( isset($post['aztor']) ? $post['autor'] : '' ).'" />';
			 else
			     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" value="'.U_NAME.'" readonly id="border-tab" />';
		          
			 if( isset( $post['text'] ) )
			     $text = $post['text'];
			 else
			     $text = '';
		 }
		 case 2:
		 {
		     $formula .= '<input type="hidden" name="action" value="quote" />
			              <input type="hidden" name="boardid" value="'.$boardid.'" />
						  <input type="hidden" name="threadid" value="'.$threadid.'" />
						  <input type="hidden" name="postid" value="'.$postid.'" />
						  <input type="hidden" name="page" value="'.$post['page'].'" />';
			 $topic = '';
			 $icon = '';
			 $poll = '';
			 $top = 'Reply';
			 if( U_ID == 0 )
			     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" id="border-tab" value="'.( isset($post['aztor']) ? $post['autor'] : '' ).'" />';
			 else
			     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" value="'.U_NAME.'" readonly id="border-tab" />';
		          
			 if( isset( $post['text'] ) )
			     $text = $post['text'];
			 else
			     $text = '';
		 }
	 }	 
	 switch( $smi )
	 {
	     case 0:
		     $smilies = 'Aus'; $smil_yes = ''; break;
		 case 1:
		 {
		     $smilies = 'An'; $smil_yes = ' checked';
		     if( isset( $post['smil'] ) )
			 {
			     if( $post['smil'] == 0 ) $smil_yes = '';
			 }
		 }
	 }
	 $abbo_yes = '';
	 if( isset($post['abbo']) )
	 {
	     if( $post['abbo'] == 1 ) $abbo_yes = ' checked';
	 }
	 switch( $abbonieren )
	 {
	     case 0:
		     $mail = 'Aus'; break;
		 case 1:
		     $mail = 'An';
	 }
	 if( isset( $post['code'] ) ) $code = $post['code'];
	 switch( $code )
	 {
	     case 0:
		     $bcode_yes = ''; break;
		 case 1:
		     $bcode_yes = ' checked';
	 }
	 
	 $TReply = str_replace( '[formula]', $formula, $TReply );
	 $TReply = str_replace( '[topic]', $topic, $TReply );
	 $TReply = str_replace( '[icon]', $icon, $TReply );
	 $TReply = str_replace( '[poll]', $poll, $TReply );
	 $TReply = str_replace( '[top]', $top, $TReply );
	 $TReply = str_replace( '[autor]', $autor, $TReply );
	 $TReply = str_replace( '[tagbar]', $tagbar, $TReply );
	 $TReply = str_replace( '[text]', $text, $TReply );
	 $TReply = str_replace( '[smilies]', $smilies, $TReply );
	 $TReply = str_replace( '[mail]', $mail, $TReply );
	 $TReply = str_replace( '[abbo_yes]', $abbo_yes, $TReply );
	 $TReply = str_replace( '[smil_yes]', $smil_yes, $TReply );
	 $TReply = str_replace( '[bcode_yes]', $bcode_yes, $TReply );
	 
     return $TReply;
 }
 // CODE -------------------------------
 if( !isset( $boardid ) )
     message ( 'Sorry! Dieses Board gibt es nicht.', 'Fehler', 0 );
 // board
 $r_board = db_query("SELECT
     board_name,
     category,
	 threads,
	 posts
 FROM ".$pref."board WHERE board_id='$boardid'"); 
 if( db_rows( $r_board ) != 1 )
     message ( 'Sorry! Dieses Board gibt es nicht.', 'Fehler', 0 );
 $board = db_result( $r_board );
 // category
 $r_category = db_query("SELECT
     category_id,
	 category_name
 FROM ".$pref."category WHERE category_id='$board[category]'");
 $category = db_result( $r_category );
 // nav_path
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="category.php?catid='.$category['category_id'].'" 
 class="bg">'.( strlen($category['category_name']) > 30 ? substr ( $category['category_name'], 0, 27).'...' : $category['category_name'] ).'</a>';
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="board.php?boardid='.$boardid.'" 
 class="bg">'.( strlen($board['board_name']) > 50 ? substr ( $board['board_name'], 0, 47).'...' : $board['board_name'] ).'</a>';
 // thread
 if( $action != 'new' )
 {
     $r_thread = db_query("SELECT
         thread_id,
	     thread_topic,
		 thread_closed,
		 replies
     FROM ".$pref."thread WHERE thread_id='$threadid' AND board_id='$boardid'");
	 if( db_rows( $r_thread ) != 1 )
         message ( 'Sorry! Fehlerhafter Link.', 'Fehler', 0 );
	 $thread = db_result( $r_thread );
	 $new['topic'] = $thread['thread_topic'];
     $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;<a href="showtopic.php?boardid='.$boardid.'&threadid='.$threadid.'" 
     class="bg">'.$thread['thread_topic'].'</a>';
 }
 // actions ----------------------------
 // new Thread
 if( $action == 'new' )
 {
	 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Neuen Thread anlegen';
	 if( !P_POSTNEW )
	     message ( 'Sorry! Du bist nicht berechtigt hier neue Threads anzulegen.', 'Fehler', 0 );
	 if( !isset( $send ) )
	 {
	     if( !isset( $back ) )
	         $data['boardtable'] = replyForm( array(), $boardid, 0, 0, $config['mail_func'], $config['smilies'], 1, 0 );
		 else
	         $data['boardtable'] = replyForm( $new, $boardid, 0, 0, $config['mail_func'], $config['smilies'], $new['code'], 0 );
	 }
	 else
	 {
	     // check entrys -----------------------
		 $err_mess = '';
		 if( U_ID == 0 )
		 {
		     $err_mess = check_string( $new['autor'], 0 );			 
		 }
		 $err_mess = check_string( $new['topic'], 1 );
		 if( strlen( $text ) < $config['min_post_len'] )
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz.';
		 if( strlen( $text ) > $config['max_post_len'] )
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang.';
		 if( $err_mess != '' )
		 {
		     $mess = '<form action="reply.php" method="post" name="sendback">
			  '.$err_mess.'
			 <input type="hidden" name="boardid" value="'.$boardid.'" />
			 <input type="hidden" name="action" value="new" />
			 <input type="hidden" name="back" value="1" />
			 <input type="hidden" name="new[autor]" value="'.$new['autor'].'" />
			 <input type="hidden" name="new[topic]" value="'.$new['topic'].'" />
			 <input type="hidden" name="new[icon]" value="'.$icon.'" />
			 <input type="hidden" name="new[text]" value="'.$text.'" />
			 <input type="hidden" name="new[abbo]" value="'.( isset($abbo) ? '1' : '0' ).'" />
			 <input type="hidden" name="new[smil]" value="'.( isset($do_smilies) ? '1' : '0' ).'" />
			 <input type="hidden" name="new[code]" value="'.( isset($b_code) ? '1' : '0' ).'" />
			 </form>';
		     message ( $mess, 'Fehler', 1 );
		 }
		 else
		 {
		     // add thread ---------------------------
			 if( U_ID != 0 )
			     $autor = U_NAME;
			 else
			     $autor = $config['guest_pref'].$new['autor'];
			 $addtime = $board_time;
		     // write threaddata
		     db_query("INSERT INTO ".$pref."thread SET
		         board_id='$boardid',
			     create_time='$addtime',
			     thread_autor='$autor',
			     autor_id='".U_ID."',
			     thread_topic='".addslashes($new['topic'])."',
			     last_act_time='$addtime',
			     last_act_user='$autor',
			     thread_icon='".$icon."'");
             // thread_id
             $threadid = mysql_insert_id();
			 // write postdata
			 db_query("INSERT INTO ".$pref."post SET
			     user_id='".U_ID."',
				 post_time='$addtime',
				 post_text='".addslashes($text)."',
				 guest_name='".( U_ID == 0 ? $autor : '' )."',
				 thread_id='$threadid',
				 board_id='$boardid',
				 post_ip='".getenv('REMOTE_ADDR')."',
				 post_smilies='".( isset($do_smilies) ? '1' : '0' )."',
				 bcode='".( isset($b_code) ? '1' : '0' )."',
				 sendmail='".( isset($abbo) ? '1' : '0' )."'"); 
			 // update thread (last_post_id)
			 $last_post_id = mysql_insert_id();
			 db_query("UPDATE ".$pref."thread SET
			     last_post_id='$last_post_id'
			 WHERE thread_id='$threadid'");
			 // boarddata
			 $threads = $board['threads']+1;
			 $posts = $board['posts']+1;
		     db_query("UPDATE ".$pref."board SET
			     last_act_time='$addtime',
				 last_post_id='$last_post_id',
				 last_thread_id='$threadid',
				 last_act_user='$autor',
				 last_act_thread='".addslashes($new['topic'])."',
				 threads='$threads',
				 posts='$posts'
			 WHERE board_id='$boardid'");
			 // update userdata
			 if( U_ID != 0 )
			 {
			     $post_count = U_COUNT+1;
			     db_query("UPDATE ".$pref."user SET
				     user_lastacttime='$addtime',
					 post_count='$post_count',
					 user_lasttopic='".addslashes($new['topic'])."',
					 user_lastpostt='$addtime',
					 user_lastpostid='$last_post_id'
				 WHERE user_id='".U_ID."'");
			 }
			 // statiks
			 $r_stats = db_query("SELECT
			     posts,
				 threads
			 FROM ".$pref."stats");
			 $stats = db_result( $r_stats );
			 $stats['posts']++;
			 $stats['threads']++;
			 db_query("UPDATE ".$pref."stats SET
			     posts='".$stats['posts']."',
				 threads='".$stats['threads']."'");
			 // last_act_time
			 if( U_ID == 0 )
			 {
			     db_query("UPDATE ".$pref."guest SET
				     last_act_time='$addtime'
				 WHERE session_id='$sid'");
			 }
			 else
			 {
			     db_query("UPDATE ".$pref."user SET
				     user_lastpostt='$addtime'
				 WHERE user_id='".U_ID."'");
			 }
			 message_redirect('Danke f&uuml;r Deinen Beitrag, bitte warten ...', 'showtopic.php?boardid='.$boardid.'&threadid='.$threadid );
		 }
	 }
 }
 // quote
 if( $action == 'quote' )
 {
	 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Zitatantwort';
	 if( ( $thread['thread_closed'] == 0 && P_REPLY ) || ( $thread['thread_closed'] == 1 && P_REPLY && P_EDITCLOSED ) )
	 {}
	 else
	     message ( 'Sorry! Du bist nicht berechtigt in geschlossenen Threads zu posten.', 'Rechte', 0 );
	 if( !isset( $send ) )
	 {
	     if( !isset( $back ) )
		 {
	         $r_post = db_query("SELECT
		         post_text,
			     user_id,
			     guest_name
		     FROM ".$pref."post WHERE post_id='$postid'");
		     $a_post = db_result( $r_post );
		     if( $a_post['user_id'] == 0 )
                  $new['text'] = '[quote][b]'.$a_post['guest_name'].' postete[/b]
'.$a_post['post_text'].'[/quote]';
		     else
		     {
                  $r_userr = db_query("SELECT
                      user_name
                  FROM ".$pref."user WHERE user_id='$a_post[user_id]'");
                  $a_userr = db_result( $r_userr );
                  $new['text'] = '[quote][b]'.$a_userr['user_name'].' postete[/b]
'.$a_post['post_text'].'[/quote]';
		     }
			 $new['page'] = $page;
	         $data['boardtable'] = replyForm( $new, $boardid, $threadid, $postid, $config['mail_func'], $config['smilies'], 1, 2 );
		 }
		 else
	         $data['boardtable'] = replyForm( $new, $boardid, $threadid, 0, $config['mail_func'], $config['smilies'], $new['code'], 2 );
	 }
	 else
	 {
	     // check entrys -----------------------
		 $err_mess = '';
		 if( U_ID == 0 )
		 {
		     $err_mess = check_string( $new['autor'], 0 );			 
		 }
		 if( strlen( $text ) < $config['min_post_len'] )
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz.';
		 if( strlen( $text ) > $config['max_post_len'] )
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang.';
		 if( $err_mess != '' )
		 {
		     $mess = '<form action="reply.php" method="post" name="sendback">
			  '.$err_mess.'
			 <input type="hidden" name="boardid" value="'.$boardid.'" />
			 <input type="hidden" name="threadid" value="'.$threadid.'" />
			 <input type="hidden" name="postid" value="'.$postid.'" />
			 <input type="hidden" name="new[page]" value="'.$page.'" />
			 <input type="hidden" name="action" value="quote" />
			 <input type="hidden" name="back" value="1" />
			 <input type="hidden" name="new[autor]" value="'.$new['autor'].'" />
			 <input type="hidden" name="new[text]" value="'.$text.'" />
			 <input type="hidden" name="new[abbo]" value="'.( isset($abbo) ? '1' : '0' ).'" />
			 <input type="hidden" name="new[smil]" value="'.( isset($do_smilies) ? '1' : '0' ).'" />
			 <input type="hidden" name="new[code]" value="'.( isset($b_code) ? '1' : '0' ).'" />
			 </form>';
		     message ( $mess, 'Fehler', 1 );
         }
		 else
		 {
		     // add post ---------------------------
			 if( U_ID != 0 )
			     $autor = U_NAME;
			 else
			     $autor = $config['guest_pref'].$new['autor'];
			 $addtime = $board_time;
			 // write postdata
			 db_query("INSERT INTO ".$pref."post SET
			     user_id='".U_ID."',
				 post_time='$addtime',
				 post_text='".addslashes($text)."',
				 guest_name='".( U_ID == 0 ? $autor : '' )."',
				 thread_id='$threadid',
				 board_id='$boardid',
				 post_ip='".getenv('REMOTE_ADDR')."',
				 post_smilies='".( isset($do_smilies) ? '1' : '0' )."',
				 bcode='".( isset($b_code) ? '1' : '0' )."',
				 sendmail='".( isset($abbo) ? '1' : '0' )."'"); 
			 // update thread --------------------------------------
			 $last_post_id = mysql_insert_id();
			 $replies = $thread['replies']+1;
			 db_query("UPDATE ".$pref."thread SET
			     last_act_time='$addtime',
			     last_act_user='$autor',
			     last_post_id='$last_post_id',
				 replies='$replies'
			 WHERE thread_id='$threadid'");
			 // boarddata
			 $posts = $board['posts']+1;
		     db_query("UPDATE ".$pref."board SET
			     last_act_time='$addtime',
				 last_post_id='$last_post_id',
				 last_thread_id='$threadid',
				 last_act_user='$autor',
				 last_act_thread='".addslashes($new['topic'])."',
				 posts='$posts'
			 WHERE board_id='$boardid'");
			 // update userdata
			 if( U_ID != 0 )
			 {
			     $post_count = U_COUNT+1;
			     db_query("UPDATE ".$pref."user SET
				     user_lastacttime='$addtime',
					 post_count='$post_count',
					 user_lasttopic='".addslashes($new['topic'])."',
					 user_lastpostt='$addtime',
					 user_lastpostid='$last_post_id'
				 WHERE user_id='".U_ID."'");
			 }
			 // statiks
			 $r_stats = db_query("SELECT
			     posts
			 FROM ".$pref."stats");
			 $stats = db_result( $r_stats );
			 $stats['posts']++;
			 db_query("UPDATE ".$pref."stats SET
			     posts='".$stats['posts']."'");
			 // last_act_time
			 if( U_ID == 0 )
			 {
			     db_query("UPDATE ".$pref."guest SET
				     last_act_time='$addtime'
				 WHERE session_id='$sid'");
			 }
			 else
			 {
			     db_query("UPDATE ".$pref."user SET
				     user_lastpostt='$addtime'
				 WHERE user_id='".U_ID."'");
			 }
			 message_redirect('Danke f&uuml;r Deinen Beitrag, bitte warten ...', 'showtopic.php?boardid='.$boardid.'&threadid='.$threadid.'&page='.$page.'#p'.$last_post_id );
		 }
     }
 }
 // antworten
 if( $action == 'reply' )
 {
	 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Antworten';
	 if( ( $thread['thread_closed'] == 0 && P_REPLY ) || ( $thread['thread_closed'] == 1 && P_REPLY && P_EDITCLOSED ) )
	 {}
	 else
	     message ( 'Sorry! Du bist nicht berechtigt in diesem Thread zu posten.', 'Rechte', 0 );
	 if( !isset( $send ) )
	 {
	     if( !isset( $back ) )
	         $data['boardtable'] = replyForm( $new, $boardid, $threadid, 0, $config['mail_func'], $config['smilies'], 1, 1 );
		 else
	         $data['boardtable'] = replyForm( $new, $boardid, $threadid, 0, $config['mail_func'], $config['smilies'], $new['code'], 1 );
	 }
	 else
	 {
	     // check entrys -----------------------
		 $err_mess = '';
		 if( U_ID == 0 )
		 {
		     $err_mess = check_string( $new['autor'], 0 );			 
		 }
		 if( strlen( $text ) < $config['min_post_len'] )
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz.';
		 if( strlen( $text ) > $config['max_post_len'] )
		     $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang.';
		 if( $err_mess != '' )
		 {
		     $mess = '<form action="reply.php" method="post" name="sendback">
			  '.$err_mess.'
			 <input type="hidden" name="boardid" value="'.$boardid.'" />
			 <input type="hidden" name="threadid" value="'.$threadid.'" />
			 <input type="hidden" name="action" value="reply" />
			 <input type="hidden" name="back" value="1" />
			 <input type="hidden" name="new[autor]" value="'.$new['autor'].'" />
			 <input type="hidden" name="new[text]" value="'.$text.'" />
			 <input type="hidden" name="new[abbo]" value="'.( isset($abbo) ? '1' : '0' ).'" />
			 <input type="hidden" name="new[smil]" value="'.( isset($do_smilies) ? '1' : '0' ).'" />
			 <input type="hidden" name="new[code]" value="'.( isset($b_code) ? '1' : '0' ).'" />
			 </form>';
		     message ( $mess, 'Fehler', 1 );
		 }
		 else
		 {
		     // add post ---------------------------
			 if( U_ID != 0 )
			     $autor = U_NAME;
			 else
			     $autor = $config['guest_pref'].$new['autor'];
			 $addtime = $board_time;
			 // write postdata
			 db_query("INSERT INTO ".$pref."post SET
			     user_id='".U_ID."',
				 post_time='$addtime',
				 post_text='".addslashes($text)."',
				 guest_name='".( U_ID == 0 ? $autor : '' )."',
				 thread_id='$threadid',
				 board_id='$boardid',
				 post_ip='".getenv('REMOTE_ADDR')."',
				 post_smilies='".( isset($do_smilies) ? '1' : '0' )."',
				 bcode='".( isset($b_code) ? '1' : '0' )."',
				 sendmail='".( isset($abbo) ? '1' : '0' )."'"); 
			 // update thread --------------------------------------
			 $last_post_id = mysql_insert_id();
			 $replies = $thread['replies']+1;
			 db_query("UPDATE ".$pref."thread SET
			     last_act_time='$addtime',
			     last_act_user='$autor',
			     last_post_id='$last_post_id',
				 replies='$replies'
			 WHERE thread_id='$threadid'");
			 // boarddata
			 $posts = $board['posts']+1;
		     db_query("UPDATE ".$pref."board SET
			     last_act_time='$addtime',
				 last_post_id='$last_post_id',
				 last_thread_id='$threadid',
				 last_act_user='$autor',
				 last_act_thread='".addslashes($new['topic'])."',
				 posts='$posts'
			 WHERE board_id='$boardid'");
			 // update userdata
			 if( U_ID != 0 )
			 {
			     $post_count = U_COUNT+1;
			     db_query("UPDATE ".$pref."user SET
				     user_lastacttime='$addtime',
					 post_count='$post_count',
					 user_lasttopic='".addslashes($new['topic'])."',
					 user_lastpostt='$addtime',
					 user_lastpostid='$last_post_id'
				 WHERE user_id='".U_ID."'");
			 }
			 // statiks
			 $r_stats = db_query("SELECT
			     posts
			 FROM ".$pref."stats");
			 $stats = db_result( $r_stats );
			 $stats['posts']++;
			 db_query("UPDATE ".$pref."stats SET
			     posts='".$stats['posts']."'");
			 // last_act_time
			 if( U_ID == 0 )
			 {
			     db_query("UPDATE ".$pref."guest SET
				     last_act_time='$addtime'
				 WHERE session_id='$sid'");
			 }
			 else
			 {
			     db_query("UPDATE ".$pref."user SET
				     user_lastpostt='$addtime'
				 WHERE user_id='".U_ID."'");
			 }
			 message_redirect('Danke f&uuml;r Deinen Beitrag, bitte warten ...', 'showtopic.php?boardid='.$boardid.'&threadid='.$threadid.'&page=last#p'.$last_post_id );
		 }
	 }
 }
 echo Output( Template ( $TBoard ) );
?>