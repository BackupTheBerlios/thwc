<?php
 /* $Id: useredit.php,v 1.2 2003/06/13 11:33:22 master_mario Exp $ */
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
 include( 'adhead.inc.php' );

 if( isset( $_POST['action'] ) ) $action=$_POST['action'];
 if( isset( $_GET['action'] ) )  $action=$_GET['action'];

 $search_user = array(
     array('user_id', 'User ID', 1),
     array('user_name', 'Username', 2),
     array('user_mail', 'Email-Adresse', 2),
     array('signatur', 'Signatur', 2),
     array('post_count', 'Postcount', 1),
     array('user_join', 'Registrierdatum', 3),
     array('last_post_time', 'Letztes Posting', 3)
 );

 $user_edit = array(
     'user_name' => array('Username', '', 2),
     'user_pw' => array('Passwort', '(Momentanes Passwort ist unsichtbar.<br>Gib hier nichts ein au&szlig;er Du willst es &auml;ndern.)', 4),
     'user_mail' => array('User Email-Adresse', '', 2),
     'post_count' => array('Postings', '', 1),
     'user_title' => array('Tietel', '&Uuml;berschreibt alle R&auml;nge', 2),
     'user_hp' => array('Homepage', '', 2),
     'user_ort' => array('Wohnort', '', 2),
     'user_icq' => array('ICQ #', '', 1),
     'user_aim' => array('AIM Name', '', 2),
     'user_msn' => array('MSN Name', '', 2),
     'user_avatar' => array('Avatar', 'Gib hier "<b>nicht erlaubt</b>" ein um dem User<br />Die Nutzung eines Avatars zu verbieten.', 2),
     'user_interests' => array('Interessen', '', 5),
     'signatur' => array('Signatur', '', 5),
     /* opts */
     'user_ishidden' => array('Unsichtbar?', '', 6),
     'show_sig' => array('Signaturen zeigen?', '', 6),
     'user_nomail' => array('E-Mail verbergen?', '', 6),
     'noPM_message' => array('PM MessageBox?', '(Messagebox bei neuen PMs)', 6)
 );
 // FUNKTIONEN
 function in_group($groupids, $groupid)
 {
     $groupids = substr($groupids, 1, strlen($groupids) - 2);
     $a_groupid = explode(',', $groupids);
     while( list(, $gid) = each($a_groupid) )
     {
         if( $gid == $groupid )
         return 1;
     }
     return 0;
 }
 // CODE
 // adduser -------------------------------------------------
 if( $action == 'adduser' )
 {
     if( isset( $_POST['send'] ) )
     {
         $user = $_POST['user'];
         if( !$user['name'] || !$user['pass'] || !$user['pass1'] )
             $data['work'] = 'Bitte f&uuml;lle alle Felder aus.';
         else
         {
             if( $user['pass'] != $user['pass1'] )
                 $data['work'] = 'Passwort und Passwortwiederholung stimmen nicht &uuml;berein.';
             else
             {
                 $r_newuser = db_query("SELECT
                     user_name
                 FROM ".$pref."user WHERE user_name='".addslashes($user['name'])."'");
                 if( db_rows( $r_newuser ) > 0 )
                     $data['work'] = 'Sorry! Es ist schon ein User mit diesem Namen registriert.';
                 else
                 {
                     db_query("INSERT INTO ".$pref."user SET
                         user_name='".addslashes($user['name'])."',
                         user_mail='".addslashes($user['mail'])."',
                         user_pw='".addslashes(md5($user['pass']))."',
                         user_join='$board_time',
                         groupids=',".$config['default_groupid'].",' ");
                     $data['work'] = 'Neuer User ist angelegt.';
                 }
             }
         }
     }
     else
     {
         $data['work'] = '<b>Neuen User registrieren</b><br />';
         $data['work'] .= '<form action="useredit.php" method="post">
          <table cellpadding="4" cellspacing="0" border="0">
           <tr><td id="blank" style="width:300px">Username</td>
            <td id="blank">
             <input type="text" name="user[name]" maxlength="'.$config['max_usernamelength'].'" value="" id="border-tab" />
            </td></tr>
           <tr><td id="blank" style="width:300px">User E-Mail</td>
            <td id="blank">
             <input type="text" name="user[mail]" maxlength="128" value="" id="border-tab" />
            </td></tr>
           <tr><td id="blank" style="width:300px">Passwort</td>
            <td id="blank">
             <input type="password" name="user[pass]" maxlength="30" id="border-tab" />
            </td></tr>
           <tr><td id="blank" style="width:300px">Passwort<font size="1"> (Wiederholung)</font></td>
            <td id="blank">
             <input type="password" name="user[pass1]" maxlength="30" id="border-tab" />
            </td></tr>
           <tr><td id="blank">&nbsp;</td><td id="blank">
             <input type="hidden" name="action" value="adduser" />
             <input type="submit" name="send" value=" Senden " id="border-tab"/>
            </td>
           </tr></table></form>';
     }
 }
 // editUser ------------------------------------
 if( $action == 'editUser' )
 {
     if( !isset( $_GET['u_id'] ) )
         $data['work'] = 'Du hast keinen User ausgew&auml;hlt.';
     else
     {
         $u_id = $_GET['u_id'];
         $r_user = db_query("SELECT
             *
         FROM ".$pref."user WHERE user_id='$u_id'");
         if( db_rows( $r_user ) != 1 )
             $data['work'] = 'Der ausgew&auml;hlte User existiert nicht.';
         else
         {
             $user = db_result( $r_user );
             $data['work'] = '<b>User editieren</b><br />
              <form action="useredit.php" method="post">
              <table cellpadding="4" cellspacing="0" border="0">';
             if( $user['usernodelete'] == 0 || $user['is_uradmin'] == 0 )
             {
                 $data['work'] .= '
                 <tr>
                  <td id="blank" style="width:250px"><b>Userstatus</b></td>
                  <td id="blank">&nbsp;</td>
                  <td id="blank">
                   <select name="status" id="tab" size="1">
                    <option value="0"'.($user['user_isadmin'] == 0 && $user['user_ismod'] == 0 ? ' selected' : '').'>regul&auml;rer User</option>
                    <option value="1"'.($user['user_ismod'] == 1 ? ' selected' : '').'>Moderator</option>
                    <option value="2"'.($user['user_isadmin'] == 1 ? ' selected' : '').'>Admin</option>
                   </select>
                  </td>
                 </tr>';
             }
             else
             {
                 $data['work'] .= '
                 <tr>
                  <td id="blank" colspan="2">&nbsp;</td>
                  <td id="blank"><font color="[color_err]">Der User ist UrAdmin, sein Status<br />
                   kann nicht ge&auml;nder werden</font></td>
                 </tr>';
             }
             $data['work'] .= '
              <tr>
               <td id="blank" style="width:250px; vertical-align:top"><b>Usergruppen</b></td>
               <td id="blank">&nbsp;</td>
               <td id="blank">
                <select id="tab" name="groupids[]" size="5" style="width:100px" multiple>';
             $r_group = db_query("SELECT
                 name,
                 groupid
             FROM ".$pref."group ORDER BY groupid ASC");
             while( $group = db_result( $r_group ) )
             {
                 $data['work'] .= '<option value="'.$group['groupid'].'"'.(in_group($user['groupids'], $group['groupid']) ? ' selected' : '').'>'.$group['name'].'</option>';
             }
             $data['work'] .= '
                </select>
               </td>
              </tr>';
             foreach( $user_edit as $key=>$value )
             {
                 $data['work'] .= '
                 <tr>
                  <td id="blank" style="width:250px; vertical-align:top"><b>'.$value[0].'</b><br /><font size="1">'.$value[1].'</font></td>
                  <td id="blank">&nbsp;</td><td id="blank" style="vertical-align:top">';
                 switch ( $value[2] )
                 {
                     case 1:
                         $data['work'] .= '<input type="text" size="10" name="user['.$key.']" value="'.$user[$key].'" id="border-tab"/>';
                         break;
                     case 2:
                         $data['work'] .= '<input type="text" name="user['.$key.']" maxlegth="255" value="'.$user[$key].'" id="border-tab" />';
                         break;
                     case 4:
                         $data['work'] .= '<input type="password" name="user['.$key.']" maxlegth="30" id="border-tab" />';
                         break;
                     case 5:
                         $data['work'] .= '<textarea name="user['.$key.']" cols="50" rows="5" id="border-tab">'.$user[$key].'</textarea>';
                         break;
                     case 6:
                         $data['work'] .= '<input type="radio" name="user['.$key.']" value="1"'.( $user[$key] == 1 ? ' checked' : '').' />&nbsp;Ja
                         &nbsp;&nbsp;&nbsp;<input type="radio" name="user['.$key.']" value="0"'.( $user[$key] == 0 ? ' checked' : '').' />&nbsp;Nein';
                 }
                 $data['work'] .= '</td></tr>';
             }
             $data['work'] .= '
              <tr>
               <td id="blank" colspan="3" style="text-align:center">
                <input type="hidden" name="action" value="updateUser" />
                <input type="hidden" name="u_id" value="'.$u_id.'" />
                <input type="submit" value=" Editieren " id="border-tab" />
               </td>
              </tr>
              </table></form>';
         }
     }
 }
 // updateUser -----------------------------------------
 if( $action == 'updateUser' )
 {
     $user = $_POST['user'];

     if( substr($user['user_hp'], 0, 7) != "http://" )
     {
         $user['user_hp'] = "http://" . $user['user_hp'];
     }
     $update = '';
     foreach( $user_edit as $key=>$v )
     {
         $value = $user[$key];
         if( $v[2] == 4 )
         {
             if( $value )
             {
                  $update .= $key."='".md5(addslashes($value))."', ";
             }
         }
         else
         {
             $update .= $key."='".addslashes($value)."', ";
         }
     }
     if( isset( $_POST['status'] ) )
     {
         if( $_POST['status'] == 0 )
             $update .= "user_ismod='0', usernodelete='0', ";
         if( $_POST['status'] == 1 )
             $update .= "user_ismod='1', usernodelete='1', ";
         if( $_POST['status'] == 2 )
             $update .= "user_ismod='0', usernodelete='1', user_isadmin='1', ";
     }
     if( !empty( $groupids ) )
         $groupids = ','.implode(',', $groupids ).',';
     else
         $groupids = ',,';
     $update .= " groupids='".$groupids."'";
     // update
     db_query("UPDATE ".$pref."user SET
         ".$update."
     WHERE user_id='$_POST[u_id]'");
     /* ######################################################
        #    Weitere Updates müssen noch bearbeitet werden   #
        ######################################################
     */
     $data['work'] = 'Userdaten wurden gespeichert.';
 }
 // deluser --------------------------------------
 if( $action == 'deluser' )
 {
     if( isset( $_POST['send'] ) )
     {
         if( !$_POST['user_name'] )
             $data['work'] = 'Bitte gib den Namen des zu l&ouml;schenden Users ein.';
         $r_deluser = db_query("SELECT
             user_name
         FROM ".$pref."user WHERE user_name='".addslashes($_POST['user_name'])."'");
         if( db_rows( $r_deluser ) != 1 )
             $data['work'] = 'Es ist kein User mit diesem Namen registriert.';
         else
         {
             db_query("DELETE FROM ".$pref."user WHERE user_name='".addslashes($_POST['user_name'])."'");
             db_query("OPTIMIZE TABLE ".$pref."user");
             $data['work'] = 'User <b>'.addslashes($_POST['user_name']).'</b> wurde gel&ouml;scht.';
         }
     }
     else
     {
         $user_name = '';
         if( isset( $_GET['user_name'] ) )
             $user_name = $_GET['user_name'];
         $data['work'] = '<b>User l&ouml;schen</b><br />';
         $data['work'] .= '<form action="useredit.php" method="post">
          <table cellpadding="4" cellspacing="0" border="0">
           <tr>
            <td id="blank" style="width:300px">Username</td>
            <td id="blank">
             <input type="text" name="user_name" value="'.$user_name.'" id="border-tab" />
            </td>
           </tr>
           <tr>
            <td id="blank">&nbsp;</td>
            <td id="blank">
             <input type="hidden" name="action" value="deluser" />
             <input type="submit" name="send" value=" l&ouml;schen " id="border-tab" />
            </td>
           </tr>
          </table>
          </form>';
     }
 }
 // searchform ----------------------------------
 if( $action == 'searchform' )
 {
     $data['work'] = '<b>User suchen</b><br />';
     $data['work'] .= '<br />Wenn Du keines der Felder ausf&uuml;llst werden alle User aufgelistet<br /><br />
     <form action="useredit.php" method="post">
     <table cellpadding="4" cellspacing="0" border="0">
      <tr>
       <td id="blank" style="width:150px">
        <i>Suchfeld</i>
       </td>
       <td id="blank">
        <i>Art der Suche</i>
       </td>
       <td id="blank">&nbsp;</td>
       <td id="blank">
        <i>Parameter</i>
       </td>
      </tr>';
     foreach( $search_user as $field )
     {
         $data['work'] .= '<tr>
               <td id="blank"><b>'.$field[1].'</b></td>
               <td id="blank">';
         if( $field[2] == 1 )
         {
             $data['work'] .= '
                <select size="1" name="lookhow['.$field[0].']" size="1" id="tab">
                 <option value="genau">genau</option>
                 <option value="groesser">gr&ouml;&szlig;er</option>
                 <option value="kleiner">kleiner</option>
                </select>
               </td>
               <td id="blank">&nbsp;</td>
               <td id="blank"><input type="text" size="6" maxlength="10" name="lookfor['.$field[0].']" id="border-tab" /></td>
             ';
         }
         elseif( $field[2] == 2 )
         {
             $data['work'] .= '
                <select size="1" name="lookhow['.$field[0].']" size="1" id="tab">
                 <option value="teil">enthalten</option>
                 <option value="exakt">exakt</option>
                </select>
               </td>
               <td id="blank">&nbsp;</td>
               <td id="blank"><input type="text" name="lookfor['.$field[0].']" id="border-tab" /></td>
             ';
         }
         else
         {
             $data['work'] .= '
                <select size="1" name="lookhow['.$field[0].']" size="1" id="tab">
                 <option value="vor">vor</option>
                 <option value="nach">nach</option>
                </select>
               </td>
               <td id="blank">&nbsp;</td>
               <td id="blank"><input type="text" maxlength="10" name="lookfor['.$field[0].']" id="border-tab" /> <i>Format (TT.MM.JJJJ)</i></td>
             ';
         }
         $data['work'] .= '</tr>';
     }
     $data['work'] .= '
      <tr><td id="blank" colspan="2">&nbsp;</td></tr><tr>
       <td id="blank" colspan="2">&nbsp;</td>
       <td id="blank" colspan="2"><input type="submit" value=" Suchen " id="border-tab" /></td>
      </tr></table>
      <input type="hidden" name="action" value="searchuser" />
      </form>
     ';
 }
 // searchuser ----------------------------------
 if( $action == 'searchuser' )
 {
     $where = 'WHERE 1';
     $lookhow = $_POST['lookhow'];
     foreach( $_POST['lookfor'] as $key=>$value )
     {
         if( !empty($value) )
         {
             $where .= " AND ".$key;
             $value = addslashes($value);
             switch( $lookhow[$key] )
             {
                 case 'genau':
                     $where .= "='$value'";
                     break;
                 case 'kleiner':
                     $where .= "<'$value'";
                     break;
                 case 'groesser':
                     $where .= ">'$value'";
                     break;
                 case 'teil':
                     $where .= " LIKE '%$value%'";
                     break;
                 case 'exakt':
                     $where .= "='$value'";
                     break;
                 case 'vor':
                 case 'nach':
                     $day = (int)(substr($value, 0, 2));
                     $mon = (int)(substr($value, 3, 2));
                     $year = (int)(substr($value, 6, 4));
                     $timestamp = mktime(0, 0, 0, $mon, $day, $year);
                     if( $lookhow[$key] == 'vor' )
                         $where .= " < $timestamp";
                     else
                         $where .= " > $timestamp";
             }
         }
     }
     $r_finduser = db_query("SELECT
         user_id,
         user_name,
         user_mail
     FROM ".$pref."user ".$where." ORDER BY user_name ASC");
     if( db_rows( $r_finduser ) < 1 )
     {
         $data['work'] = '<b>Ergebnis:</b> Kein User gefunden.';
     }
     else
     {
         $data['work'] = '<b>Ergebnis:</b> '.db_rows( $r_finduser ).' User gefunden.<br /><br />
          <form action="useredit.php" name="form" method="post">
          <table cellpadding="3" cellspacing="1" border="0">
          <tr>
           <td id="blank">&nbsp;</td>
           <td id="blank"><i>User ID</i></td>
           <td id="blank" style="width:150px"><i>Username</i></td>
           <td id="blank" style="width:250px"><i>Email-Adresse</i></td>
           <td id="blank"><i>Optionen</i></td>
          </tr>';
         $i=0;
         while( $found = db_result( $r_finduser ) )
         {
             $data['work'] .= '
              <tr bgcolor="'.($i % 2 == 0 ? '#DADADA' : '#E5E5E5').'">
               <td id="blank"><input type="checkbox" name="userid['.$found['user_id'].']" value="1" /></td>
               <td id="blank"><b>'.$found['user_id'].'</b></td>
               <td id="blank">'.$found['user_name'].'&nbsp;</td>
               <td id="blank">'.$found['user_mail'].'&nbsp;&nbsp;</td>
               <td id="blank">
                <a href="useredit.php?action=editUser&u_id='.$found['user_id'].'">Edit</a>
                | <a href="useredit.php?action=deluser&user_name='.$found['user_name'].'&send=1">L&ouml;schen</a>
               </td>
              </tr>';
             $i++;
         }
         $data['work'] .= '</table></form>';
     }
 }
 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>