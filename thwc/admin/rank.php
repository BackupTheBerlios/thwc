<?php
/* $Id: rank.php,v 1.1 2003/06/12 13:59:31 master_mario Exp $ */
 include( 'adhead.inc.php' );

 if( $config['ranks'] == 0 )
 {
     $data['work'] = '<b>R&auml;nge</b><br /><br />
      ...sind nicht aktiviert. Aktivieren kannst Du sie <a href="basics.php">hier</a>.';
 }
 else
 {
     $data['work'] = '<b>Rangliste</b><br /><br />';
     // updateRanks ------------------------------------------------
     if( $action == 'updateRanks' )
     {
         $rank = $_POST['rank'];
         foreach( $rank as $key=>$value )
         {
             if( isset( $value['delete'] ) )
             {
                 db_query("DELETE FROM ".$pref."ranks WHERE rankid='$key'");
             }
             else
             {
                 db_query("UPDATE ".$pref."ranks SET
                     ranktitle='".addslashes($value['title'])."',
                     rankimage='".addslashes($value['image'])."',
                     post_counts='".intval($value['posts'])."'
                 WHERE rankid='$key'");
             }
         }
         db_query("OPTIMIZE TABLE ".$pref."ranks");
         $data['work'] .= 'R&auml;ge editiert<br /><br />';
         $action = '';
     }
     // addRank -----------------------------------------------
     if( $action == 'addRank' )
     {
         $rank = $_POST['rank'];
         if( $rank['title'] != '' )
         {
             db_query("INSERT INTO ".$pref."ranks SET
                 ranktitle='".addslashes($rank['title'])."',
                 rankimage='".addslashes($rank['image'])."',
                 post_counts='".intval($rank['posts'])."'");
             $data['work'] .= 'Rang hinzugef&uuml;gt<br /><br />';
         }
         $action = '';
     }
     if( $action == '' )
     {
     // listranks
         $r_rank = db_query("SELECT
             *
         FROM ".$pref."ranks ORDER BY post_counts ASC");
         $data['work'] .= '<form action="rank.php" method="post">
          <table cellpadding="3" cellspacing="0" border="0">
           <tr>
            <td id="blank"><i>Titel</i></td>
            <td id="blank">&nbsp;</td>
            <td id="blank"><i>Rangimage<font size="1">(optional)</font></i></td>
            <td id="blank">&nbsp;</td>
            <td id="blank"><i>N&ouml;tige Posts</i></td>
            <td id="blank">&nbsp;</td>
            <td id="blank"><i>l&ouml;schen</i></td>
           </tr>
         ';
         $i=0;
         while( $a_rank = db_result( $r_rank ) )
         {
             $data['work'] .= '<tr bgcolor="'.( $i % 2 == 0 ? '#DADADA' : '' ).'">
               <td id="blank"><input type="text" name="rank['.$a_rank['rankid'].'][title]" maxlength="30" value="'.htmlspecialchars($a_rank['ranktitle']).'" id="border-tab" /></td>
               <td id="blank">&nbsp;</td>
               <td id="blank"><input type="text" name="rank['.$a_rank['rankid'].'][image]" maxlength="128" value="'.htmlspecialchars($a_rank['rankimage']).'" id="border-tab" /></td>
               <td id="blank">&nbsp;</td>
               <td id="blank"><input type="text" name="rank['.$a_rank['rankid'].'][posts]" size="6" maxlength="10" value="'.$a_rank['post_counts'].'" id="border-tab" /></td>
               <td id="blank">&nbsp;</td>
               <td id="blank"><input type="checkbox" name="rank['.$a_rank['rankid'].'][delete]" value="YES" /></td>
              </tr>';
              $i++;
         }
         $data['work'] .= '</table><br />
          <input type="hidden" name="action" value="updateRanks" />';
         if( $i > 0 )
         {
             $data['work'] .= '<input type="submit" value=" Editieren " id="border-tab"/>';
         }
         $data['work'] .= '</form>
          <hr><b>Rang hinzuf&uuml;gen</b><br />
          <form action="rank.php" method="post">
           <table cellpadding="4" cellspacing="0" border="0">
            <tr>
             <td id="blank">Tietel</td>
             <td id="blank">&nbsp;</td>
             <td id="blank"><input type="text" name="rank[title]" maxlength="30" id="border-tab" /></td>
            </tr>
            <tr>
             <td id="blank">Image<font size="1">&nbsp;(optional)</font></td>
             <td id="blank">&nbsp;</td>
             <td id="blank"><input type="text" name="rank[image]" maxlength="128" id="border-tab" /></td>
            </tr>
            <tr>
             <td id="blank">Posts<br /><font size="1">(N&ouml;tige Anzahl an Posts<br />zum Erreichen dieses Rangs.)</font></td>
             <td id="blank">&nbsp;</td>
             <td id="blank"><input type="text" name="rank[posts]" size="6" maxlength="10" id="border-tab" /></td>
            </tr>
           </table>
           <br />
           <input type="hidden" name="action" value="addRank" />
           <input type="submit" value=" Rang hinzuf&uuml;gen " id="border-tab" />
          </form>';
     }
 }

 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>