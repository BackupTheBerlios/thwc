<?php
 /* $Id: edit.php,v 1.3 2003/06/26 13:46:18 master_mario Exp $ */
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
 include ( 'inc/tagbar.inc.php' );
 
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 
 // editForm --------------------
 function editForm( $post, $boardid, $threadid, $postid, $abbonieren, $smi, $code )
 {
	 global $style, $config, $tagbar;
     $TReply = Get_Template( 'templates/'.$style['styletemplate'].'/reply.html' );

     $formula = '<form action="edit.php" method="post" name="form">
	              <input type="hidden" name="send" value="1" />
	              <input type="hidden" name="action" value="edit" />
	              <input type="hidden" name="boardid" value="'.$boardid.'" />
				  <input type="hidden" name="threadid" value="'.$threadid.'" />
				  <input type="hidden" name="postid" value="'.$postid.'" />
				  <input type="hidden" name="page" value="'.$post['page'].'" />';
	 $topic = '<input type="hidden" name="new[topic]" value="'.$post['topic'].'" />';
	 if( P_EDITTOPIC == 1 )
	     $topic = '<tr><td class="cella">&nbsp;Topic</td>
		  <td class="cellb"><input type="text" size="60" maxlength="'.$config['max_topic_len'].'" name="new[topic]" id="border-tab" value="'.$post['topic'].'" /></td></tr>';
	 $icon = '';
	 $poll = '';
	 $top = 'Edit';
	 if( $post['user_id'] == 0 )
	     $autor = '<input type="text" name="new[autor]" size="30" maxlength="'.$config['max_usernamelength'].'" id="border-tab" value="'.$post['guest_name'].'" />';
	 else
	     $autor = '<input type="hidden" name="new[autor]" value="" />&nbsp;[smallfont]registriertes Mitglied[smallfontend]';
	 $text = stripslashes(decodeX($post['text']));

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
	 $TReply = str_replace( '[max_len]', $config['max_post_len'], $TReply );
	 
     return $TReply;
 }
 // CODE
 if( !isset( $action ) )
     message ( 'Bitte w&auml;le eine Funktion aus.', 'Fehler', 0 );
	 
 $data['nav_path'] = board_nav( $boardid, $threadid, $data['nav_path'] );
 // edit ------------------------------------------------------------    
 if( $action == 'edit' )
 {
     // nav_path
     $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Editieren';
	 // threaddaten
	 $r_thread = db_query("SELECT
	     thread_topic,
		 thread_closed,
		 deleted
	 FROM ".$pref."thread WHERE thread_id='$threadid'");
	 $thread = db_result( $r_thread );
	 if( $thread['deleted'] == 1 )
	     message ( 'In gel&ouml;schten Threads kann nicht editiert werden.', 'Fehler', 0 );
	 // postdaten
	 $r_post = db_query("SELECT
	     user_id,
		 post_time,
		 guest_name,
		 post_text,
		 edit_count
	 FROM ".$pref."post WHERE post_id='$postid' AND thread_id='$threadid'");
	 if( db_rows( $r_post ) != 1 )
         message ( 'Sorry! Fehlerhafter Link.', 'Fehler', 0 );
	 else
	     $post = db_result( $r_post );	     
	 // edit limit ---------------
	 if( $config['edit_time_limit'] != 0 )
	 {
	     $elimit = $config['edit_time_limit']*( $config['e_time_art'] == 0 ? 3600 : 86400 );
		 if( $post['post_time']+$elimit > $board_time )
		 {
		     if( !U_ISMOD && !U_ISADMIN )
                 message ( 'Sorry! Das Zeitlimit f&uuml;r Edits ist leider &uuml;berschritten.', 'Rechte', 0 );
		 }
	 }
	 // eigenes Posting?
	 $own_post = 0;
	 if( U_ID != 0 && U_ID == $post['user_id'] )
	     $own_post = 1;
	 if( $thread['thread_closed'] == 1 && !P_EDITCLOSED )
	     message( 'Du bist nicht berechtigt in geschlossenen Threads zu editieren.', 'Rechte', 0 );
	 if( ( $own_post == 1 && P_EDIT ) || ( $own_post == 0 && P_OEDIT ) )
	 {
	     if( !isset( $send ) )
	     {
		     if( !isset( $back ) )
			 {
			     $new['thread_topic'] = $thread['thread_topic'];
				 $new['page'] = $page;
				 $new['user_id'] = $post['user_id'];
				 $new['guest_name'] = $post['guest_name'];
				 $new['text'] = $post['post_text'];
				 $new['topic'] = $thread['thread_topic'];
	             $data['boardtable'] = editForm( $new, $boardid, $threadid, $postid, $config['mail_func'], $config['smilies'], 1 );
			 }
			 else
			 {
				 $new['user_id'] = $post['user_id'];
	             $data['boardtable'] = editForm( $new, $boardid, $threadid, $postid, $config['mail_func'], $config['smilies'], $new['code'] );
			 }
	     }
	     else
	     {
	         // check entrys -----------------------
		     $err_mess = '';
			 if( $post['user_id'] == 0 )
			     $err_mess = check_string( $new['autor'], 0 );		
		     $err_mess = check_string( $new['topic'], 1 );
		     if( strlen( $text ) < $config['min_post_len'] )
		         $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu kurz.';
		     if( strlen( $text ) > $config['max_post_len'] )
		         $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der Text ist zu lang.';
		     if( $err_mess != '' )
		     {
		         $mess = '<form action="edit.php" method="post" name="sendback">
			      '.$err_mess.'
			     <input type="hidden" name="boardid" value="'.$boardid.'" />
			     <input type="hidden" name="threadid" value="'.$threadid.'" />
			     <input type="hidden" name="postid" value="'.$postid.'" />
			     <input type="hidden" name="new[page]" value="'.$page.'" />
			     <input type="hidden" name="action" value="edit" />
			     <input type="hidden" name="back" value="1" />';
				 if( $post['user_id'] == 0 )
				     $mess .= '<input type="hidden" name="new[guest_name]" value="'.$new['autor'].'" />';
				 else
				     $mess .= '<input type="hidden" name="new[guest_name]" value="" />';
				 $mess .= '
			     <input type="hidden" name="new[topic]" value="'.$new['topic'].'" />
			     <input type="hidden" name="new[text]" value="'.$text.'" />
			     <input type="hidden" name="new[abbo]" value="'.( isset($abbo) ? '1' : '0' ).'" />
		    	 <input type="hidden" name="new[smil]" value="'.( isset($do_smilies) ? '1' : '0' ).'" />
	    		 <input type="hidden" name="new[code]" value="'.( isset($b_code) ? '1' : '0' ).'" />
	    		 </form>';
		         message ( $mess, 'Fehler', 1 );
		     }
			 else
			 {
			     db_query("UPDATE ".$pref."post SET
				     post_text='".addslashes($text)."',
					 guest_name='".addslashes($new['autor'])."',
					 post_smilies='".( isset($do_smilies) ? 1 : '' )."',
					 last_edit_by='".addslashes(U_NAME)."',
					 last_edit_time='$board_time',
					 last_edit_ip='".getenv('REMOTE_ADDR')."',
					 edit_count='".($post['edit_count']+1)."',
					 bcode='".( isset($b_code) ? 1 : '' )."',
					 sendmail='".( isset($abbo) ? 1 : '' )."'
				 WHERE post_id='$postid'");
				 if( P_EDITTOPIC == 1 )
				     db_query("UPDATE ".$pref."thread SET
					     thread_topic='".addslashes($new['topic'])."'
					 WHERE thread_id='$threadid'");
			     message_redirect('Der Beitrag wurde editiert, bitte warten ...', 'showtopic.php?boardid='.$boardid.'&threadid='.$threadid.'&page='.$page.'#p'.$postid );
			 }
	     }
	 }
	 else
         message ( 'Sorry! Du bist nich berechtigt dieses Posting zu editierten.', 'Rechte', 0 );
 }
 // delete post -------------------------
 elseif( $action == 'delete' )
 {
     $r_post = db_query("SELECT
	     deleted,
		 user_id
	 FROM ".$pref."post WHERE thread_id='$threadid' AND post_id='$postid'");
	 if( db_rows( $r_post ) != 1 )
	     message( 'Sorry! Fehlerhafter Link', 'Fehler', 0 );
	 $post = db_result( $r_post );
	 if( $post['deleted'] == 1 )
	     message( 'Dieses Posting ist bereits als gel&ouml;scht makiert.<br />
		           Echtes l&ouml;schen ist nur im Mod,- oder Admincenter durch Administratoren m&ouml;glich.', 'Fehler', 0 );
	 $del_thread = 0;
     $r_post = db_query("SELECT
	     MIN(post_id)
	 FROM ".$pref."post WHERE thread_id='$threadid'");
	 $post = db_result( $r_post );
	 list(, $min_id ) = each( $post );
	 if( $min_id == $postid )
	     $del_thread = 1; 
	 $r_post = db_query("SELECT
	     count(post_id)
	 FROM ".$pref."post WHERE thread_id='$threadid' AND deleted='0'");
	 $post = db_result( $r_post );
	 list(, $post_count ) = each( $post );
	 if( $post_count == 1 )
	     $del_thread = 1; 
	 if( $del_thread == 1 )
	 {
         $mess = '<form action="threadopt.php" method="post" name="weiter">
			     Durch l&ouml;schen dieses Beitrags wird der gesamte Thread gel&ouml;scht<br />
				 Wenn Du sicher bist, dann best&auml;tige mit weiter.
				 <input type="hidden" name="action" value="delete" />
			     <input type="hidden" name="boardid" value="'.$boardid.'" />
			     <input type="hidden" name="threadid" value="'.$threadid.'" />
			     <input type="hidden" name="postid" value="'.$postid.'" />
			     <input type="hidden" name="new[page]" value="'.$page.'" />
	    		 </form>';
		         message ( $mess, 'Fehler', 2 );
	 }
	 else
	 {
	     // threaddaten lesen
	     $r_thread = db_query("SELECT
		     replies,
			 replies_del
		 FROM ".$pref."thread WHERE thread_id='$threadid'");
		 $thread = db_result( $r_thread );
		 // Boarddaten lesen
		 $r_board = db_query("SELECT
		     posts,
			 posts_del
		 FROM ".$pref."board WHERE board_id='$boardid'");
		 // post updaten
		 db_query("UPDATE ".$pref."post SET
		     deleted='1'
		 WHERE post_id='$postid'");
		 // threaddaten updaten
		 db_query("UPDATE ".$pref."thread SET
		     replies='".($thread['replies']-1)."',
		     replies_del='".($thread['replies_del']+1)."'
		 WHERE thread_id='$threadid'");
		 // boarddaten updaten 
		 db_query("UPDATE ".$pref."board SET
		     posts='".($board['posts']-1)."',
		     posts_del='".($board['posts_del']+1)."'
		 WHERE board_id='$boardid'");
		 // modlog
             $basename = basename($HTTP_SERVER_VARS["SCRIPT_NAME"]);
                 db_query( "INSERT INTO ".$pref."modlog SET
                 logtime='$board_time',
                 loguser='".( U_ID == 0 ? 'Gast' : U_NAME )."',
                 logip='".getenv('REMOTE_ADDR')."',
                 logfile='$basename',
                 action='".$action."(p".$postid.")'");
				 
		 message_redirect('Der Beitrag wurde als gel&ouml;scht makiert, bitte warten ...', 'showtopic.php?boardid='.$boardid.'&threadid='.$threadid.'&page='.$page.'#p'.$postid );
	 }
 }
 // no action ---------------------------
 else
     message ( 'Bitte w&auml;le eine Funktion aus.', 'Fehler', 0 );
 echo Output( Template ( $TBoard ) );
?>