<?php
/* $Id: style.php,v 1.1 2003/06/12 13:59:33 master_mario Exp $ */
 include( 'adhead.inc.php' );
 $data['work'] = '<b>Styles</b><br /><br />';

 $style_array = array(
     array( 'stylename', '', 'Stylename', '', 1, 0 ),
     array( '', 'Layout', '', '', 0, 1 ),
     array( 'styletemplate', '', 'Templateset', 'Verwendete Html-Styles', 2, 0 ),
     array( '', 'Main', '', 'Hintergrundfarbe und Textfarbe au&szlig;erhalb der Tabellen', 0, 1 ),
     array( 'colorbg', '', 'Hintergrundfarbe', '', 3, 0 ),
     array( 'colorbgfont', '', 'Textfarbe', 'Text au&szlig;erhalb der Tabellen', 3, 0 ),
     array( '', 'Tabellen Kopf,- und Fu&szlig;zeilen', '', '', 0, 1 ),
     array( 'color4', '', 'Hintergrundfarbe', '', 3, 0 ),
     array( 'col_he_fo_font', '', 'Textfarbe', '', 3, 0 ),
     array( '', 'Tabellen', '', 'Betrifft die Inhalte aller Tabellen abgesehen der Kopf-, und Fu&szlig;zeilen', 0, 1 ),
     array( 'color1', '', 'Textfarbe', '', 3, 0 ),
     array( 'CellA', '', 'Zellenhintergrund', '', 3, 0 ),
     array( 'CellB', '', 'Alternative Farbe f&uuml;r Zellenhintergrund', '', 3, 0 ),
     array( 'border_col', '', 'Rahmenfarbe', 'Farbe zwischen den Zellen', 3, 0 ),
     array( 'color_err', '', 'Fehlermeldungen', 'Farbe f&uuml;r Fehlermeldungen oder wichtige Hinweise', 3, 0 ),
     array( 'col_link', '', 'Link', '', 3, 0 ),
     array( 'col_link_v', '', 'Link', 'Farbe f&uuml;r besuchte Links', 3, 0 ),
     array( 'col_link_hover', '', 'Hover', 'Links &auml;ndern die Farbe wenn sie mit der Maus *&uuml;berfahren* werden.', 3, 0 ),
     array( 'stdfont', '', 'Sriftart', '( z.B. verdana, Arial, Helvetica, ... )', 4, 0 ),
     array( 'styleispublic', '', 'Publicstyle', 'Gibt Style f&uuml;r die Auswahl durch User frei.', 5, 0 ),
     array( '', 'Imagepfade(optional)', '', 'Pfade zu Bildern f&uuml;r verschiedene Verwendungen. Wenn keine Bilder verwendet werden sollen, die Felder leer lassen.<br />
     Wenn im Header nur ein Bild ohne Text angezeigt werden soll, dann bei den Basics das Feld *Headername* leer lassen.', 0, 1 ),
     array( 'boardimage', '', 'Boardimage', 'Bild wird oben im Header angezeigt<br />z.B. in Form eines Banners', 4, 0 ),
     array( 'newtopicimage', '', 'Newtopic', 'Bild wird anstelle von *neues Thema erstellen* angezeigt', 4, 0 ),
     array( 'newpollimage', '', 'Newpoll', 'Bild wird anstelle von *neue Umfrage erstellen* angezeigt', 4, 0 ),
     array( '', 'Postimages', '', 'Folgende beziehen sich auf die Leiste unter den Posts. Bitte daran denken das sich dadurch die Ladezeiten erheblich verl&auml;ngern k&ouml;nnen, je nach Gr&ouml;&szlig;e der Bilddatein.', 0, 1 ),
     array( 'profileimage', '', 'Profileimage', 'Bild wird anstelle von *Profil* angezeigt', 4, 0 ),
     array( 'messageimage', '', 'Messageimage', 'Bild wird anstelle von *Privatmessage* angezeigt', 4, 0 ),
     array( 'searchimage', '', 'Searchimage', 'Bild wird anstelle von *Suche* angezeigt', 4, 0 ),
     array( 'quoteimage', '', 'Quoteimage', 'Bild wird anstelle von *Zitatantwort* angezeigt', 4, 0 ),
     array( 'editimage', '', 'Editimage', 'Bild wird anstelle von *Editieren* angezeigt', 4, 0 ),
     array( 'deleteimage', '', 'Deleteimage', 'Bild wird anstelle von *L&ouml;schen* angezeigt', 4, 0 ),
     array( 'reportimage', '', 'Reportimage', 'Bild wird anstelle von *Melden* angezeigt', 4, 0 )
 );
 // FUNKTION
 function styleTemplate ()
 {
     $templateset = array();
     $path = opendir('../templates/');
     while( $file = readdir($path) )
     {
         if( $file!='.' && $file!='..' && $file!='mail' && $file!='CVS' )
         {
             if( is_dir('../templates/'.$file) )
             {
                 $templateset[] = $file;
             }
         }
     }
     return $templateset;
 }
 function styleForm( $style, $action )
 {
     global $style_array;
     $back = '<form action="style.php" method="post">
      <table cellpadding="4" cellspcing="0" border="0">';
     foreach( $style_array as $value )
     {
         $back .= '<tr>';
         if( $value[5] == 0 )
         {
             $back .= '<td style="width:400px; vertical-align:top">'.$value[2].'<br /><font size="1">'.$value[3].'</font></td>';
             if( $value[4] == 1 )
             {
                 $back .= '<td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td>
                  <td style="vertical-align:top">
                   <input type="text" name="style['.$value[0].']" maxlength="32" value="'.( isset($style[$value[0]]) ? $style[$value[0]] : '' ).'" id="border-tab" />
                  </td>';
             }
             if( $value[4] == 2 )
             {
                 $back .= '<td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td>
                  <td style="vertical-align:top">
                   <select name="style[styletemplate]" size="1" id="border-tab">';
                 $styletemplate = styleTemplate();
                 foreach( $styletemplate as $value )
                 {
                     $back .= '<option value="'.$value.'"'.( isset($style['styletemplate']) && $value == $style['styletemplate'] ? ' checked' : '' ).'>'.$value.'</option>';
                 }
                 $back .= '</select>
                  </td>';
             }
             if( $value[4] == 3 )
             {
                 $back .= '<td style="vertical-align:top"><input type="text" size="10" id="style" style="background-color:'.( isset($style[$value[0]]) ? $style[$value[0]] : '#000000' ).';" readonly /></td>
                  <td>&nbsp;&nbsp;&nbsp;</td>
                  <td style="vertical-align:top"><input type="text" size="10" maxlength="7" name="style['.$value[0].']" id="border-tab" value="'.( isset($style[$value[0]]) ? $style[$value[0]] : '' ).'" /></td>';
             }
             if( $value[4] == 4 )
             {
                 $back .= '<td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td>
                  <td style="vertical-align:top">
                   <input type="text" name="style['.$value[0].']" size="30" maxlength="128" value="'.( isset($style[$value[0]]) ? $style[$value[0]] : '' ).'" id="border-tab" />
                  </td>';
             }
             if( $value[4] == 5 )
             {
                 $public = 1;
                 if( isset( $style['styleispublic'] ) )
                     $public = $style['styleispublic'];
                 $back .= '<td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td>
                  <td style="vertical-align:top">
                   <input type="radio" name="style[styleispublic]" value="1"'.( $public == 1 ? ' checked' : '' ).' />&nbsp;Ja
                   &nbsp;&nbsp;<input type="radio" name="style[styleispublic]" value="0"'.( $public == 0 ? ' checked' : '' ).' />&nbsp;Nein
                  </td>';
             }
         }
         else
         {
             $back .= '<td colspan="4"><b>'.$value[1].'</b><br /><font size="1">'.$value[3].'</td>';
         }
         $back .= '<tr>';
     }
     $back .= '<tr>
      <td colspan="4" style="text-align:center">
       <br />
       <input type="hidden" name="action" value="'.$action.'" />
       <input type="hidden" name="style[styleid]" value="'.( isset($style['styleid']) ? $style['styleid'] : '' ).'" />
       <input type="submit" value=" Senden " id="border-tab" />
      </td>
     </tr></table></form>';

     return $back;
 }
 // CODE
 // newStyle _-----------------------------------------------------------
 if( $action == 'newStyle' )
 {
     $data['work'] .= 'Neuen Style anlegen';
     $data['work'] .= styleForm( array(), 'addStyle' );
 }
 // addStyle -------------------------------------------------------------
 if( $action == 'addStyle' )
 {
     db_query("INSERT INTO ".$pref."style SET
         stylename='".addslashes($style['stylename'])."',
         colorbg='$style[colorbg]',
         colorbgfont='$style[colorbgfont]',
         color1='$style[color1]',
         CellA='$style[CellA]',
         CellB='$style[CellB]',
         col_he_fo_font='$style[col_he_fo_font]',
         color4='$style[color4]',
         border_col='$style[border_col]',
         color_err='$style[color_err]',
         col_link='$style[col_link]',
         col_link_v='$style[col_link_v]',
         col_link_hover='$style[col_link_hover]',
         stdfont='".addslashes($style['stdfont'])."',
         boardimage='$style[boardimage]',
         newtopicimage='$style[newtopicimage]',
         styleispublic='$style[styleispublic]',
         styletemplate='$style[styletemplate]',
         newpollimage='$style[newpollimage]',
         profileimage='$style[profileimage]',
         messageimage='$style[messageimage]',
         searchimage='$style[searchimage]',
         quoteimage='$style[quoteimage]',
         editimage='$style[editimage]',
         deleteimage='$style[deleteimage]',
         reportimage='$style[reportimage]'");
     $data['work'] .= '<font color="#990000">Style angelegt.</font><br />';
     $action = '';
 }
 // editStyle _-----------------------------------------------------------
 if( $action == 'editStyle' )
 {
     $r_style = db_query("SELECT
         *
     FROM ".$pref."style WHERE styleid='$styleid'");
     $style = db_result( $r_style );

     $data['work'] .= 'Style editieren';
     $data['work'] .= styleForm( $style, 'updateStyle' );
 }
 // updateStyle -------------------------------------------------------------
 if( $action == 'updateStyle' )
 {
     db_query("UPDATE ".$pref."style SET
         stylename='".addslashes($style['stylename'])."',
         colorbg='$style[colorbg]',
         colorbgfont='$style[colorbgfont]',
         color1='$style[color1]',
         CellA='$style[CellA]',
         CellB='$style[CellB]',
         col_he_fo_font='$style[col_he_fo_font]',
         color4='$style[color4]',
         border_col='$style[border_col]',
         color_err='$style[color_err]',
         col_link='$style[col_link]',
         col_link_v='$style[col_link_v]',
         col_link_hover='$style[col_link_hover]',
         stdfont='".addslashes($style['stdfont'])."',
         boardimage='$style[boardimage]',
         newtopicimage='$style[newtopicimage]',
         styleispublic='$style[styleispublic]',
         styletemplate='$style[styletemplate]',
         newpollimage='$style[newpollimage]',
         profileimage='$style[profileimage]',
         messageimage='$style[messageimage]',
         searchimage='$style[searchimage]',
         quoteimage='$style[quoteimage]',
         editimage='$style[editimage]',
         deleteimage='$style[deleteimage]',
         reportimage='$style[reportimage]' WHERE styleid='$style[styleid]'");
     $data['work'] .= '<font color="#990000">Style editiert.</font><br />';
     $action = '';
 }
 // publicStyle --------------- gild für alle Boards ------------------
 if( $action == 'publicStyle' )
 {
     if( $styleid == 'all' )
     {
         db_query("UPDATE ".$pref."style SET styleispublic='1'");
         $action = '';
     }
     else
     {
         db_query("UPDATE ".$pref."style SET styleispublic='0'");
         $action = '';
     }
 }
 // exStyle ------------------ Style exportieren als Dumpzeile --------
 // ############################ muß noch weiter bearbeitet werden ###############################################
 if( $action == 'exStyle' )
 {
     $r_sty = db_query("SELECT
         *
     FROM ".$pref."style WHERE styleid='$styleid'");
     $sty = db_result( $r_sty );
     $dump = 'INSERT INTO [tabellenname] SET ';
     foreach( $sty as $key=>$value )
     {
         if( $key != 'styleid' )
         {
             $dump .= $key."='".$value."', ";
         }
     }
     $dump = substr( $dump, 0, strlen($dump)-2 ).';';

     $data['work'] .= $dump.'<br /><br /><font color="#990000">Dieser StrinG kann als Befehl direkt in PhpMyAdmin
     verwendet werden.<br />[tabellenname] mu&szlig; entsprechend abgewandelt werden.</font><br /><br />
     <a href="style.php">Zur&uuml;ck</a>';
 }
 // defaultStyle ------------------------------------------------------
 if( $action == 'defaultStyle' )
 {
     db_query("UPDATE ".$pref."style SET styleisdefault='0'");
     db_query("UPDATE ".$pref."style SET styleisdefault='1' WHERE styleid='$styleid'");
     $action = '';
 }
 // deleStyle ------------------------------------------------------
 if( $action == 'deleStyle' )
 {
     db_query("DELETE FROM ".$pref."style WHERE styleid='$styleid'");
     // USER AUF DEFAULT SETZEN WENN SIE DIESEN BENUTZEN #############################################################
     db_query("OPTIMIZE TABLE ".$pref."style");
     $action = '';
 }
 // list styles -------------------------------------------------------
 if( $action == '' )
 {
     $r_list = db_query("SELECT
         styleid,
         stylename,
         styleispublic,
         styleisdefault,
         styletemplate
     FROM ".$pref."style");
     if( db_rows( $r_list ) < 1 )
         $data['work'] = '<font color="#990000">Kein Style angelegt.</font>';
     else
     {
         $data['work'] .= '<a href="style.php?action=publicStyle&styleid=all">Alle als &ouml;ffendlich makieren</a> |
          <a href="style.php?action=publicStyle&styleid=no">Alle als nicht &ouml;ffendlich makieren</a><br /><br />
          <table cellpadding="3" cellspacing="0" border="0">';
         while( $list = db_result( $r_list ) )
         {
             $data['work'] .= '<tr>
              <td>'.$list['stylename'].'</td>
              <td>&nbsp;</td>
              <td><font color="#FF0000">'.( $list['styleisdefault'] == 1 ? '*' : '&nbsp;' ).'</font></td>
              <td><font color="#0000FF">'.( $list['styleispublic'] == 1 ? '*' : '&nbsp;' ).'</font></td>
              <td>&nbsp;</td>
              <td>
               <a href="style.php?action=editStyle&styleid='.$list['styleid'].'">Editieren</a> |
               <a href="style.php?action=deleStyle&styleid='.$list['styleid'].'">L&ouml;schen</a> |
               <a href="style.php?action=defaultStyle&styleid='.$list['styleid'].'">Als global makieren</a> |
               <a href="style.php?action=exStyle&styleid='.$list['styleid'].'">Exportieren</a>
              </td>
             </tr>';
         }
         $data['work'] .= '</table><br /><br />
          <font color="#FF0000">*</font> Globaler Style. Dieser Wird auf der Indexseite und in allen Foren genutzt, es sei denn
          f&uuml;r ein Forum<br />&nbsp;&nbsp; ist ein anderer Style makiert.<br /><br />
          <font color="#0000FF">*</font> &Ouml;ffendlicher Style. Diese Styles k&ouml;nnen vom User im Profil ausgew&auml;hlt werden.<br />
          &nbsp;&nbsp; Dann wird f&uuml;r ihn das gesammte Board mit diesem Style angezeigt.
         ';
     }
 }

 $data['javascript'] = '';
 echo Template( Get_Template( 'templates/admin.html' ) );
 tb_footer();
?>