<?php
 /* $Id: memberlist.php,v 1.2 2003/06/26 13:46:18 master_mario Exp $ */
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
 $data['nav_path'] .= '&nbsp;&gt;&gt;&nbsp;Memberlist';
 // global Templates ----------------------------------
 $TBoard = Get_Template( 'templates/'.$style['styletemplate'].'/board.html' );
 $TList = Get_Template( 'templates/'.$style['styletemplate'].'/memberlist.html' );
 $TRow = Get_Template( 'templates/'.$style['styletemplate'].'/membersrow.html' );
 // Guest ---------------------------------------------
 if( U_ID < 1 && $config['guest_memberlist'] == 0 )
     message( 'Die Memberlist ist f&uuml;r G&auml;ste deaktiviert.', 'Rechte', 0 );
 // suchoptionen --------------------------------------
 if( !isset( $method ) )
     $method = 0;
	 
 switch( $method )
 {
     case 0:
	     $order = "user_name";
		 break;
     case 1:
	     $order = "user_mail";
		 break;
     case 2:
	     $order = "user_icq";
		 break;
     case 3:
	     $order = "user_hp";
		 break;
     case 4:
	     $order = "user_ort";
		 break;
     case 5:
	     $order = "post_count";
		 break;
     case 6:
	     $order = "user_join";
		 break;
     case 7:
	     $order = "user_lastpostt";
		 break;
 }
 $TList = str_replace( '[met'.$method.']', 'selected', $TList );
 // suchreihenfolge -----------------------------------
 if( !isset( $art ) )
     $art = 0;

 switch( $art )
 {
     case 0:
	     $opt = 'ASC';
		 break;
	 case 1:
	     $opt = 'DESC'; 
 }  
 $TList = str_replace( '[art'.$art.']', 'selected', $TList );
 // limit und mem_nav ----------------------------------
 $r_count = db_query("SELECT
     user
 FROM ".$pref."stats");
 $side = 20;
 $a_count = db_result( $r_count );
 $count = $a_count['user'];
 // pages 
 $pages = $count/$side;
 $pages = bcadd( $pages, 0, 0 );
 if( bcmul ( $pages, $side, 0 ) < $count )
     $pages++;	 
 // mem_nav
 if( !isset( $page ) )
     $page = 1; 
 $data['mem_na'] = check_pages( $count, $side, $page, 0, 'memberlist.php?method='.$method.'&art='.$art );
 $data['mem_nav'] = check_pages( $count, $side, $page, 1, 'memberlist.php?method='.$method.'&art='.$art );
 // define LIMIT
 if( $page == 'last' )
     $page = $pages;
 $page  = intval( $page );
 if( $page<1 )
     $page = 1;
 $start = ($page-1)*$side;
 $limit = $start.', '.$side;
 $memberslist = '';
 $r_user = db_query("SELECT
     user_id,
	 user_name,
	 user_mail,
	 user_nomail,
	 user_icq,
	 user_hp,
	 user_ort,
	 post_count,
	 user_join,
	 user_lastpostt
 FROM ".$pref."user ORDER BY ".$order." ".$opt." LIMIT ".$limit." ");
 $i = 0;
 while( $user = db_result( $r_user ) )
 {
     if( U_ID < 1 )
	     $mail = '- Versteckt';
	 else
	 {
	     if( $user['user_mail'] == '' )
		     $mail = '&nbsp;';
		 if( $user['user_nomail'] == 1 && U_ISADMIN == 0 && U_ISMOD == 0 )
		     $mail = '- Versteckt';
		 if( $user['user_nomail'] == 0 || ( $user['user_nomail'] == 1 && ( U_ISADMIN == 1 || U_ISMOD == 1 ) ) )
		     $mail = '<a href="mailto:'.$user['user_mail'].'">'.$user['user_mail'].'</a>';
	 }
 
     $row = $TRow;
	 $row = str_replace( '[color_row]', ( $i % 2 == 0 ? '[CellB]' : '[CellA]' ), $row );
	 $row = str_replace( '[name]', '<a href="s_profile.php?username='.$user['user_name'].'">'.$user['user_name'].'</a>', $row );
	 $row = str_replace( '[mail]', $mail, $row );
	 $row = str_replace( '[icq]', ( $user['user_icq'] != 0 ? $user['user_icq'] : '&nbsp;' ), $row );
	 $row = str_replace( '[hp]', ( $user['user_hp'] != '' && $user['user_hp'] != 'http://' ? '<a href="'.$user['user_hp'].'" target="_blank">'.$user['user_hp'].'</a>' : '&nbsp;' ), $row );
	 $row = str_replace( '[ort]', ( $user['user_ort'] != '' ? $user['user_ort'] : '&nbsp;' ), $row );
	 $row = str_replace( '[posts]', $user['post_count'], $row );
	 $row = str_replace( '[join]', datum( $user['user_join'] ), $row );
	 $row = str_replace( '[last]', ( $user['user_lastpostt'] != 0 ? datum( $user['user_lastpostt'] ) : 'N/A' ), $row );
	 $memberslist .= $row;
	 $i++;   
 } 
 
 $TList = str_replace( '[members]', $memberslist, $TList );
 $data['boardtable'] = Template( $TList );
 echo Output( Template ( $TBoard ) );
?>