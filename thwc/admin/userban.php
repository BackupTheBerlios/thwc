<?php
 /* $Id: userban.php,v 1.1 2003/06/12 13:59:30 master_mario Exp $ */
 include( 'adhead.inc.php' );

 if( isset( $_POST['action'] ) ) $action=$_POST['action'];
 elseif( isset( $_GET['action'] ) )  $action=$_GET['action'];
 else $action = '';
 // FUNKTIONEN
 function banForm ( $ban, $action )
 {
     if( !isset( $ban['banreason'] ) )
         $ban['banreason'] = '';
     if( !isset( $ban['bannote'] ) )
         $ban['bannote'] = '';
     if( !isset( $ban['banid'] ) )
         $ban['banid'] = '';

     $back = '<a href="userban.php?action=banlist">Liste der gebannten User zeigen</a><br />
      <form action="userban.php" method="post">
      <table cellpadding="4" cellspacing="0" border="0">
       <tr>
        <td id="blank" style="width:250px"><b>Username</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">';
     if( !isset( $ban['baned_name'] ) )
         $back .= '<input type="text" name="ban[user_name]" id="border-tab" />';
     else
         $back .= '<b>'.$ban['baned_name'].'</b><input type="hidden" name="ban[user_name]" value="'.$ban['baned_name'].'" />';
     $back .= '</td>
       </tr>';
     if( $action == 'banUser' )
     {
         $back .= '<tr>
        <td id="blank" style="width:250px; vertical-align:top"><b>Art des Banns</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">
         <input type="radio" name="ban[art]" value="0" checked/>Permanent bannen<br />
         <input type="radio" name="ban[art]" value="1" />Zeitbeschr&auml;ngt:
         Frei geben in&nbsp;<input type="text" size="6" maxlength="6" name="ban[time]" id="border-tab" />&nbsp;
         <select name="ban[time_art]" size="1" id="tab">
          <option value="0" selected>Tagen</option>
          <option value="1">Stunden</option>
          <option value="2">Minuten</option>
         </select>
        </td>
       </tr>';
     }
     $back .= '<tr>
        <td id="blank" style="width:250px; vertical-align:top"><b>Grund</b><br />
        <font size="1">(&ouml;ffendlich)</font></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">
         <textarea name="ban[reason]" cols="50" rows="5" id="border-tab">'.$ban['banreason'].'</textarea>
        </td>
       </tr>
       <tr>
        <td id="blank" style="width:250px; vertical-align:top"><b>Bemerkungen</b><br />
        <font size="1">(Nur sichtbar f&uuml;r Moderatoen und Admins)</font></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">
         <textarea name="ban[note]" cols="50" rows="5" id="border-tab">'.$ban['bannote'].'</textarea>
        </td>
       </tr>
       <tr>
        <td id="blank" colspan="3" style="text-align:center">
         <input type="hidden" name="action" value="'.$action.'" />
         <input type="hidden" name="ban[banid]" value="'.$ban['banid'].'" />
         <input type="submit" value=" Senden " id="border-tab" />
        </td>
       </tr>
      </table>
     </form>';
     return $back;
 }
 //CODE
 // chooseUser -----------------------------------------------
 if( $action == 'chooseUser' )
 {
     $data['work'] = '<b>User bannen</b><br /><br />';
     $data['work'] .= banForm( array(), 'banUser' );
 }
 // banUser -------------------------------------------------
 if( $action == 'banUser' )
 {
     $ban = $_POST['ban'];
     $r_user = db_query("SELECT
         user_id,
         user_name,
         is_uradmin
     FROM ".$pref."user WHERE user_name='".addslashes($ban['user_name'])."' ");
     if( db_rows( $r_user ) != 1 )
         $data['work'] = 'Es ist kein User mit diesem Namen registiert.<br />
         Deie Eingabe <b>'.$ban['user_name'].'</b>';
     else
     {
         $user = db_result( $r_user );
         if( $user['is_uradmin'] == 1 )
             $data['work'] = 'Dieser User ist der UrAdmin und kann nicht gebannt werden.';
         else
         {
             $r_ban = db_query("SELECT
                 banid
             FROM ".$pref."ban WHERE baned_name='".addslashes($ban['user_name'])."'");
             if( db_rows( $r_ban ) > 0 )
             {
                 $a_ban = db_result( $r_ban );
                 $data['work'] = 'Dieser User unterliegt bereits einem Ban<br /><br />
                 <a href="userban.php?action=showBan&banid='.$a_ban['banid'].'">Ansehen</a>';
             }
             else
             {
                 if( $ban['art'] == 0 )
                     $endtime = 0;
                 else
                 {
                     switch( $ban['time_art'] )
                     {
                         case 0:
                             $endtime = $ban['time'] * 86400;
                             break;
                         case 1:
                             $endtime = $ban['time'] * 3600;
                             break;
                         case 2:
                             $endtime = $ban['time'] * 60;
                     }
                     $endtime = $board_time + $endtime;
                 }
                 // banedby aus header.inc übernehmen #############################################################
                 db_query("INSERT INTO ".$pref."ban SET
                     bantime='$board_time',
                     banreason='".addslashes($ban['reason'])."',
                     bannote='".addslashes($ban['note'])."',
                     banedby='0',
                     baned_name='".addslashes($ban['user_name'])."',
                     baned_id='".$user['user_id']."',
                     timetoend='$endtime'");

                 $data['work'] = 'User: <b>'.$ban['user_name'].'</b> ist gebannt.';
             }
         }
     }
 }
 // banlist ----------------------------------------------------------
 if( $action == 'banlist' )
 {
     $r_ban = db_query("SELECT
         *
     FROM ".$pref."ban ORDER BY baned_name");
     if( db_rows( $r_ban ) == 0 )
         $data['work'] = 'Es sind keine User in der Bannliste';
     else
     {
         $data['work'] = '<b>Bannliste</b><br />
          <table width="100%" cellpadding="4" cellspacing="0" border="0">
           <tr>
            <td id="blank"><i>Username</i></td>
            <td id="blank"><i>Bannbeginn</i></td>
            <td id="blank"><i>Bannende</i></td>
            <td id="blank"><i>Optionen</i></td>
           </tr>';
         $i=0;
         while( $a_ban = db_result( $r_ban ) )
         {
             $data['work'] .= '<tr bgcolor="'.($i % 2 == 0 ? '#DADADA' : '').'">
               <td id="blank">'.$a_ban['baned_name'].'</td>
               <td id="blank">'.date( "d.m.Y H:i", $a_ban['bantime'] ).'</td>';
             if( $a_ban['timetoend'] == 0 )
                 $end = '<font color="[color_err]">(niemals)</font>';
             else
                 $end = date( "d.m.Y H:i", $a_ban['timetoend'] );
             $data['work'] .= '<td id="blank">'.$end.'</td>
               <td id="blank">
                [smallfont]
                <a href="userban.php?action=unBan&banid='.$a_ban['banid'].'">Aufheben</a> |
                <a href="userban.php?action=showBan&banid='.$a_ban['banid'].'">Ansehen</a> |
                <a href="userban.php?action=editBan&banid='.$a_ban['banid'].'">Editieren</a>
                [smallfontend]
               </td>
              </tr>';
             $i++;
         }
         $data['work'] .= '</table>';
     }
 }
 // unBan ---------------------------------------------------------
 if( $action == 'unBan' )
 {
     db_query("DELETE FROM ".$pref."ban WHERE banid='$_GET[banid]'");
     db_query("OPTIMIZE TABLE ".$pref."ban");
     $data['work'] = 'Bann wurde aufgehoben.';
 }
 // showBan ---------------------------------------------------------
 if( $action == 'showBan' )
 {
     $r_ban = db_query("SELECT
         *
     FROM ".$pref."ban WHERE banid='$_GET[banid]'");
     $a_ban = db_result( $r_ban );

     $data['work'] = '<b>Bandaten: '.$a_ban['baned_name'].'</b><br /><br />
      <table width="100%" cellpadding="4" cellspacing="0" border="0">
       <tr bgcolor="#DADADA">
        <td id="blank" style="width:200px"><b>Bannstart</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">'.date( "d.m.Y H:i\U\h\\r", $a_ban['bantime'] ).'</td>
       </tr>';
      if( $a_ban['timetoend'] == 0 )
      {
          $end = '<font color="[color_err]">(niemals)</font>';
          $wait = '';
      }
      else
      {
          $end = date( "d.m.Y H:i\U\h\\r", $a_ban['timetoend'] );
          $a = $a_ban['timetoend'] - $board_time;
          $d = $a / 86400; $a = $a % 86400; $d = bcadd( $d, 0, 0 );
          if( $a > 0 )
          {
              $s = $a / 3600; $a = $a % 3600; $s = bcadd( $s, 0, 0 );
          }
          if( $a > 0 )
          {
              $m = $a / 60; $m = bcadd( $m, 0, 0 );
          }
          if( !isset( $s ) )
              $s = 0;
          if( !isset( $m ) )
              $m = 0;
          $wait = '<font color="[color_err]"> (noch '.$d.'Tage '.$s.'Stunden '.$m.'Minuten</font>)';
      }
      $data['work'] .= '<tr>
        <td id="blank"><b>Bannende</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">'.$end.$wait.'</td>
       </tr>
       <tr bgcolor="#DADADA">
        <td id="blank"><b>Gebannt von</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">'.$a_ban['banedby'].'</td>
       </tr>
       <tr>
        <td id="blank" style="vertical-align:top"><b>Bangrund</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">'.$a_ban['banreason'].'</td>
       </tr>
       <tr bgcolor="#DADADA">
        <td id="blank" style="vertical-align:top"><b>Bemerkungen</b></td>
        <td id="blank">&nbsp;</td>
        <td id="blank">'.$a_ban['bannote'].'</td>
       </tr>
       <tr>
        <td id="blank" colspan="3" style="text-align:center">
         <a href="userban.php?action=unBan&banid='.$a_ban['banid'].'">Bann aufheben</a> |
         <a href="userban.php?action=editBan&banid='.$a_ban['banid'].'">Bann editieren</a>
        </td>
       </tr>
      </table>
     ';
 }
 // editBan -------------------------------------------------------------------
 if( $action == 'editBan' )
 {
     $r_ban = db_query("SELECT
         *
     FROM ".$pref."ban WHERE banid='$_GET[banid]'");
     $ban = db_result( $r_ban );
     $data['work'] = '<b>Bann editieren</b><br /><br />';
     $data['work'] .= banForm( $ban, 'updateBan' );
 }
 // updateBan -------------------------------------------------------------------
 if( $action == 'updateBan' )
 {
     $ban = $_POST['ban'];
     db_query("UPDATE ".$pref."ban SET
         banreason='".addslashes($ban['reason'])."',
         bannote='".addslashes($ban['note'])."'
     WHERE banid='$ban[banid]'");

     $data['work'] = 'Ban editiert.';
 }
 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>