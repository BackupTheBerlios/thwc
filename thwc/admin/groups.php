<?php
 /* $Id: groups.php,v 1.4 2003/06/17 20:19:21 master_mario Exp $ */
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
 $data['work'] = '';

 /* permission constants */
 define('P_VIEW', 1 << 0);
 define('P_REPLY', 1 << 1);
 define('P_POSTNEW', 1 << 2);
 define('P_CLOSE', 1 << 3);
 define('P_DELTHREAD', 1 << 4);
 define('P_OMOVE', 1 << 5);
 define('P_DELPOST', 1 << 6);
 define('P_EDIT', 1 << 7);
 define('P_OCLOSE', 1 << 8);
 define('P_ODELTHREAD', 1 << 9);
 define('P_ODELPOST', 1 << 10);
 define('P_OEDIT', 1 << 11);
 define('P_TOP', 1 << 12);
 define('P_EDITCLOSED', 1 << 13);
 define('P_IP', 1 << 14);
 define('P_EDITTOPIC', 1 << 15);
 define('P_NOFLOODPROT', 1 << 16);
 define('P_NOEDITLIMIT', 1 << 17);
 define('P_CANSEEINVIS', 1 << 18);
 define('P_NOPMLIMIT', 1 << 19);
 define('P_INTEAM', 1 << 20);
 define('P_CEVENT', 1 << 21);
 define('P_SHOWDELETED', 1 << 22);
 define('P_POLLNEW', 1 << 23);
 define('P_DELPOLL', 1 << 24);
 define('P_CLOSEPOLL', 1 << 25);
 define('P_EDITPOLL', 1 << 26);
 define('P_ODELPOLL', 1 << 27);
 define('P_OCLOSEPOLL', 1 << 28);
 define('P_OEDITPOLL', 1 << 29);
 define('P_OMOVEPOLL', 1 << 30);
 /* and descriptions. ( REQUIRED! )*/
 $p_desc = array(
     P_VIEW => 'Board zeigen?',
     P_REPLY => 'Kann auf Threads antworten?',
     P_POSTNEW => 'Kann neue Threads anlegen?',
     P_CLOSE => 'Kann <u>eigene</u> Threads schliessen?',
     P_DELTHREAD => 'Kann <u>eigene</u> Threads löschen?',
     P_DELPOST => 'Kann <u>eigene</u> Posts löschen?',
     P_EDIT => 'Kann <u>eigene</u> Posts editieren?',
     P_OMOVE => 'Kann Threads verschieben?',
     P_OCLOSE => 'Kann Threads anderer schliessen?',
     P_ODELTHREAD => 'Kann Threads anderer l&ouml;schen?',
     P_ODELPOST => 'Kann Posts anderer l&ouml;schen?',
     P_OEDIT => 'Kann Threads anderer editieren?',
     P_TOP => 'Kann Threads *fest* machen',
     P_EDITCLOSED => 'Kann Posts in geschlossenen Threads editieren?',
     P_IP => "IP's zeigen?",
     P_EDITTOPIC => 'Kann Threadtopics editieren?',
     P_NOFLOODPROT => 'Keine flood protection?',
     P_NOEDITLIMIT => 'Kein Zeitlimmit f&uuml;r Edits?',
     P_CANSEEINVIS => 'Kann unsichtbare User sehen?',
     P_NOPMLIMIT => "Kein Limit f&uuml;r PM's?",
     P_INTEAM => 'Auf der Teampage anzeigen?',
     P_CEVENT => 'Kann neue Kalendereintr&auml;ge machen?',
     P_SHOWDELETED => 'Kann gel&ouml;schte Posts und Threads sehen?',
     P_POLLNEW => 'Kann neue Umfragen erstellen?',
     P_DELPOLL => 'Kann <u>eigene</u> Umfragen l&ouml;schen?',
     P_CLOSEPOLL => 'Kann <u>eigene</u> Umfragen schliessen',
     P_EDITPOLL => 'Kann <u>eigene</u> Umfragen editieren',
     P_ODELPOLL => 'Kann Umfragen anderer l&ouml;schen?',
     P_OCLOSEPOLL => 'Kann Umfragen anderer schliessen?',
     P_OEDITPOLL => 'Kann Umfragen anderer editieren?',
     P_OMOVEPOLL => 'Kann Umfragen verschieben?',       // nicht mehr verwendet !!!!!!!!!!!
 );

 $p_globalonly = array(
     P_CANSEEINVIS => 1,
     P_NOPMLIMIT => 1,
     P_INTEAM => 1,
     P_CEVENT => 1
 );
 /*FUNKTIONEN*/
 function grouplist_remove(&$list, $groupid)
 {
     $a_groupid = explode(',', $list);
     $a_new = array();
     while( list(, $gid) = each($a_groupid) )
     {
         if( $gid != $groupid )
             $a_new[] = $gid;
     }
     $list = implode(',', $a_new);
 }
 function print_perms($accessmask, $color = '', $global = 0)
 {
     global $p_desc, $p_globalonly;
     reset($p_desc);
     $back = '';
     while( list($k, $v) = each($p_desc) )
     {
         if( isset($p_globalonly[$k]) && !$global )
         {
              $back .= '<td id="blank" style="text-align:center">-</td>';
         }
         else
         {
             if( $accessmask & $k )
             {
                 $back .= '<td id="blank" style="text-align:center"><font color="'.$color.'">J</font></td>';
             }
             else
             {
                  $back .= '<td id="blank" style="text-align:center"><font color="'.$color.'">N</font></td>';
             }
         }
     }
 return $back;
 }
 function group_form ( $group, $action )
 {
     global $p_desc;

     if( !isset( $group['name'] ) )
         $group['name'] = '';
     if( !isset( $group['title'] ) )
         $group['title'] = '';
     if( !isset( $group['titlepriority'] ) )
         $group['titlepriority'] = '';
     if( !isset( $group['accessmask'] ) )
         $group['accessmask'] = '';
     if( !isset( $group['groupid'] ) )
         $group['groupid'] = '';

     $form = '<form action="groups.php" method="post">
      <table width="100%" border="0" cellspacing="0" cellpadding="4">
       <tr>
        <td id="blank"><b>Gruppenname (intern)</b></td>
        <td id="blank">
         <input type="text" name="name" value="'.htmlspecialchars($group['name']).'" id="border-tab" />
        </td>
       </tr>
       <tr>
        <td id="blank"><b>Gruppentitel</b></td>
        <td id="blank">
         <input type="text" name="title" value="'.htmlspecialchars($group['title']).'" id="border-tab" />
        </td>
       </tr>
       <tr>
        <td id="blank"><b>Gruppentitel (Priorit&auml;t)</b><br><font size="1">F&uuml;r User die in mehr als einer Gruppr vertreten sind.<br> Der Titel (wenn gesetzt) der Gruppe mit der h&ouml;chsten<br> Priorit&auml;t wird angezeigt.<br> Zahlen zwischen 0 und 999 sind m&ouml;glich.</font></td>
        <td id="blank">
         <input type="text" size="4" name="titlepriority" value="'.htmlspecialchars($group['titlepriority']).'" id="border-tab" />
        </td>
       </tr>
       <tr>
        <td id="blank">Rechte:</td>
        <td id="blank"></td>
       </tr>';
       $i = 0;
       foreach( $p_desc as $key=>$value )
       {
           $form .= '<tr>
            <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').'>'.$value.'</td>
            <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').'>
             <input type="radio" name="p'.$key.'" value="yes"'.($key & $group['accessmask'] ? ' checked' : '' ).' />
             Ja&nbsp;&nbsp;&nbsp;
             <input type="radio" name="p'.$key.'" value="no"'.($key & $group['accessmask'] ? '' : ' checked' ).' />
             Nein
            </td>
           </tr>';
           $i++;
        }
        $form .= '<tr>
         <td id="blank">&nbsp;</td>
         <td id="blank"></td>
        </tr><tr>
         <td colspan="2" id="blank" style="text-align:center" />
          <input type="hidden" name="groupid" value="'.$group['groupid'].'" />
          <input type="hidden" name="action" value="'.$action.'" />
          <input type="submit" name="submit" value="Send" id="border-tab"/>
         </td>
        </tr>
        </table>
        </form>';
        return $form;
 }
 function groupboard_form($group, $board, $useglobal)
 {
     global $p_desc, $p_globalonly;

     if( !isset( $group['name'] ) )
         $group['name'] = '';
     if( !isset( $group['title'] ) )
         $group['title'] = '';
     if( !isset( $group['titlepriority'] ) )
         $group['titlepriority'] = '';
     if( !isset( $group['accessmask'] ) )
         $group['accessmask'] = '';
     if( !isset( $group['groupid'] ) )
         $group['groupid'] = '';

     $back = '<form name="theform" method="post" action="groups.php">
      <table width="100%" border="0" cellspacing="0" cellpadding="4">
       <tr>
        <td id="blank" colspan="2">Edit Rechte f&uuml;r Gruppe &quot;<b>'.$group['name'].'</b>&quot; und Board &quot;<b>'.$board['board_name'].'</b>&quot;</td>
        <td id="blank">
         &nbsp;
        </td>
       </tr>
       <tr>
        <td id="blank" colspan="2">
         <input type="radio" name="useglobal" value="yes"'.($useglobal ? ' checked' : '').' />
          Dieses Board nutzt globale Rechte. (Settings werden ignoriert)<br>
         <input type="radio" name="useglobal" value="no"'.($useglobal ? '' : ' checked').' />
          Dieses Board nutzt individuelle Rechte f&uuml;r diese Gruppe</td>
        <td id="blank"></td>
       </tr>';
     $i = 0;
     foreach( $p_desc as $k=>$v )
     {
         if( !isset($p_globalonly[$k]) )
         {
             $back .= '<tr'.($i % 2 == 0 ? '' : ' bgcolor="#E2E2E2"').'>
              <td id="blank">'.$v.'</td>
              <td id="blank">
               <input type="radio" name="p'.$k.'" value="yes"'.($k & $group['accessmask'] ? ' checked' : '' ).' />
                Ja&nbsp;&nbsp;&nbsp;
               <input type="radio" name="p'.$k.'" value="no"'.($k & $group['accessmask'] ? '' : ' checked' ).' />
                Nein
              </td>
             </tr>';
             $i++;
         }
     }
     $back .= '
      <tr>
       <td id="blank">&nbsp;</td>
       <td id="blank"></td>
      </tr><tr>
       <td id="blank" colspan="2" style="text-align:center">
        <input type="hidden" name="groupid" value="'.$group['groupid'].'" />
        <input type="hidden" name="boardid" value="'.$board['board_id'].'" />
        <input type="hidden" name="action" value="updategroupboard" />
        <input type="submit" name="submit" value="Send" id="border-tab"/>
       </td>
      </tr>
     </table>
    </form>';
 return $back;
 }
 /* CODE */
 if( $action == 'list' )
 {
     $r_group = db_query("SELECT
         groupid,
         name,
         accessmask,
         nodelete
     FROM ".$pref."group ORDER BY name ASC");

     $data['work'] = '
      <form action="groups.php" method="post">
       <table border="0" cellspacing="1" cellpadding="3">
        <tr>
         <td colspan="2" id="blank"><b>Spezielle Gruppen</b></td>
        </tr>
        <tr>
        <td id="blank">Gruppe f&uuml;r neue User</td>
        <td id="blank">
         <select name="default_groupid">';
     while( $group = db_result( $r_group ) )
     {
         $data['work'] .= '<option value="'.$group['groupid'].'"'.($config['default_groupid'] == $group['groupid'] ? ' selected' : '').'>'.$group['name'].'</option>';
     }
     $data['work'] .= '
         </select>
        </td>
       </tr>
       <tr>
        <td id="blank">G&auml;stegruppe (user die nicht eingeloggt sind)</td>
        <td id="blank">
         <select name="guest_groupid">';
     mysql_data_seek($r_group, 0);
     while( $group = db_result( $r_group ) )
     {
         $data['work'] .= '<option value="'.$group['groupid'].'"'.($config['guest_groupid'] == $group['groupid'] ? ' selected' : '').'>'.$group['name'].'</option>';
     }
     $data['work'] .= '
         </select>
        </td>
       </tr>
       <tr align="right">
        <td colspan="2" id="blank">
         <input type="hidden" name="action" value="set_default_groups" />
         <input type="submit" name="done" value="Done" />
        </td>
       </tr>
       <tr>
        <td colspan="2" id="blank"> </td>
       </tr>
      </table>
     </form>';
     $data['work'] .= '
     <table width="100%" border="0" cellspacing="0" cellpadding="4">
      <tr>
       <td id="blank"><b>Gruppenname</b></td>
       <td id="blank"><b>Members</b></td>
       <td id="blank"><b>Optionen</b></td>
      </tr>';
      $i = 0;
      mysql_data_seek($r_group, 0);
      while( $group = db_result( $r_group ) )
      {
          if( $group['groupid'] == $config['guest_groupid'] )
          {
              $data['work'] .= '<tr'.($i % 2 == 0 ? '' : ' bgcolor="#E2E2E2"').'>
               <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').' valign="top">'.$group['name'].'</td>
               <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').' valign="top"><font size="1">(nicht eingeloggte User)</font></td>
               <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').'>';
          }
          else
          {
              $r_user = db_query("SELECT
                  count(user_id) AS count
              FROM ".$pref."user WHERE INSTR(groupids, ',$group[groupid],')>0");
              $user = db_result( $r_user );
              $data['work'] .= '<tr>
               <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').' valign="top">'.$group['name'].'</td>
               <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').' valign="top">'.$user['count'].' member(s) - <a href="groups.php?action=listmembers&groupid='.$group['groupid'].'">list</a></td>
               <td id="blank"'.($i % 2 == 0 ? ' class="cellb"' : ' class="cella"').'>';
          }
          $data['work'] .= '<font size="1">
           <a href="groups.php?action=boardpermtable&groupid='.$group['groupid'].'">View/Edit Rechte</a><br>
           <a href="groups.php?action=delete&groupid='.$group['groupid'].'">Gruppe l&ouml;schen</a><br></font> ';
          $data['work'] .= '</td>
           </tr>';
          $i++;
      }
      $data['work'] .= '</table><br><br>';
 }
 elseif( $action == 'listmembers' )
 {
     $r_group = db_query("SELECT
         name
     FROM ".$pref."group WHERE groupid='$_GET[groupid]'");
     $group = db_result( $r_group );
     $data['work'] = 'Memberlisting f&uuml;r Gruppe: "<b>'.$group['name'].'</b>"<br><br>';
     $r_user = db_query("SELECT
         user_name,
         user_id
     FROM ".$pref."user WHERE INSTR(groupids, ',$_GET[groupid],')>0 ORDER BY user_name ASC");
     if( db_rows( $r_user ) == 0 )
     {
         $data['work'] .= 'Diese Gruppe hat keine Mitglieder.';
     }
     else
     {
          while( $user = db_result( $r_user ) )
          {
              $data['work'] .= htmlspecialchars($user['user_name']).' - <a href="useredit.php?action=editUser&u_id='.$user['user_id'].'">edit</a><br>';
          }
     }
 }
 elseif( $action == 'create' )
 {
     $data['work'] = '<b>Create new group</b><br><br>';
     $data['work'] .= group_form(array(), 'insert');
 }
 elseif( $action == 'insert' )
 {
     if( !$_POST['name'] )
     {
         $data['work'] = 'Bitte gib einen Gruppennamen an.';
     }
     else
     {
         $accessmask = 0;
         /* create accessmask */
         foreach( $p_desc as $key=>$value )
         {
             if( $_POST['p'.$key] == 'yes' )
             {
                 $accessmask |= $key;
             }
         }
         db_query("INSERT INTO ".$pref."group SET
             name='".addslashes($_POST['name'])."',
             accessmask='".$accessmask."',
             title='".addslashes($_POST['title'])."',
             titlepriority='".$_POST['titlepriority']."'");
         $data['work'] = 'Gruppe wurde angelegt!';
     }
 }
 elseif( $action == 'delete' )
 {
     $r_group = db_query("SELECT
         nodelete,
         name
     FROM ".$pref."group WHERE groupid='".$_GET['groupid']."'");
     $group = db_result( $r_group );
     /* WARNING: do NOT remove this check unless you know what youre doing .. */
     if( $_GET['groupid'] == $config['default_groupid'] || $_GET['groupid'] == $config['guest_groupid'] )
     {
         $data['work'] = 'Sorry, you cannot delete this group (Are you trying to delete the default or guest group?).';
     }
     else
     {
         $data['work'] = 'You are going to delete "'.$group['name'].'". Are you sure? (Group members will be removed from group)<br><br><a href="groups.php?action=drop&groupid='.$_GET['groupid'].'">Yes</a>';
     }
 }
 elseif( $action == 'edit' )
 {
     $r_group = db_query("SELECT
         groupid,
         name,
         accessmask,
         title,
         priority
     FROM ".$pref."group WHERE groupid='".$_GET['groupid']."'");
     $group = db_result( $r_group );
     $data['work'] = '<b>Edit group</b><br><br>';
     $data['work'] .= group_form($group, 'update');
 }
 elseif( $action == 'editgroupboard' )
 {
     $r_group = db_query("SELECT
         groupid,
         name,
         accessmask
     FROM ".$pref."group WHERE groupid='$_GET[groupid]'");
     $group = db_result( $r_group );

     $r_board = db_query("SELECT
         board_id,
         board_name
     FROM ".$pref."board WHERE board_id='$_GET[boardid]'");
     $board = db_result($r_board);

     $r_groupboard = db_query("SELECT
         groupid,
         accessmask
     FROM ".$pref."groupboard WHERE groupid='$_GET[groupid]' AND boardid='$_GET[boardid]'");
     if( db_rows( $r_groupboard ) > 0 )
     {
         $groupboard = db_result( $r_groupboard );
         $group['accessmask'] = $groupboard['accessmask'];
         $data['work'] = groupboard_form($groupboard, $board, false);
     }
     else
     {
         $data['work'] = groupboard_form($group, $board, false);
     }
 }
 elseif( $action == 'drop' )
 {
     /* put members into the default group */
     /*query("UPDATE $pref"."user SET groupid=$config[default_groupid] WHERE groupid='$groupid'");*/
     $r_user = db_query("SELECT
         user_id,
         groupids
     FROM ".$pref."user WHERE INSTR(groupids, ',$_GET[groupid],')>0");
     while( $user = db_result( $r_user ) )
     {
         $user['groupids'] = substr($user['groupids'], 1, strlen($user['groupids']) - 2);
         grouplist_remove($user['groupids'], $_GET['groupid']);
         $user['groupids'] = ','.$user['groupids'].',';
         db_query("UPDATE ".$pref."user SET
             groupids='$user[groupids]'
         WHERE userid=$user[user_id]");
     }
     /* delete the group. */
     db_query("DELETE FROM ".$pref."group WHERE groupid='$_GET[groupid]'");
     /* delete group/board*/
     db_query("DELETE FROM ".$pref."groupboard WHERE groupid='$_GET[groupid]'");
     $data['work'] = 'Group has been deleted!';
 }
 elseif( $action == 'updategroupboard' )
 {
     if( $_POST['useglobal'] == 'yes' )
     {
         db_query("DELETE FROM ".$pref."groupboard WHERE groupid='$_POST[groupid]' AND boardid='$_POST[boardid]'");
         $data['work'] = 'Settings gespeichert.<br><br><a href="groups.php?action=boardpermtable&groupid='.$_POST['groupid'].'">Back</a>';
     }
     else
     {
         // delete old perms, no matter whether they exist or not ..
         db_query("DELETE FROM ".$pref."groupboard WHERE groupid='$_POST[groupid]' AND boardid='$_POST[boardid]'");
         $accessmask = 0;
         /* create accessmask */
         foreach( $p_desc as $k=>$v )
         {
             if( !isset($p_globalonly[$k]) )
             {
                 if( $_POST['p'.$k] == 'yes' )
                 {
                     $accessmask |= $k;
                 }
             }
         }
         // insert new
         db_query("INSERT INTO ".$pref."groupboard SET
             groupid='$_POST[groupid]',
             boardid='$_POST[boardid]',
             accessmask='$accessmask'");
         $data['work'] = 'Settings gespeichert.<br><br><a href="groups.php?boardid='.$_POST['boardid'].'&groupid='.$_POST['groupid'].'&action=boardpermtable">Zur&uuml;ck</a>';
     }
 }
 elseif( $action == 'update' )
 {
     if( !$_POST['name'] )
     {
         $data['work'] = '<b>Fehler</b><br><br>Gib bitte einen Gruppennamen an.';
     }
     else
     {
         $accessmask = 0;
         /* create accessmask */
         foreach( $p_desc as $k=>$v )
         {
             if( $_POST['p'.$k] == 'yes' )
             {
                 $accessmask |= $k;
             }
         }
         db_query("UPDATE ".$pref."group SET
             name='".addslashes($_POST['name'])."',
             accessmask='$accessmask',
             title='".addslashes($_POST['title'])."',
             priority='$_POST[titlepriority]'
         WHERE groupid='$_POST[groupid]'");
         $data['work'] = 'Gruppe wurde editiert!';
     }
 }
 elseif( $action == 'boardpermtable' )
 {
     $i = 0;
     // select groups global perms
     $r_group = db_query("SELECT
         name,
         accessmask
     FROM ".$pref."group WHERE groupid='$_GET[groupid]'");
     $group = db_result( $r_group );
     $data['work'] = 'Board/Rechtetabelle f&uuml;r Gruppe: <b>'.$group['name'].'</b><br><br>';
     $data['work'] .= '<br><font color="#000066">Blue</font> - Board nutzt globale Rechte<br>
      <font color="#990000">Red</font> - Board nutzt Rechte zugewiesene Rechte
       <br><br>';
     // board-perm
     $r_groupboard = db_query("SELECT
         boardid,
         accessmask
     FROM ".$pref."groupboard WHERE groupid='$_GET[groupid]'");
     $a_groupboard = array();
     while( $groupboard = db_result( $r_groupboard ) )
     {
         $a_groupboard[$groupboard['boardid']] = $groupboard['accessmask'];
     }
     // boards
     $r_board = db_query("SELECT
         board_id,
         board_name,
         category
     FROM ".$pref."board WHERE category > '0' ORDER BY board_order ASC");
     $a_board = array();
     while( $board = db_result( $r_board ) )
     {
         $a_board[$board['category']][] = $board;
     }
     $data['work'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="4">';
     // print header
     $data['work'] .= '<tr>';
     $data['work'] .= '<td id="blank"></td>';
     while( list($k, $v) = each($p_desc) )
     {
         $data['work'] .= '<td id="blank" style="width:20px; text-align:center"><img src="./images/pbar_'.($k).'.png"></td>';
     }
     $data['work'] .= '</tr>';
     // global perms
     $data['work'] .= '<tr>';
     $data['work'] .= '<td id="blank"><i>Global permissions - <a href="groups.php?action=edit&groupid='.$_GET['groupid'].'">Modify</a></i></td>';
     $data['work'] .= print_perms($group['accessmask'], '#000000', 1);
     $data['work'] .= '</tr>';
     // select categories
     $r_category = db_query("SELECT
         category_id,
         category_name
     FROM ".$pref."category ORDER BY category_order ASC");
     while( $category = db_result( $r_category ) )
     {
         $data['work'] .= '<tr bgcolor="#E1E1E1"><td id="blank" colspan="'.(count($p_desc) + 1).'"><b>Category: '.htmlspecialchars($category['category_name']).'</b></td></tr>';
         while( list(, $board) = @each($a_board[$category['category_id']]) )
         {
             $data['work'] .= '<tr><td id="blank">'.htmlspecialchars($board['board_name']).'<br><font size="1"><a href="groups.php?action=editgroupboard&boardid='.$board['board_id'].'&groupid='.$_GET['groupid'].'">Modify permissions ...</a></font></td>';
             if( isset($a_groupboard[$board['board_id']]) )
             {
                 // custom perms
                 $data['work'] .= print_perms($a_groupboard[$board['board_id']], '#800000');
             }
             else
             {
                 // global
                 $data['work'] .= print_perms($group['accessmask'], '#000080');
             }
             $data['work'] .= '</tr>';
         }
     }
     $data['work'] .= '</table>';
 }
 elseif( $action == 'grouppermtable' )
 {
     $r_board = db_query("SELECT
         board_name
     FROM ".$pref."board WHERE board_id='$_GET[boardid]'");
     list($boardname) = mysql_fetch_row($r_board);
     $data['work'] = 'Gruppen/Rechte Tabelle f&uuml;r Board: <b>'.$boardname.'</b><br><br>';
     $data['work'] .= '<br><font color="#000066">Blau</font> - Board nutzt globale Rechte<br>
      <font color="#990000">Rot</font> - Board nutzt abweichende Rechte
      <br><br>';
     /* global perms */
     $a_group = array();
     $r_group = db_query("SELECT
         groupid,
         name,
         accessmask
     FROM ".$pref."group");
     while( $group = db_result( $r_group ) )
     {
         $a_group[$group['groupid']] = $group;
     }
     /* custom */
     $a_groupboard = array();
     $r_groupboard = db_query("SELECT
         groupid,
         accessmask
     FROM ".$pref."groupboard WHERE boardid='$_GET[boardid]'");
     while( $groupboard = db_result( $r_groupboard ) )
     {
         $a_groupboard[$groupboard['groupid']] = $groupboard;
     }
     $data['work'] .= '<table width="100%" border="0" cellspacing="0" cellpadding="4">';
     /* header */
     $data['work'] .= '<tr><td id="blank"></td>';
     while( list($k, $v) = each($p_desc) )
     {
         $data['work'] .= '<td id="blank" style="width:20px; text-align:center"><img src="./images/pbar_'.($k).'.png"></td>';
     }
     $data['work'] .= '</tr>';
     /* group rows */
     while( list(, $group) = each($a_group) )
     {
         $data['work'] .= '<tr><td id="blank">'.htmlspecialchars($group['name']).'<br><font size="1"><a href="groups.php?action=editgroupboard&boardid='.$_GET['boardid'].'&groupid='.$group['groupid'].'">Modify permissions ...</a></font></td>';
         if( isset($a_groupboard[$group['groupid']]) )
         {
             // custom perms
             $data['work'] .= print_perms($a_groupboard[$group['groupid']]['accessmask'], '#800000');
         }
         else
         {
             // global
             $data['work'] .= print_perms($group['accessmask'], '#000080');
         }
         $data['work'] .= '</tr>';
     }
     $data['work'] .= '</table>';
 }
 elseif( $action == 'set_default_groups' )
 {
     db_query("UPDATE ".$pref."registry SET
         keyvalue='$_POST[default_groupid]'
     WHERE keyname='default_groupid'");
     db_query("UPDATE ".$pref."registry SET
         keyvalue='$_POST[guest_groupid]'
     WHERE keyname='guest_groupid'");
     $data['work'] = 'Default groups have been set.';
 }

 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>