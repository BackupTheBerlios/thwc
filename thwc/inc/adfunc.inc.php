<?php
/* $Id: adfunc.inc.php,v 1.1 2003/06/12 13:59:24 master_mario Exp $ */
 function tb_footer()
 {
     print '
        <tr>
         <td  id="blank"></td>
         <td  id="blank" style="text-align:right">
          <font size="1">--&gt; ThWClone (c) 2003 by Mario Pischel based up&acute;n ThWboard (c) 2002 by Paul Baecher
          &amp; Felix Gonschorek&nbsp;&nbsp;&nbsp;</font>&nbsp;&nbsp;&nbsp;
         </td>
        </tr>
       </table>
      </body>
      </html>';
 }
 function listbox( $name, $key, $value, $table, $select, $addoption='', $addkey='' )
 {
     $back = '<select name="'.$name.'" id="tab">';

     if( $addoption != '' )
     {
         $back .= ' <option value="'.$addkey.'"'.($addkey == $select ? " selected" : "").'>'.$addoption.'</option>';
     }

     $result=db_query("SELECT $key, $value FROM $table");
     while( list( $key, $value ) = mysql_fetch_row( $result ) )
     {
         $back .= ' <option value="'.$key.'"'.($key == $select ? " selected" : "").'>'.$value.'</option>';
     }
     return $back.'</select>';
 }
 function encode($string)
 {
    $string = str_replace('&', '&amp;', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    return $string;
 }
 function decode($string)
 {
    $string = str_replace('&amp;', '&', $string);
    $string = str_replace('&lt;', '<', $string);
    $string = str_replace('&gt;', '>', $string);
    return $string;
 }
 function encodeX($string)
 {
    $string = str_replace('&prime;', "'", $string);
    $string = str_replace('&', '&amp;', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    return $string;
 }
?>