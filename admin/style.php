<?php
/*
+--------------------------------------------------------------------------
|   Alex Guest Engine
|   ========================================
|   by Alex Höntschel
|   (c) 2002 AlexScriptEngine
|   http://www.alexscriptengine.de
|   ========================================
|   Web: http://www.alexscriptengine.de
|   Email: info@alexscriptengine.de
+---------------------------------------------------------------------------
|
|   > Beschreibung
|   > Style Einstellungen
|
|	> Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|	> bedingungen (read-me.html) für weitere Informationen.
|	>-----------------------------------------------------------------
|	> This script is NOT freeware! Please read the Copyright Notice
|	> (read-me_eng.html) for further information. 
|
|	> $Id: style.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/
define("FILE_NAME","settings.php");

include_once('adminfunc.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
$auth->checkEnginePerm("canaccessadmincent");

function buildStyleColorRow($title,$name,$value) {
    global $config,$bgcount,$sess;
    echo "<tr class=\"".switchBgColor()."\"><td>".$title."</td>\n";
    echo "<td nowrap><input size=\"40\" name=\"".$name."\" type=\"text\" value=\"".$value."\" onchange=\"changecolor(this.form.preview".$name.",this.value)\"> <input type=\"button\" id=\"preview".$name."\" value=\"          \" style=\"background-color:".$value."\" DISABLED></td>\n</tr>\n";
}

function show_all_template_folders() {
    global $style,$a_lang;
    $tpl_folder = './../templates';

    $folder_list = array();
    $handle = opendir($tpl_folder);
    while ($file = @readdir($handle)) {
        
        if (@is_dir($tpl_folder."/".$file) && $file != "." && $file != "..") {
            if(!strstr($file,"CVS")) {
                $folder_list[] = $file;
            } else {
                continue;
            }
        }
    }
    closedir($handle);
    if (empty($folder_list) || !is_array($folder_list)) {
        $message = $a_lang['no_template_folder_found'];
        return false;
    } else {
        $template_folder_opt = "<select class=\"input\" name=\"template_folder\">\n";
        for($i = 0; $i < sizeof($folder_list); $i++) {
            $template_folder_opt .= "<option value=\"".$folder_list[$i]."\"";
            if ($style['template_folder'] == $folder_list[$i]) {
                $template_folder_opt .= " selected=\"selected\"";
            }
            $template_folder_opt .= ">".$folder_list[$i]."</option>\n";
        }
        return $template_folder_opt;
    }
}

function updateCssFile($style_id) {
    global $db_sql,$config,$a_lang,$style_table,$set_table;
    $style = $db_sql->query_array("SELECT * FROM $style_table WHERE style_id='".intval($style_id)."'");
    $style = stripslashes_array($style);
    $css = str_replace("{body_font_face}",$style['body_font_face'],$style['css_file']);
    $css = str_replace("{body_font_color}",$style['body_font_color'],$css);
    $css = str_replace("{body_font_size}",$style['body_font_size'],$css);    
    $css = str_replace("{body_background_color}",$style['body_background_color'],$css);
    $css = str_replace("{row_top_border_color}",$style['row_top_border_color'],$css);
    $css = str_replace("{row_top_background_color}",$style['row_top_background_color'],$css);
    $css = str_replace("{row_top_font_color}",$style['row_top_font_color'],$css);
    $css = str_replace("{row_top_font_size}",$style['row_top_font_size'],$css);
    $css = str_replace("{breadcrumb_font_color}",$style['breadcrumb_font_color'],$css); 
    $css = str_replace("{breadcrumb_font_color_hover}",$style['breadcrumb_font_color_hover'],$css); 
    $css = str_replace("{breadcrumb_font_size}",$style['breadcrumb_font_size'],$css); 
    $css = str_replace("{content_border_color}",$style['content_border_color'],$css); 
    $css = str_replace("{content_background_color_odd}",$style['content_background_color_odd'],$css); 
    $css = str_replace("{content_background_color_even}",$style['content_background_color_even'],$css); 
    $css = str_replace("{content_font_color}",$style['content_font_color'],$css); 
    $css = str_replace("{content_font_color_hover}",$style['content_font_color_hover'],$css); 
    $css = str_replace("{content_font_size}",$style['content_font_size'],$css); 
    $css = str_replace("{content_highlight_background_color}",$style['content_highlight_background_color'],$css); 
    $css = str_replace("{content_highlight_font_color}",$style['content_highlight_font_color'],$css); 
    $css = str_replace("{content_highlight_font_color_hover}",$style['content_highlight_font_color_hover'],$css); 
    $css = str_replace("{row_bottom_border_color}",$style['row_bottom_border_color'],$css); 
    $css = str_replace("{row_bottom_background_color}",$style['row_bottom_background_color'],$css); 
    $css = str_replace("{row_bottom_font_color}",$style['row_bottom_font_color'],$css); 
    $css = str_replace("{row_bottom_font_size}",$style['row_bottom_font_size'],$css);       
    
    if($config['style_id'] == intval($style_id)) {
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_top_border_color'])."' WHERE find_word='row_top_border_color'");
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_top_background_color'])."' WHERE find_word='row_top_background_color'");
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['content_border_color'])."' WHERE find_word='content_border_color'");
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_bottom_border_color'])."' WHERE find_word='row_bottom_border_color'");
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_bottom_background_color'])."' WHERE find_word='row_bottom_background_color'");
        $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['body_background_color'])."' WHERE find_word='body_background_color'");    
    }
                                                          
    $fp = "";
    $fp = @fopen("./../templates/".$style['template_folder']."/style.css",'w');
    if (@fwrite($fp, $css)) {
        return $a_lang['settings_mes6'];
    } else {
        return $a_lang['settings_mes7'];
    }   		
}

$message = "";

if(isset ($action) && $action=='new_style') {
    $db_sql->sql_query("INSERT INTO $style_table (style_name,template_folder,body_font_face,body_font_color,body_font_size,body_background_color,row_top_border_color,row_top_background_color,row_top_font_color,row_top_font_size,breadcrumb_font_color,breadcrumb_font_size,breadcrumb_font_color_hover,content_border_color,content_background_color_odd,content_background_color_even,content_font_color,content_font_color_hover,content_font_size,content_highlight_background_color,content_highlight_font_color,content_highlight_font_color_hover,row_bottom_border_color,row_bottom_background_color,row_bottom_font_color,row_bottom_font_size,css_file) VALUES
                        ('".addslashes($style_name)."','".addslashes($template_folder)."','".addslashes($body_font_face)."','".$body_font_color."','".$body_font_size."','".$body_background_color."','".$row_top_border_color."','".$row_top_background_color."','".$row_top_font_color."','".$row_top_font_size."','".$breadcrumb_font_color."','".$breadcrumb_font_size."','".$breadcrumb_font_color_hover."','".$content_border_color."','".$content_background_color_odd."','".$content_background_color_even."','".$content_font_color."','".$content_font_color_hover."','".$content_font_size."','".$content_highlight_background_color."','".$content_highlight_font_color."','".$content_highlight_font_color_hover."','".$row_bottom_border_color."','".$row_bottom_background_color."','".$row_bottom_font_color."','".$row_bottom_font_size."','".addslashes($css_file)."')");
    $style_id = $db_sql->insert_id();                        
    $step = 'edit';
    //$add_message = updateCssFile($style_id);
    $message = $a_lang['style_successfully_created'].$add_message;
}

if(isset ($action) && $action=='update_style') {
    $db_sql->sql_query("UPDATE $style_table SET
                        style_name='".addslashes($style_name)."', 
                        template_folder='".$template_folder."', 
                        body_font_face='".$body_font_face."', 
                        body_font_color='".$body_font_color."', 
                        body_font_size='".$body_font_size."', 
                        body_background_color='".$body_background_color."', 
                        row_top_border_color='".$row_top_border_color."', 
                        row_top_background_color='".$row_top_background_color."', 
                        row_top_font_color='".$row_top_font_color."', 
                        row_top_font_size='".$row_top_font_size."', 
                        breadcrumb_font_color='".$breadcrumb_font_color."', 
                        breadcrumb_font_size='".$breadcrumb_font_size."', 
                        breadcrumb_font_color_hover='".$breadcrumb_font_color_hover."', 
                        content_border_color='".$content_border_color."', 
                        content_background_color_odd='".$content_background_color_odd."', 
                        content_background_color_even='".$content_background_color_even."', 
                        content_font_color='".$content_font_color."', 
                        content_font_color_hover='".$content_font_color_hover."', 
                        content_font_size='".$content_font_size."', 
                        content_highlight_background_color='".$content_highlight_background_color."', 
                        content_highlight_font_color='".$content_highlight_font_color."', 
                        content_highlight_font_color_hover='".$content_highlight_font_color_hover."', 
                        row_bottom_border_color='".$row_bottom_border_color."', 
                        row_bottom_background_color='".$row_bottom_background_color."', 
                        row_bottom_font_color='".$row_bottom_font_color."', 
                        row_bottom_font_size='".$row_bottom_font_size."', 
                        css_file='".addslashes($css_file)."'                        
                        WHERE style_id='".intval($style_id)."'");
    $step = 'new_style';
    $add_message = updateCssFile($style_id);
    $message = $a_lang['style_changed'].$add_message;                        
}

if(isset ($action) && $action=='set_style') {
    $style = $db_sql->query_array("SELECT * FROM $style_table WHERE style_id='".intval($style_id)."'");
    $style = stripslashes_array($style);
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['template_folder'])."' WHERE find_word='template_folder'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".$style_id."' WHERE find_word='style_id'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_top_border_color'])."' WHERE find_word='row_top_border_color'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_top_background_color'])."' WHERE find_word='row_top_background_color'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['content_border_color'])."' WHERE find_word='content_border_color'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_bottom_border_color'])."' WHERE find_word='row_bottom_border_color'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['row_bottom_background_color'])."' WHERE find_word='row_bottom_background_color'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".addslashes($style['body_background_color'])."' WHERE find_word='body_background_color'");
    $db_sql->sql_query("UPDATE $set_table SET replace_value='".$config['engine_mainurl']."/templates/".$config['template_folder']."/images' WHERE find_word='grafurl'");
            
    $add_message = updateCssFile($style_id);            
    $step = 'edit';
    $message = $a_lang['style_changed2'].$add_message;  
}

if(isset ($action) && $action=='delete') {
    if($config['style_id'] != intval($style_id)) {
        $db_sql->sql_query("DELETE FROM $style_table WHERE style_id='".intval($style_id)."'");
        $message = $a_lang['style_set_deleted'];        
    } else {
        $message = $a_lang['style_del_not_possible'];
    }
    $step = 'edit';
}

buildAdminHeader();

if ($message != "") buildMessageRow($message);

if(!isset ($step) && $change == '') {
  echo " <p><b>Es wurde kein Einstelltyp gewählt. Bitte wähle links aus der Navigation die gewünschte Option aus. $step</b></p>";
} else {
    if($step == 'edit') {
        buildHeaderRow($a_lang['edit_style_sets'],"col_setting.gif");
        $result = $db_sql->sql_query("SELECT style_id, style_name FROM $style_table");
        buildTableHeader($a_lang['available_styles']);
        if($db_sql->num_rows($result) >= 1) {
            $total = $db_sql->num_rows($result);
        
            while($style = $db_sql->fetch_array($result)) {
                $style = stripslashes_array($style);
                unset($current_icon);
                if($total == 1 || $style['style_id'] == $config['style_id']) {
                    $delete = "<img src=\"images/no_delete.gif\" alt=\"".$a_lang['style_delete']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang['style_delete'];
                } else {
                    $delete = "<a class=\"menu\" href=\"".$sess->adminUrl("style.php?style_id=".$style['style_id']."&step=delete_style")."\"><img src=\"images/delete.gif\" alt=\"".$a_lang['style_delete']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang['style_delete']."</a>";
                }
                
                if($config['style_id'] == $style['style_id']) $current_icon = "<img src=\"images/infopage.gif\" alt=\"".$a_lang['style_set_in_use']."\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">";
                buildStandardRow("(ID: <b>".$style['style_id']."</b>) <b>".$style['style_name']."</b> ".$current_icon, "<a class=\"menu\" href=\"".$sess->adminUrl("style.php?style_id=".$style['style_id']."&step=new_style")."\"><img src=\"images/edit.gif\" alt=\"".$a_lang['style_edit']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang['style_edit']."</a>&nbsp;&nbsp;&nbsp;&nbsp;".$delete."&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"menu\" href=\"".$sess->adminUrl("style.php?style_id=".$style['style_id']."&action=set_style")."\"><img src=\"images/lock.gif\" alt=\"".$a_lang['use_style_set']."\" width\"16\" height=\"16\" border=\"0\" align=\"absmiddle\">".$a_lang['use_style_set']."</a>");
            }
        } else {
            buildLightColumn($a_lang['no_style_set_available'],1,1,2);
        }
        buildTableFooter();
        buildExternalItems($a_lang['add_style_set'],"style.php?step=new_style","add.gif");
    }
    
    if($step == 'new_style') {
        if($style_id) {
            $style = $db_sql->query_array("SELECT * FROM $style_table WHERE style_id='".intval($style_id)."'");
            $style = stripslashes_array($style);
            $action = "update_style";
        } else {
            $action = "new_style";
        }
        buildHeaderRow($a_lang['edit_style_sets'],"col_setting.gif",1);
        buildInfo($a_lang['info11'][0],$a_lang['info11'][1]);
        buildFormHeader("style.php", "post", $action);
        if($style_id) buildHiddenField("style_id", $style_id);
        buildTableHeader($a_lang['body_data']);
        buildInputRow($a_lang['style_set_name'], "style_name", $style['style_name']);
        buildStandardRow($a_lang['style_templat_folder_name'], show_all_template_folders());
        buildInputRow($a_lang['body_font_face'], "body_font_face", $style['body_font_face']);
        buildStyleColorRow($a_lang['body_font_color'],"body_font_color",$style['body_font_color']);
        buildInputRow($a_lang['body_font_size'], "body_font_size", $style['body_font_size']);
        buildStyleColorRow($a_lang['background_color'],"body_background_color",$style['body_background_color']);
        buildTableSeparator($a_lang['design_row_top']);
        buildStyleColorRow($a_lang['border_color'],"row_top_border_color",$style['row_top_border_color']);
        buildStyleColorRow($a_lang['background_color'],"row_top_background_color",$style['row_top_background_color']);
        buildStyleColorRow($a_lang['body_font_color'],"row_top_font_color",$style['row_top_font_color']);
        buildInputRow($a_lang['body_font_size'], "row_top_font_size", $style['row_top_font_size']);
        buildTableSeparator($a_lang['breadcrumb_row']);
        buildStyleColorRow($a_lang['body_font_color'],"breadcrumb_font_color",$style['breadcrumb_font_color']);
        buildStyleColorRow($a_lang['font_color_mouseover'],"breadcrumb_font_color_hover",$style['breadcrumb_font_color_hover']);
        buildInputRow($a_lang['body_font_size'], "breadcrumb_font_size", $style['breadcrumb_font_size']);
        buildTableSeparator($a_lang['design_main_area']);
        buildStyleColorRow($a_lang['border_color'],"content_border_color",$style['content_border_color']);
        buildStyleColorRow($a_lang['alternating_bg_color1'],"content_background_color_odd",$style['content_background_color_odd']);
        buildStyleColorRow($a_lang['alternating_bg_color2'],"content_background_color_even",$style['content_background_color_even']);
        buildStyleColorRow($a_lang['body_font_color'],"content_font_color",$style['content_font_color']);
        buildStyleColorRow($a_lang['font_color_mouseover'],"content_font_color_hover",$style['content_font_color_hover']);
        buildInputRow($a_lang['body_font_size'], "content_font_size", $style['content_font_size']);    
        buildStyleColorRow($a_lang['background_highlighted_area'],"content_highlight_background_color",$style['content_highlight_background_color']);
        buildStyleColorRow($a_lang['font_color_highlighted_area'],"content_highlight_font_color",$style['content_highlight_font_color']);
        buildStyleColorRow($a_lang['font_color_hover_highlighted_area'],"content_highlight_font_color_hover",$style['content_highlight_font_color_hover']);
        buildTableSeparator($a_lang['design_row_bottom']);
        buildStyleColorRow($a_lang['border_color'],"row_bottom_border_color",$style['row_bottom_border_color']);
        buildStyleColorRow($a_lang['background_color'],"row_bottom_background_color",$style['row_bottom_background_color']);
        buildStyleColorRow($a_lang['body_font_color'],"row_bottom_font_color",$style['row_bottom_font_color']);
        buildInputRow($a_lang['body_font_size'], "row_bottom_font_size", $style['row_bottom_font_size']);        
        buildTableSeparator("CSS-Datei direkt bearbeiten");
        echo "
        <tr>
            <td class=\"firstcolumn\" valign=\"top\">".$a_lang['css_description']."</td>
            <td class=\"othercolumn\" nowrap>
                <textarea class=\"input\" cols=\"75\" rows=\"25\" name=\"css_file\" wrap=\"off\">".$style['css_file']."</textarea>
            </td>
        </tr> ";        
        buildFormFooter($a_lang['save_style_set'], $a_lang['reset_style_set']);
        
    }
    
    if($step == 'delete_style') {
        $result = $db_sql->sql_query("SELECT * FROM $style_table WHERE style_id='".intval($style_id)."'");
        $del = $db_sql->fetch_array($result);
        buildHeaderRow($a_lang['afunc_13'],"delart.gif");
        buildFormHeader("style.php", "post", "delete");
        buildHiddenField("style_id",intval($style_id));
        buildTableHeader($a_lang['delete_style_set'].": <u>$del[style_name]</u>");
        buildDarkColumn($a_lang['confirm_delete_style_set'],1,1,2);
        buildFormFooter($a_lang['afunc_61'], "", "", $a_lang['afunc_62']);
    }    
}

buildAdminFooter();
?>
