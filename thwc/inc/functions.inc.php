<?php
/* $Id: functions.inc.php,v 1.4 2003/06/16 18:14:57 master_mario Exp $ */
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
  function Get_Template ( $template )
  {
      if( !file_exists( $template ) )
      {
          die ('<br><br><center><b><font color="#FF0000">Template '.$template.' nicht geladen.</font></b></center>');
      }
      $daten = fopen ( $template, "r" );
      $show  = fread ( $daten, filesize($template) );
      fclose ( $daten );
      return $show;
  }

  function Template ( $show )
  {
      global $data;
      foreach ( $data as $key=>$value )
      {
          $show = str_replace ( '['.$key.']', $value, $show );
      }
      return $show;
  }

  function Output ( $show )
  {
      global $style;
      foreach ( $style as $key=>$value )
      {
          $show = str_replace ( '['.$key.']', $value, $show );
      }
      return $show;
  }


  function db_query ( $query )
  {
      if(!$this->result = mysql_query($query))
      {
          $this->errormessage = mysql_error();
          $this->errornumber = mysql_errno();
          DIE ( '<font color="#FF0000">query failed<BR>'.$this->errornumber.' : '.$this->errormessage.'<br>'.$query );
      }
      else
      {
          return $this->result;
      }
  }

  function db_result ( $result )
  {
      if(!$result)
      {
          DIE ( '<font color="#FF0000">result error<BR>99 : Result is empty.' );
      }
      else
      {
          return mysql_fetch_array ( $result, MYSQL_ASSOC );
      }
  }

  function db_rows ( $result )
  {
      return mysql_num_rows ( $result );
  }

  function Create_Smillist ( $checked )
  {
      global $style;
      $smilies = opendir('templates/'.$style['styletemplate'].'/images/icon/');
      $a_file = array();
      $smil = array();
      while( $file = readdir($smilies) )
      {
          if( preg_match("/_new/", $file ) )
          {
              $smil[] = $file;
          }
      }
      closedir($smilies);
      $next = @bcdiv( count( $smil ), 2, 0 )+1;
      $icon_list = '<table cellpadding="3" cellspacing="0" border="0"><tr><td class="blank">';
      $t = 0;
      foreach( $smil as $value )
      {
          $t++;
          $select = '';
          if( $checked == str_replace( '_new.gif', '', $value ) )
          {
              $select = ' checked';
          }
          if( $t == $next )
          {
              $icon_list .= '</td></tr><tr><td class="blank">&nbsp;<input type="radio" name="icon" value="'.str_replace( '_new.gif', '', $value ).'"'.$select.'>&nbsp;<img src="templates/'.$style['styletemplate'].'/images/icon/'.$value.'" width="15" height="15" border="0"></input>&nbsp;';
          }
          else
          {
               $icon_list .= '&nbsp;<input type="radio" name="icon" value="'.str_replace( '_new.gif', '', $value ).'"'.$select.'>&nbsp;<img src="templates/'.$style['styletemplate'].'/images/icon/'.$value.'" width="15" height="15" border="0"></input>&nbsp;';
          }
      }
      $icon_list .= '</td></tr></table>';
      return $icon_list;
  }

  function Jump ( $pref )
  {
      $jump_list = '<select name="boardid" size="1" id="tab">';
      $r_jump_cat = db_query("SELECT
          category_id,
          category_name
      FROM ".$pref."category ORDER BY Category_order ASC");
      if( db_rows( $r_jump_cat ) > 0 )
      {
          while( $a_jump_cat = db_result( $r_jump_cat ) )
          {
              $jump_list .= '<option value="-'.$a_jump_cat['category_id'].'">'.$a_jump_cat['category_name'].'</option>';
              $r_jump_board = db_query("SELECT
                  board_id,
                  board_name
              FROM ".$pref."board WHERE category='$a_jump_cat[category_id]' ORDER BY board_order ASC");
              if( db_rows( $r_jump_board ) > 0 )
              {
                   while( $a_jump_board = db_result( $r_jump_board ) )
                   {
                       $jump_list .= '<option value="'.$a_jump_board['board_id'].'">-&nbsp;'.$a_jump_board['board_name'].'</option>';
                   }
              }
          }
      }
      $jump_list .= '</select>';
      return $jump_list;
  }

  function message ( $mess, $messtopic, $mode )
  {
      global $style, $data, $TBoard;
      $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Fehler';
          if( $mode == 0 )
              $TMessage = Get_Template( 'templates/'.$style['styletemplate'].'/message.html' );
          if( $mode == 1 )
              $TMessage = Get_Template( 'templates/'.$style['styletemplate'].'/message_back.html' );
          $TMessage = str_replace( '[message]', $mess, $TMessage );
          $TMessage = str_replace( '[messagetopic]', $messtopic, $TMessage );
          $TBoard = str_replace( '[boardtable]', $TMessage, $TBoard );
      $TBoard = Template ( $TBoard );
      echo Output( $TBoard );
          exit;
  }

  function fragen ( $mess, $messtopic, $url, $antwort )
  {
      global $style, $data, $TBoard;
      $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;'.$messtopic;
          $TMessage = Get_Template( 'templates/'.$style['styletemplate'].'/fragen.html' );
          $TMessage = str_replace( '[message]', $mess, $TMessage );
          $TMessage = str_replace( '[messagetopic]', $messtopic, $TMessage );
          $TMessage = str_replace( '[url]', $url, $TMessage );
          $TMessage = str_replace( '[antwort]', $antwort, $TMessage );
          $TBoard = str_replace( '[boardtable]', $TMessage, $TBoard );
      $TBoard = Template ( $TBoard );
      echo Output( $TBoard );
          exit;
  }

  function message_redirect($msg, $url)
  {
      global $style;

      $TRedirect = Get_Template( 'templates/'.$style['styletemplate'].'/redirect.html' );
      $TRedirect = str_replace( '[url]', $url, $TRedirect );
      $TRedirect = str_replace( '[msg]', $msg, $TRedirect );
      echo Output( $TRedirect );

      exit;
  }

  function check_string( $string, $mode )
  {
      global $config;
	  
	  $err_mess = '';
	  if( $mode == 0 )
	  {
	      if( strlen( $string ) < $config['min_usernamelength'] )
		  {
		      $err_mess .= 'Der gew&auml;lte Name ist zu kurz.';
			  $err = 1;
		  }
	      if( strlen( $string ) > $config['max_usernamelength'] )
		  {
		      $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der gew&auml;lte Name ist zu lang.';
			  $err = 1;
		  }
          $legalchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 [|](){}.-_‰ˆ¸ƒ÷‹ﬂ";
          for( $i = 0; $i < strlen($string); $i++ )
          {
              if( !strstr($legalchars, $string[$i]) )
              {
		          $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Der gew&auml;lte Name enth&auml;lt nicht erlaubte Zeichen. ( '.$string[$i].' )';
              }
          }
	  }
	  if( $mode == 1 )
	  {
	      if( strlen( $string ) < $config['min_topic_len'] )
		  {
		      $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Das gew&auml;lte Topic ist zu kurz.';
			  $err = 1;
		  }
	      if( strlen( $string ) > $config['max_topic_len'] )
		  {
		      $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Das gew&auml;lte Topic ist zu lang.';
			  $err = 1;
		  }
          $legalchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 [|](){}.-_‰ˆ¸ƒ÷‹ﬂ";
          for( $i = 0; $i < strlen($string); $i++ )
          {
              if( !strstr($legalchars, $string[$i]) )
              {
		          $err_mess .= ( $err_mess == '' ? '' : '<br />' ).'Das gew&auml;lte Topic enth&auml;lt nicht erlaubte Zeichen. ( '.$string[$i].' )';
              }
          }
	  }
      return $err_mess;
  }
  define('INVALID_CHAR', 1);
  define('INVALID_LENGTH', 2);
  define('NAME_TAKEN', 3);
  define('NAME_BANNED', 4);

  function verify_username($username)
  {
      global $config, $pref;
      $legalchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 [|](){}.-_‰ˆ¸ƒ÷‹ﬂ";
      for( $i = 0; $i < strlen($username); $i++ )
      {
          if( !strstr($legalchars, $username[$i]) )
          {
              return INVALID_CHAR;
          }
      }

      while( list(, $bannedname) = @each($config['bannednames']) )
      {
          if( $bannedname && stristr($username, $bannedname) )
          {
              return NAME_BANNED;
          }
      }

      if( strlen($username) > $config['max_usernamelength'] || strlen($username) < $config['min_usernamelength'] )
      {
          echo strlen($username);
          return INVALID_LENGTH;
      }

      $r_user = db_query("SELECT user_id FROM $pref"."user WHERE user_name='".addslashes($username)."'");
      if( mysql_num_rows($r_user) )
      {
          return NAME_TAKEN;
      }
      return 0;
  }

  //called by register
  function check_username($username)
  {
      switch( verify_username($username) )
      {
          case NAME_TAKEN:
            message( 'Der Benutzername existiert leider schon!', 'Fehler' );
            break;
          case INVALID_CHAR:
            message( 'Ihr gew&#xFC;nschter Benutzername enth&#xE4;lt ung&#xFC;ltige Zeichen!', 'Fehler' );
            break;
          case NAME_BANNED:
            message( 'Der ausgew&#xE4;hlte Benutzername kann leider nicht verwendet werden.', 'Fehler' );
            break;
          case INVALID_LENGTH:
            message( 'Die L&#xE4;nge des Benutzernamens ist ung&#xFC;ltig', 'Fehler' );
            break;
          default:
      }
      return;
  }

  //called by register, editprofile
  function check_email($email)
  {
      return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email);
  }

  //called by online
  class Position
  {
      function Position ( $pref )
          {
              $r_pos_board = db_query("SELECT
                      board_id,
                          board_name
                  FROM ".$pref."boards ");
                  $position_board_id = array();
                  $position_board_name = array();
                  while( $a_pos_board = db_result( $r_pos_board ) )
                  {
                      $this->position_board_id[] = $a_pos_board['board_id'];
                      $this->position_board_name[] = $a_pos_board['board_name'];
                  }
                  mysql_free_result( $r_pos_board );
                  unset( $a_pos_board );
                  $r_pos_category = db_query("SELECT
                      category_id,
                          category_name
                  FROM ".$pref."category ");
                  $position_category_id = array();
                  $position_category_name = array();
                  while( $a_pos_category = db_result( $r_pos_category ) )
                  {
                      $this->position_category_id[] = $a_pos_category['category_id'];
                      $this->position_category_name[] = $a_pos_category['category_name'];
                  }
                  mysql_free_result( $r_pos_category );
                  unset( $a_pos_category );
          }

      function check_position ( $position )
      {
              if( ereg ( "[^0-9]", $position ) )
                  {
                      $position = explode( '|', $position );
                          if( $position[0] == 2 )
                          {
                              if( array_search ( $position[1], $this->position_category_id ) === 'FALSE' )
                                  {
                                      $pos = 'ka';
                                  }
                                  else
                                  {
                                  $key = array_search ( $position[1], $this->position_category_id );
                                      $pos = 'Category: <a href="board.php?b_id='.$position[1].'">'.$this->position_category_name[$key].'</a>';
                                  }
                          }
                          if( $position[0] == 3 )
                          {
                              if( array_search ( $position[1], $this->position_board_id ) === 'FALSE' )
                                  {
                                      $pos = 'ka';
                                  }
                                  else
                                  {
                                  $key = array_search ( $position[1], $this->position_board_id );
                                      $pos = 'Board: <a href="board.php?b_id='.$position[1].'">'.$this->position_board_name[$key].'</a>';
                                  }
                          }
                  }
                  else
                  {
              if( $position == 0 )
                      $pos = 'Login =&gt; Index';
                  if( $position == 1 )
                      $pos = 'Wer war online?';
                  if( $position == 100 )
                      $pos = 'Logout';
                  }
              return $pos;
      }
  }

  //called by online, board.php, showtopic.php
  function check_pages ( $count, $default, $page, $mode, $datei )
  {
      $pages = $count/$default;
      $pages = bcadd( $pages, 0, 0 );
      if( bcmul ( $pages, $default, 0 ) < $count )
          $pages++;
          if( $page == 'last' )
              $page = $pages;
          else
          $page  = intval( $page );
          $back = '<b>'.$pages.'</b> Seite(n):';
      if( $page > 4 )
      {
          $back .= ' [<a href="'.$datei.'&page=1" class="bg">erste Seite</a>] ...';
      }
      $end = $page+3;
      if( $end > $pages ) { $end = $pages; }
      for( $x=$page-3; $x<$end+1; $x++ )
      {
          if( $x > 0 )
          {
              if( $x == $page )
              {
                  $back .= ' <b>-'.$x.'-</b>';
              }
              else
              {
                  $back .= ' [<a href="'.$datei.'&page='.$x.'" class="bg">'.$x.'</a>]';
              }
          }
      }
      if( $x <= $pages )
      {
          $back .= ' ... [<a href="'.$datei.'&page=last" class="bg">letzte Seite</a>]';
      }

          return $back;
  }

  function datum ( $timestring )
  {
      global $board_time;
          if( date( "d.m.Y", $board_time ) == date( "d.m.Y", $timestring ) )
              $back = '<b>Heute</b> '.date( "\u\m H:i\U\h\\r", $timestring );
          elseif( date( "d.m.Y", ($board_time-86400) ) == date( "d.m.Y", $timestring ) )
              $back = '<b>Gestern</b>';
          else
              $back = date( "d.m.Y\, H:i \U\h\\r", $timestring );
          return $back;
  }
  function globalPermissions ( $groupids )
  {
      global $pref;
          $len = strlen( $groupids );
          $groupids = substr ( $groupids, 1 );
          $groupids = substr ( $groupids, 0, $len-2 );
          $r_groups = db_query("SELECT
              accessmask,
                  priority
          FROM ".$pref."group WHERE groupid IN (".$groupids.")");
          $priority = 0;
          while( $a_groups = db_result( $r_groups ) )
          {
              if( $a_groups['priority'] >= $priority )
                      $accessmask = $a_groups['accessmask'];
          }
          $rechte = decbin ( intval ( $accessmask ) );
      if( strlen( $rechte ) < 30 )
      {
          $y = 30-strlen( $rechte );
          for( $x=0; $x<$y; $x++ )
              $rechte = '0'.$rechte;
      }
      $rechte = chunk_split ( $rechte, 1, '|' );
      $P = explode( '|', $rechte );
          return $P;
  }
  function boardPermissions ( $groupids, $boardid )
  {
      global $pref;

          $len = strlen( $groupids );
          $groupids = substr ( $groupids, 1 );
          $groupids = substr ( $groupids, 0, $len-2 );

          $r_groupboard = db_query("SELECT
              accessmask
          FROM ".$pref."groupboard WHERE boardid='$boardid' AND groupid IN (".$groupids.")");
          if( db_rows( $r_groupboard ) == 1 )
          {
              $a_groupboard = db_result( $r_groupboard );
                  $acces = $a_groupboard['accessmask'];
                  mysql_free_result( $r_groupboard );
                  unset( $a_groupboard );
          }
          else
          {
              $r_groups = db_query("SELECT
                  accessmask,
                      priority
              FROM ".$pref."group WHERE groupid IN (".$groupids.")");
              $priority = 0;
              while( $a_groups = db_result( $r_groups ) )
              {
                  if( $a_groups['priority'] >= $priority )
                          $acces = $a_groups['accessmask'];
              }
                  mysql_free_result( $r_groups );
                  unset( $a_groups );
          }
          $rechte = decbin ( intval ( $acces ) );
      if( strlen( $rechte ) < 30 )
      {
          $y = 30-strlen( $rechte );
          for( $x=0; $x<$y; $x++ )
              $rechte = '0'.$rechte;
      }
      $rechte = chunk_split ( $rechte, 1, '|' );
          $rechte = substr ( $rechte, 0, 61 );
      $rechte = strrev ( $rechte );
      $P = explode( '|', $rechte );
          return $P;
  }
  // new Posts ----------------------
  function setNewposts( $last_act )
  {
      global $pref;
      $r_boards = db_query("SELECT
          board_id
      FROM ".$pref."board WHERE category!='0' AND disabled!='1'");
      if( db_rows( $r_boards ) > 0 )
      {
          while( $a_boards = db_result( $r_boards ) )
          {
              $session_var = 'b'.$a_boards['board_id'];
              $r_post_id = db_query("SELECT
                  MIN(post_id),
                  COUNT(post_id)
              FROM ".$pref."post WHERE board_id='$a_boards[board_id]' AND post_time>'$last_act'");
              $a_post_id = db_result( $r_post_id );
                  list( $a, $b) = each($a_post_id);
              if( $b == 0 )
              {
                  $r_max_post = db_query("SELECT
                      MAX(post_id)
                  FROM ".$pref."post WHERE board_id='$a_boards[board_id]'");
                  $a_max_post = db_result( $r_max_post );
                      list(, $a ) = each($a_max_post);
                  $_SESSION[$session_var] = $a;
              }
              else
              {
                  $_SESSION[$session_var] = $a-1;
              }
          } // while
	      $_SESSION['newpost'] = 1;
      } // if
  }
  //called by createStat
  function proz( $hundert, $anteil )
  {
      $einpro = @bcdiv( $hundert, 100, 2 );
	  $prozente = bcdiv( $anteil, $einpro, 2 );
	  $prozente = round ($prozente);   
	  if( $prozente > 100 )
	      $prozente = 100;
	
	  return  $prozente;
  }
  //called by ministat
  function createStat( $name, $posts, $deleted, $count, $permission )
  {
      global $style;
	  
	  $width = proz( $posts, $count );
	  $real  = proz( $posts+$deleted, $count );
	  
	  $back = '<table cellpadding="0" cellspacing="0" border="0">
	  <tr>
	   <td class="blank">
	    [smallfont]'.$name.': '.$count.' / '.$posts.( $permission == 1 ? ' / <font color="[col_link]">'.$deleted.'</font>' : '' ).'[smallfontend]
	   </td>
	  </tr>
	  <tr>
	   <td class="blank">
	    <img src="templates/'.$style['styletemplate'].'/images/mini2.png" style="width:'.$width.'px; height:10px" border="0" />[smallfont]&nbsp;&nbsp;&nbsp;'.$width.'%[smallfontend]';
	  if( $permission == 1 )
	  {
	      $back .= '<br />
		   <img src="templates/'.$style['styletemplate'].'/images/mini1.png" style="width:'.$real.'px; height:10px" border="0" /><font color="[col_link]">[smallfont]&nbsp;&nbsp;&nbsp;'.$real.'%[smallfontend]</font>';
 	  }
	  $back .= '</td></tr></table>';
	  
	  return $back;
  }
?>