<?php
 /* $Id: index.php,v 1.2 2003/06/20 10:41:47 master_mario Exp $ */
 include( 'adhead.inc.php' );
 // deleteAdlog ---------------------------------------------------------------------------
 if( $action == 'deleteAdlog' )
 {
     if( $admin['is_uradmin'] != 1 )
         $data['work'] = 'Dazu hast Du kein Recht, das darf nur der UrAdmin.';
     else
     {
         if( isset( $delete ) )
		 {
             foreach( $delete as $key=>$value )
             {
                 db_query("DELETE FROM ".$pref."adlog WHERE logtime='$key'");
             }
             db_query("OPTIMIZE TABLE ".$pref."adlog");
		 }
     }
 }
 $data['work'] = '
  <b>Adlog</b><br /><br />
  <center>';
 if( $admin['is_uradmin'] == 1 )
 {
     $data['work'] .= '<form action="index.php" method="post">';
 }
 $data['work'] .= '<table cellpadding="2" cellspacing="0" border="0">
   <tr>
    <td><i>Zeit</i></td>
    <td><i>User</i></td>
    <td><i>IP</i></td>
    <td><i>File</i></td>
    <td><i>Action</i></td>';
 if( $admin['is_uradmin'] == 1 )
 {
     $data['work'] .= '<td>&nbsp;</td><td>&nbsp;</td>';
 }
 $data['work'] .= '</tr>';
 $r_log = db_query("SELECT
     *
 FROM ".$pref."adlog ORDER BY logtime DESC");
 $i=0;
 while( $log = db_result( $r_log ) )
 {
     $data['work'] .= '
      <tr bgcolor="'.( $i % 2 == 0 ? '#D5D5D5' : '' ).'">
       <td id="blank">'.date( "d.m.Y H:i:s", $log['logtime'] ).'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['loguser'].'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['logip'].'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['logfile'].'&nbsp;&nbsp;&nbsp;</td>
       <td id="blank">'.$log['action'].'</td>';
     if( $admin['is_uradmin'] == 1 )
     {
         $data['work'] .= '<td>&nbsp;</td><td><input type="checkbox" name="delete['.$log['logtime'].']" value="1"'.( isset($_GET['selectAll']) ? ' checked' : '' ).'/></td>';
     }
     $data['work'] .= '</tr>';
     $i++;
 }

 $data['work'] .= '</table>';
 if( $admin['is_uradmin'] == 1 )
 {
     $data['work'] .= '<input type="hidden" name="action" value="deleteAdlog" /><br />
      <input type="submit" value=" makierte l&ouml;schen " id="border-tab" /></form>
      <a href="index.php?selectAll=1">Alle makieren</a>';
 }
 $data['work'] .= '</center>';

 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>