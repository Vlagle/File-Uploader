<?php
/*
+--------------------------------------------------------------------------
|   Alex Download Engine
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
|   > Funktionsbibliothek für die DL-Engine
|
|   > Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|   > bedingungen (read-me.html) für weitere Informationen.
|   >-----------------------------------------------------------------
|   > This script is NOT freeware! Please read the Copyright Notice
|   > (read-me_eng.html) for further information. 
|
|   > $Id: function.dl.php 29 2005-10-30 10:09:10Z alex $
|
+--------------------------------------------------------------------------
*/

function postCss($comno) {
    global $config;
    if(($comno/2) != floor($comno/2)) {
        $css = 'list_dark';
    } else {
        $css = 'list_light';
    }
    return $css;
}

function buildThumbnail($dl,$resize_img) {
    global $config;
    if ($dl['thumb'] != "0" && $dl['thumb'] != "") {
        if($resize_img == false) {
               $size = @getimagesize("./thumbnail/".$dl['thumb']);
               if($size[0] != "") $thumb_width = "width=\"".$size[0]."\"";
               if($size[1] != "") $thumb_height = "height=\"".$size[1]."\"";
            $thumb_url = $config['thumburl']."/".$dl['thumb'];
        } else {
            $thumb_file = "./thumbnail/thumb_".$dl['thumb'];
                            
            if(file_exists($thumb_file)) {
                $orig_data = @getimagesize($thumb_file);
                if($orig_data[0] != "") $thumb_width = "width=\"".$orig_data[0]."\"";
                if($orig_data[1] != "") $thumb_height = "height=\"".$orig_data[1]."\"";
                $thumb_url = $config['thumburl']."/thumb_".$dl['thumb'];
            } else {    
                $orig_data = @getimagesize($add."thumbnail/".$dl['thumb']);             
                if(($orig_data[0] > $config['image_width']) || ($orig_data[1] > $config['image_height'])) {     
                    $thumb = calcThumbnail($dl,$add="./");
                    if(createThumbnail("./thumbnail/".$dl['thumb'],$thumb,$thumb_file,chkgd2())) {
                        $size = @getimagesize($thumb_file);
                        if($size[0] != "") $thumb_width = "width=\"".$size[0]."\"";
                        if($size[1] != "") $thumb_height = "height=\"".$size[1]."\"";                   
                        $thumb_url = $config['thumburl']."/thumb_".$dl['thumb'];
                    } else {
                        if($orig_data[0] != "") $thumb_width = "width=\"".$orig_data[0]."\"";
                        if($orig_data[1] != "") $thumb_height = "height=\"".$orig_data[1]."\"";                     
                        $thumb_url = $config['thumburl']."/".$dl['thumb'];
                    }                       
                } else {
                    $size = @getimagesize("./thumbnail/".$dl['thumb']);
                    if($size[0] != "") $thumb_width = "width=\"".$size[0]."\"";
                    if($size[1] != "") $thumb_height = "height=\"".$size[1]."\"";
                    $thumb_url = $config['thumburl']."/".$dl['thumb'];                      
                }       
            }
        }
        $pic = "<img src=\"".$thumb_url."\" ".$thumb_width." ".$thumb_height." border=\"0\" align=\"top\" />";          
    } else {
        $pic = "";
    }
    return $pic;
}

function displayDetailedFiles($subcat,$start,$orderby) {
    global $db_sql, $config, $auth, $sess, $lang, $_ENGINE,$dl_table,$mirror_table,$bbcode,$total_dls;
    
    if($config['active_image_resizer']) {
        include_once($_ENGINE['eng_dir']."admin/enginelib/function.img.php");
        if(chkgd2() >= 2) {
            $resize_img = true;
        } else {
            $resize_img = false;
        }
    }
    
    $detailed_files_loop = array();
    
    $result2 = $db_sql->sql_query("SELECT $dl_table.*, count(mirror_id) AS mirror_no FROM $dl_table 
                                    LEFT JOIN $mirror_table ON $dl_table.dlid = $mirror_table.dlid
                                    WHERE $dl_table.catid='$subcat' AND $dl_table.status!='3' GROUP BY $dl_table.dlid ORDER BY $orderby LIMIT ".$start.",$config[dlperpage]");
    while($dl = $db_sql->fetch_array($result2)) {
        $dl = stripslashes_array($dl);
        unset($points);
        unset($kom_i);
        unset($email_author);
        unset($homep);
        unset($update_mark);
        if ($dl['dlpoints'] != 0) {
            $dl_divp = $dl['dlpoints'] / $dl['dlvotes'];
            $points = round($dl_divp,2);
        }
        
        if($config['active_image_resizer']) {
            $pic = buildThumbnail($dl,$resize_img);
        } else {
            if ($dl['thumb'] != "0" && $dl['thumb'] != "") {
                $size = @getimagesize("./thumbnail/".$dl['thumb']);
                if($size[0] != "") $thumb_width = "width=\"".$size[0]."\"";
                if($size[1] != "") $thumb_height = "height=\"".$size[1]."\"";
                $pic = "<img src=\"$config[thumburl]/$dl[thumb]\" $thumb_width $thumb_height border=\"0\" align=\"left\" />";
            } else {
                $pic = "";
            }       
        }

        if ($dl['onlyreg'] == 0 || $auth->user['canaccessregisteredfiles'] == "1") {
            $headlink = "<a class=\"list_headline\" href=\"".$sess->url("comment.php?dlid=".$dl['dlid'])."\">".trim($dl['dltitle'])."</a>";
            $headlink2 = "<a class=\"file_new_link\" href=\"".$sess->url("comment.php?dlid=".$dl['dlid'])."\">".trim($dl['dltitle'])."</a>";            
            
            if($config['front_download']) $piclink = "<a class=\"cat\" href=\"".$sess->url("redirect.php?dlid=".$dl['dlid'])."\"><img src=\"".$_ENGINE['languageurl']."/img_download.gif\" width=\"105\" height=\"22\" border=\"0\" align=\"middle\" /></a>";
            if($dl['mirror_no'] >= 1) $mirrorlink = "&raquo; <a href=\"".$sess->url("comment.php?dlid=".$dl['dlid'])."#mirror\">".$lang['index_mirror_links']."</a> (".$dl['mirror_no'].")<br />";
        } else {
            $headlink = trim($dl['dltitle'])." *<br><font size=\"1\">".$lang['index_registered_members_only']."</font>";
            $piclink = "&nbsp;";
        }       

        if ($dl['dlpoints'] == 0) {
            $rate = $lang['index_currently_no_rating'];
        } else {
            $rate = sprintf($lang['index_rate_on_total_votes'],$points,$dl['dlvotes']);
        }
        
        $file_author = trim($dl['dlauthor']);
        
        if ($dl['hplink'] != "" && $dl['hplink'] != "0") {
            $homep = " <a href=\"".trim(htmlspecialchars($dl['hplink']))."\"><img src=\"$config[grafurl]/img_homepage.gif\" alt=\"$lang[php_goto_hp] $file_author\" width=\"16\" height=\"16\" border=\"0\" align=\"middle\" /></a> ";
        } else {
            $homep = "";
        }

        if ($dl['authormail'] != "") $email_author = "<a href=\"".$sess->url("misc.php?action=formmailer&dlid=".$dl['dlid'])."\"><img src=\"$config[grafurl]/img_email.gif\" alt=\"$lang[php_sendmail] $file_author\" width=\"16\" height=\"16\" border=\"0\" align=\"middle\" /></a>";
        
        if ($dl['status'] == 1 ) {
            $deadlink = " <a class=\"infile\" href=\"".$sess->url("deadlink.php?subcat=".$dl['catid']."&dlid=".$dl['dlid'])."\">".$lang['index_report_deadlink']."</a>";
        } else {
            $deadlink = $lang['index_link_will_be_checked_soon'];
        }
        
        if($config['updatemark'] && $dl['update_date']) $update_mark = buildUpdate($dl['update_date']);

        if ($config['newmark']) $graph = newgraph($dl['dl_date']);
        
        if ($config['top_list']) $cool = CoolDL($dl['dlhits'],$total_dls);
        $stars = Stars($points);
        $loadtime = LoadTime($dl['dlsize']);
        
        $detailed_files_loop[] = array('headlink' => $headlink,
                                        'index_comment' => $lang['index_comment'],
                                        'index_rate_file' => $lang['index_rate_file'],
                                        'index_mail_a_friend' => $lang['index_mail_a_friend'],
                                        'index_options' => $lang['index_options'],
                                        'gfx_graph' => $graph,
                                        'gfx_cool' => $cool,
                                        'gfx_stars' => $stars,
                                        'gfx_update' => $update_mark,
                                        'file_description' => $bbcode->rebuildText($dl['dldesc']),
                                        'file_total_downloads' => sprintf($lang['index_total_downloads'],$dl['dlhits']),
                                        'file_total_rating' => $rate,
                                        'file_total_time_to_download' => LoadTime($dl['dlsize']),
                                        'options_mirror_link' => $mirrorlink,
                                        'options_deadlink' => $deadlink,
                                        'options_comment_count' => $dl['comment_count'],
                                        'options_comment_url' => $sess->url('comment.php?dlid='.$dl['dlid']).'#comm',
                                        'options_rate_url' => $sess->url('comment.php?dlid='.$dl['dlid']).'#rate',
                                        'options_recommend_url' => $sess->url('recommend.php?dlid='.$dl['dlid']),
                                        'file_pic' => $pic,
                                        'direct_download_link' => $piclink,
                                        'author_information' => sprintf($lang['index_author_of_file'],trim($dl['dlauthor']),$email_author,$homep,aseDate($config['shortdate'],$dl['dl_date'])));
    }  
    return $detailed_files_loop;
}

function displayListedFiles($subcat,$start,$orderby) {
    global $db_sql, $config, $auth, $sess, $lang, $_ENGINE,$dl_table,$mirror_table,$bbcode,$total_dls;
    $result2 = $db_sql->sql_query("SELECT $dl_table.*, count(mirror_id) AS mirror_no FROM $dl_table 
                                    LEFT JOIN $mirror_table ON $dl_table.dlid = $mirror_table.dlid
                                    WHERE $dl_table.catid='$subcat' AND $dl_table.status!='3' GROUP BY $dl_table.dlid ORDER BY $orderby LIMIT ".$start.",$config[dlperpage]");
    $no = 0;
    $listed_files_loop = array();
    while($dl = $db_sql->fetch_array($result2)) {
        $dl = stripslashes_array($dl);
        unset($points);
        unset($added);
        unset($loadtime);
        unset($description);
        unset($graph);
        unset($cool);
        unset($stars);
        $rowcolor = postCss($no);
        if ($dl['dlpoints'] != 0) {
            $dl_divp = $dl['dlpoints'] / $dl['dlvotes'];
            $points = round($dl_divp,2);
        }

        //if ($dl['onlyreg'] == 0 || $auth->user['canaccessregisteredfiles'] == "1") {
            $headlink = "<a href=\"".$sess->url("comment.php?dlid=".$dl['dlid'])."\"><b>".trim($dl['dltitle'])."</b></a>";
        /*} else {
            $headlink = "<b>".trim($dl['dltitle'])."</b> *<font size=\"1\">".$lang['index_registered_members_only']."</font>";
        }*/     

        if ($dl['dlpoints'] == 0) {
            $rate = $lang['index_currently_no_rating'];
        } else {
            $rate = "<b>".$points."</b> (".$dl['dlvotes']." ".$lang['index_votes'].")";
        }

        if ($config['newmark']) $graph = newgraph($dl['dl_date']);
        
        if ($config['top_list']) $cool = CoolDL($dl['dlhits'],$total_dls);
        $stars = Stars($points);
        $file_size = buildFileSize($dl['dlsize']);
        
        if(strlen($dl['dldesc'])>160) $dl['dldesc'] = substr($dl['dldesc'],0,157)."...";
        $description = $bbcode->rebuildText($dl['dldesc']);  
        $no++;                      
        
        $listed_files_loop[] = array('headlink' => $headlink,
                                    'description' => $description,
                                    'file_date' => aseDate($config['longdate'],$dl['dl_date'],1),
                                    'rowcolor' => $rowcolor,
                                    'file_size' => $file_size,
                                    'file_rating' => $rate,
                                    'file_hits' => $dl['dlhits'],
                                    'file_stars' => $stars, 
                                    'file_graph' => $graph,
                                    'file_cool' => $cool);
    }  
    return $listed_files_loop;
}

function displayCatBits($subcat=0,$count_rows=0) {
    global $db_sql, $lang,$cat_table,$dl_childtable,$cat_table,$sess,$parse_categories,$tpl;
    $result = $db_sql->sql_query("SELECT $cat_table.*, $dl_childtable.childlist FROM $cat_table
                                    LEFT JOIN $dl_childtable ON $cat_table.catid = $dl_childtable.catid
                                    WHERE subcat='".$subcat."' ORDER BY catorder ASC");
                                    
    if($count_rows) {                                    
        $row = $db_sql->num_rows($result);
        if ($row != 0) {
            $parse_categories = true;
        }
    }
    
    // entspricht $config['no_of_cat']
    $cat_rows = 2;
    
    $i = 1;
    $anz = $cat_rows;
    
    $b = 0;
    $width_cat = round(100/$anz,0);
    $tpl->register(array('width'=>$width_cat,
                        'colspan' => $cat_rows));
    
    $row = $db_sql->num_rows($result);
                                            
    $count_cat = 1;
    $category_loop = array();
    while($cat = $db_sql->fetch_array($result)) {
        $cat = stripslashes_array($cat);
        unset($new_count);
        $new_count = $cat['download_count'];
        if($cat['childlist']) {
            $result2 = $db_sql->sql_query("SELECT $cat_table.download_count FROM $cat_table WHERE catid IN (".modifyCatChildList($cat['childlist']).")");
            while($counter = $db_sql->fetch_array($result2)) $new_count = $new_count + intval($counter['download_count']);
        }

        $dl_counter = $new_count;
        $cat_counter = $db_sql->num_rows($db_sql->sql_query("SELECT catid FROM $cat_table WHERE subcat='$cat[catid]'"));

        $b++;
        $table_end = "</td>\n";
        if($b == $anz && $i != intval($row)) {
            $table_end .= "</tr>\n<tr>\n";
            $b = 0;
        }        
        
        $category_loop[] = array('category_headline' => $cat['titel'],
                                'category_description' => $cat['cat_desc'],
                                'category_url' => $sess->url('index.php?subcat='.$cat['catid']),
                                'number_of_subcategories' => $cat_counter,
                                'number_of_files' => $dl_counter,
                                'table_start' => "<td width=\"$width_cat%\" valign=\"top\">\n",
                                'table_end' => $table_end);
        
        unset($dl_counter);
        unset($cat_counter);
        $count_cat++;
        $i++;
    }
    $catbits = array($cat_mainbit_l,$cat_mainbit_r);
    if($count_rows) $catbits[] = $subcat_head;
    return $category_loop;
}

function newgraph($date) {
    global $lang, $config;
    $today = time();
    
    $latest = $config['newmark']*24*60*60;
    $intervall = $latest / 3;
    
    if ($date < $today-($intervall*2) && $date > $today-$latest) {
        $newgr = " <img src=\"$config[grafurl]/img_new3.gif\" alt=\"\" width=\"60\" height=\"14\" border=\"0\" align=\"middle\" /> ";
    } elseif ($date < $today-$intervall && $date > $today-($intervall*2)) {
        $newgr = " <img src=\"$config[grafurl]/img_new2.gif\" alt=\"\" width=\"60\" height=\"14\" border=\"0\" align=\"middle\" /> ";
    } elseif ($date > $today-$intervall || $date == $today) {
        $newgr = " <img src=\"$config[grafurl]/img_new.gif\" alt=\"\" width=\"60\" height=\"14\" border=\"0\" align=\"middle\" /> ";
    } else {
        $newgr = " ";
    }    
    return $newgr;
}

function GetFile($dlid) {
    global $dl_table,$db_sql,$cat_table;
    $sql = $db_sql->query_array("SELECT $dl_table.*, $cat_table.titel FROM $dl_table 
                                LEFT JOIN $cat_table ON $dl_table.catid = $cat_table.catid
                                WHERE $dl_table.dlid='$dlid'");
    return stripslashes_array($sql);
}
         
function VoteFile($vote,$dlid,$userid) {
    global $dl_table,$dl_iptable,$db_sql,$config,$_COOKIE,$lang,$user;
    $update_vote = FALSE;
    list($dlid,$dlvotes,$dlpoints) = $db_sql->sql_fetch_row("SELECT dlid,dlvotes,dlpoints FROM $dl_table WHERE dlid='$dlid'");       
         
    switch ($config['kindoflock']) {
        case 0: // Cookie
            $cookie_name = "vote_dlid_".$dlid;
            if($_COOKIE["$cookie_name"] != $dlid) {
                $update_vote = TRUE;
                setcookie("$cookie_name", $dlid, time()+($config['time_to_lock']*60),"/");
            }
            break;
        case 1: // IP
            $block_time = time() - ($config['time_to_lock']*60);
            $db_sql->sql_query("DELETE FROM $dl_iptable WHERE vote_time < '$block_time'");
            $user_ip = getenv("REMOTE_ADDR");
            $voted_file = $db_sql->query_array("SELECT * from $dl_iptable WHERE user_ip='$user_ip' AND dl_id='$dlid'");
            if($voted_file == "") {
                $update_vote = TRUE;
                $db_sql->sql_query("INSERT INTO $dl_iptable (dl_id,user_ip,vote_time) VALUES ('$dlid','$user_ip','".time()."')");
            }
            break;
    case 2: // Cookie und IP
        $cookie_name = "vote_dlid_".$dlid;
        if($_COOKIE["$cookie_name"] != $dlid) {
            $update_vote = TRUE;
            setcookie("$cookie_name", $dlid, time()+($config['time_to_lock']*60),"/");
        }        
        $block_time = time() - ($config['time_to_lock']*60);
        $db_sql->sql_query("DELETE FROM $dl_iptable WHERE vote_time < '$block_time'");
        $user_ip = getenv("REMOTE_ADDR");
        $voted_file = $db_sql->query_array("SELECT * FROM $dl_iptable WHERE user_ip='$user_ip' AND dl_id='$dlid'");
        if($voted_file == "") {
            $update_vote = TRUE;
            $db_sql->sql_query("INSERT INTO $dl_iptable (dl_id,user_ip,vote_time) VALUES ('$dlid','$user_ip','".time()."')");
        }        
        break;          
    }
    
    if(!$config['active_lock']) $update_vote = TRUE;

    if($update_vote) {
        if($config['user_rate_factor'] == 0) $config['user_rate_factor'] = 1;
        if($user['groupid'] != 8) {
            $value = $vote*$config['user_rate_factor'];
            $uservotes = $config['user_rate_factor'];
        } else {
            $value = $vote;
            $uservotes = 1;
        }
        
        $dlpoi = $dlpoints + $value;
        $dlvot = $uservotes + $dlvotes;
        $db_sql->sql_query("UPDATE $dl_table SET dlpoints='$dlpoi', dlvotes='$dlvot' WHERE dlid='$dlid'");
        return $lang['rec_error8'];
    } else {
        return $lang['rec_error41'];
    }
}   

function convertOrderBy($table, $order) {
    if($order == "nameA")       return "$table.username ASC"; 
    if($order == "pnameA")      return "$table.dltitle ASC"; 
    if($order == "rankA")       return "$table.groupid ASC";
    if($order == "sinceA")      return "$table.regdate ASC";
    if($order == "dateA")       return "$table.dl_date ASC";
    if($order == "nameD")       return "$table.username DESC"; 
    if($order == "pnameD")      return "$table.dltitle DESC"; 
    if($order == "rankD")       return "$table.groupid DESC";
    if($order == "sinceD")      return "$table.regdate DESC";
    if($order == "dateD")       return "$table.dl_date DESC";
}
         
function convertorderbyin($orderby) {
    global $dl_table;
    if ($orderby == "titleA")   $orderby = "$dl_table.dltitle ASC"; 
    if ($orderby == "dateA")    $orderby = "$dl_table.dl_date ASC";
    if ($orderby == "hitsA")    $orderby = "$dl_table.dlhits ASC";
    if ($orderby == "votesA")   $orderby = "$dl_table.dlvotes ASC";
    if ($orderby == "titleD")   $orderby = "$dl_table.dltitle DESC"; 
    if ($orderby == "dateD")    $orderby = "$dl_table.dl_date DESC";
    if ($orderby == "hitsD")    $orderby = "$dl_table.dlhits DESC";
    if ($orderby == "votesD")   $orderby = "$dl_table.dlvotes DESC";
    return $orderby;
}
         
function postcolor($comno) {
    global $config;
    if(($comno/2) != floor($comno/2)) {
        $background = $config['postcol1'];
    } else {
        $background = $config['postcol2'];
    }
    return $background;
}

function CatHead($catid,$no) {
    global $cat_table,$config,$db_sql;
    list ($titel) = $db_sql->sql_fetch_row("SELECT titel FROM $cat_table WHERE catid='$catid'");
    eval ("\$including = \"".gettemplate("newdl_cathead")."\";");   
}
         
function CoolDL($t_hits,$total=0) {
    global $dl_table, $config,$db_sql;
    $hits = 0;
    
    $top = $total / 100 * $config['top_list_q'];
    
    if ($t_hits < $top) {
        $cool = "";
    } else {
        $cool = " <img src=\"$config[grafurl]/img_popular.gif\" alt=\"\" width=\"60\" height=\"14\" border=\"0\" align=\"middle\" /> ";
    }
    return $cool;
}

function buildUpdate($up_date) {
    global $config;
    $new_d = ($config['updatemark']*24*60*60)+$up_date;
    if($new_d >= time()) {
        return " <img src=\"$config[grafurl]/img_updated.gif\" alt=\"\" width=\"60\" height=\"14\" border=\"0\" align=\"middle\" /> ";
    } else {
        return false;
    }
}
         
function Stars($points) {
    global $config,$lang;
    if ($points > "4") {
        $stars = "";
    } elseif ($points > "3") {
        //$stars = " <img src=\"$config[grafurl]/pop.gif\" width=11 height=10 border=0 alt=\"$lang[file_bit_rate] $points\">";
        $stars = " <img src=\"$config[grafurl]/stars1.gif\" width=\"35\" height=\"14\" border=\"0\" align=\"middle\" alt=\"".sprintf($lang['misc_top10_rating_rate'],$points)."\" />";
    } elseif ($points > "2") {
        //$stars = " <img src=\"$config[grafurl]/pop.gif\" width=11 height=10 border=0 alt=\"$lang[file_bit_rate] $points\">";
        //$stars .= "<img src=\"$config[grafurl]/pop.gif\" width=11 height=10 border=0 alt=\"$lang[file_bit_rate] $points\">";
        $stars = " <img src=\"$config[grafurl]/stars2.gif\" width=\"35\" height=\"14\" border=\"0\" align=\"middle\" alt=\"".sprintf($lang['misc_top10_rating_rate'],$points)."\" />";        
    } elseif ($points >= "1") {
        //$stars = " <img src=\"$config[grafurl]/pop.gif\" width=11 height=10 border=0 alt=\"$lang[file_bit_rate] $points\">";
        //$stars .= "<img src=\"$config[grafurl]/pop.gif\" width=11 height=10 border=0 alt=\"$lang[file_bit_rate] $points\">";
        //$stars .= "<img src=\"$config[grafurl]/pop.gif\" width=11 height=10 border=0 alt=\"$lang[file_bit_rate] $points\">";
        $stars = " <img src=\"$config[grafurl]/stars3.gif\" width=\"35\" height=\"14\" border=\"0\" align=\"middle\" alt=\"".sprintf($lang['misc_top10_rating_rate'],$points)."\" />";        
    } else {
        $stars = " ";
    }
    return $stars;
}

function buildFileSize($fsize) {
    global $config,$lang;
    //$fsize = intval($fsize);
    $length = strlen($fsize);
    if($length <= 3) {
        $fsize = number_format($fsize,2,",",".");
        return $fsize." Bytes";
    } elseif($length >= 4 && $length <= 6) {
        $fsize = number_format($fsize/1024,2,",",".");
        return $fsize." kB";
    } elseif($length >= 7 && $length <= 9) {    
        $fsize = number_format($fsize/1048576,2,",",".");
        return $fsize." MB";
    } else {
        $fsize = number_format($fsize/1073741824,2,",",".");
        return $fsize." GB";
    }   
}
         
function InsertPost($text) {
    $text = str_replace("'","&acute;", $text);
    $text = str_replace("\"","&quot;", $text);      
    return $text;        
}       

function sBBcode($text,$imgcode = 1,$urlparsing = 1,$smilie = 1) {
    global $sBBcode,$config;
    if($imgcode == 1) $sBBcode->imgcode = 1;
    if($urlparsing == 1) $text = $sBBcode->url_parse($text);

    $text= stripslashes($text);
    $text = htmlspecialchars($text);
    $text = $sBBcode->parsen($text);
    $text = nl2br($text);
    $text = str_replace(":-)","<image src=\"$config[smilieurl]/smile.gif\" />",$text);
    $text = str_replace(";-)","<image src=\"$config[smilieurl]/wink.gif\" />",$text);
    $text = str_replace(":O","<image src=\"$config[smilieurl]/wow.gif\" />",$text);
    $text = str_replace(";-(","<image src=\"$config[smilieurl]/sly.gif\" />",$text);
    $text = str_replace(":D","<image src=\"$config[smilieurl]/biggrin.gif\" />",$text);
    $text = str_replace("8-)","<image src=\"$config[smilieurl]/music.gif\" />",$text);
    $text = str_replace(":-O","<image src=\"$config[smilieurl]/cry.gif\" />",$text);
    $text = str_replace(":-(","<image src=\"$config[smilieurl]/confused.gif\" />",$text);
    $text = str_replace("(?)","<image src=\"$config[smilieurl]/sneaky2.gif\" />",$text);
    $text = str_replace("(!)","<image src=\"$config[smilieurl]/notify.gif\" />",$text);
    $text = str_replace(":!","<image src=\"$config[smilieurl]/thumbs-up.gif\" />",$text);
    $text = str_replace(":zzz:","<image src=\"$config[smilieurl]/sleepy.gif\" />",$text);
    $text = str_replace(":baaa:","<image src=\"$config[smilieurl]/baaa.gif\" />",$text);
    $text = str_replace(":blush:","<image src=\"$config[smilieurl]/blush.gif\" />",$text);
    $text = str_replace(":inlove:","<image src=\"$config[smilieurl]/inlove.gif\" />",$text);
    $text = str_replace(":stupid:","<image src=\"$config[smilieurl]/withstupid.gif\" />",$text);
    $text = str_replace(":xmas:","<image src=\"$config[smilieurl]/xmas.gif\" />",$text);
    return $text;
}        

function SetDeadLink($dlid) {
    global $config, $dl_table,$db_sql;
    if ($config['deadlink'] == 1) {
        $db_sql->sql_query("UPDATE $dl_table SET status=2 WHERE dlid='$dlid'");
    }
}
            
function holeComment($comid) {
    global $config, $dlcomment_table,$db_sql;
    $sql = $db_sql->query_array("SELECT * FROM $dlcomment_table WHERE comid='$comid'");
    return stripslashes_array($sql);
}
        
function CountComments($dlid) {
    global $dlcomment_table,$db_sql;
    return $db_sql->num_rows($db_sql->sql_query("SELECT comid FROM $dlcomment_table where dlid='$dlid' and com_status='1'"));
}       
        
function GetCatForm() {
    global $cat_table,$config;
    $result = sql_query("SELECT * FROM $cat_table", $link);
    while($cat = mysql_fetch_array($result)) echo "<option value=\"$cat[catid]\">".stripslashes($cat[titel])."</option>\n";
}   
        
function makeCatLink($catid,$subcat,$depth=1) {
    global $cat_table,$db_sql;
    $result2 = $db_sql->sql_query("SELECT * FROM $cat_table WHERE subcat='$subcat' ORDER BY catorder");
    while ($dl_cat = $db_sql->fetch_array($result2)) {
        $dl_cat = stripslashes_array($dl_cat);
        if ($depth != "1") $limiter = str_repeat("--",$depth-1);
        
        if ($dl_cat['catid'] == $catid) {
            $cat_link .= "<option value=\"$dl_cat[catid]\" selected=\"selected\">".$limiter.$dl_cat['titel']."</option>\n";
        } else {
            $cat_link .= "<option value=\"$dl_cat[catid]\">".$limiter.$dl_cat['titel']."</option>\n\t\t\t\t\t\t\t";             
        }        
        $newcat = $dl_cat['catid'];
        $cat_link .= makeCatLink($catid,$newcat,$depth+1);
    }    
    return $cat_link;
}        

function uploadThumbnail($thumbsdir) {
    global $_FILES,$config,$lang;       
    $allowed_pics = array("gif","png","jpg","jpeg");            
    $pic_extension = strtolower(substr(strrchr($_FILES['upl_thumbnail']['name'],"."),1));
            
    if(!in_array($pic_extension,$allowed_pics)) { // Dateierweiterung Vorschaubild ungültig
        $upload = FALSE;
    } else {
        if(file_exists($thumbsdir."/".$_FILES['upl_thumbnail']['name'])) {
            // Zufallsnamen wählen
            $build_destname = strtolower(randomName(8));
            $thumb_destname = $build_destname.".".$pic_extension;
        } else {
            // Filename bleibt
            $thumb_destname = $_FILES['upl_thumbnail']['name'];
        }           
                
        if(@move_uploaded_file($_FILES['upl_thumbnail']['tmp_name'],$thumbsdir."/".$thumb_destname)) { // Datei kopieren
            @chmod($thumbsdir."/".$thumb_destname, 0777); 
            @unlink($thumb_destname);                       
            $upload = $thumb_destname;                      
        } else {
            $upload = FALSE;
        }
    }           
    return $upload;     
}       
        
function randomName($word_len = 10) { 
    $allchar = "ABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789" ; 
    $str = "" ; 
    mt_srand (( double) microtime() * 1000000 ); 
    
    for ( $i = 0; $i<$word_len ; $i++ ) 
    $str .= substr( $allchar, mt_rand (0,25), 1 ) ; 
    
    return $str ; 
}       

function buildBreadCrumb($bread) {
    global $config, $lang;
    $no = count($bread);
    $i = 1;
    foreach($bread as $val => $key) {
        if($i == 0) $breadcrumb .= "<a href=\"".$key."\" class=\"catrow\">".$val."</a>";
        if($i == $no) {
            $breadcrumb .= "<b>".$val."</b>";
        } else {
            $breadcrumb .= "<a href=\"".$key."\" class=\"catrow\">".$val."</a>";
        }
        
        if($i != $no) $breadcrumb .= "&nbsp;&raquo;&nbsp;";
        $i++;
    }
    return $breadcrumb;
}

function realFileSize() {
    $size = 0;
    $handle = @opendir('./files/');
    while ($file = @readdir($handle)) {
        if (eregi("^\.{1,2}$",$file))  continue;
        $size += filesize('./files/'.$file);
    }
    @closedir($handle);  
    return $size;
}  

function getPosticon($value) {
    global $config;
    switch($value) {
        case $config['smilieurl']."/posticons/ausrufezeichen.gif":
            $ch = 1;
            break;
        case $config['smilieurl']."/posticons/biggrin.gif":
            $ch = 2;
            break;
        case $config['smilieurl']."/posticons/boese.gif":
            $ch = 3;
            break;
        case $config['smilieurl']."/posticons/cool.gif":
            $ch = 4;
            break;
        case $config['smilieurl']."/posticons/eek.gif":
            $ch = 5;
            break;
        case $config['smilieurl']."/posticons/frage.gif":
            $ch = 6;
            break;
        case $config['smilieurl']."/posticons/frown.gif":
            $ch = 7;
            break;
        case $config['smilieurl']."/posticons/icon1.gif":
            $ch = 8;
            break;
        case $config['smilieurl']."/posticons/lampe.gif":
            $ch = 9;
            break;
        case $config['smilieurl']."/posticons/mad.gif":
            $ch = 10;
            break;
        case $config['smilieurl']."/posticons/sad.gif":
            $ch = 11;
            break;
        case $config['smilieurl']."/posticons/smilie.gif":
            $ch = 12;
            break;
        case $config['smilieurl']."/posticons/thumb_down.gif":
            $ch = 13;
            break;
        case $config['smilieurl']."/posticons/thumb_up.gif":
            $ch = 14;
            break;
        case $config['smilieurl']."/posticons/tongue.gif":
            $ch = 15;
            break;
        case $config['smilieurl']."/posticons/noicon.gif":
            $ch = 16;
            break;                                                                                                                                                                                                
    }
    return $ch;
}   

function modifyCatChildList($var) {
    $t = substr($var,0,1);
    if($t == ",") {
        $var_modified = substr($var,1);
    } else {
        $var_modified = $var;
    }
    
    $var_reverse = strrev($var_modified);
    $s = substr($var_reverse,0,1);
    
    if($s == ",") {
        return strrev(substr($var_reverse,1));
    } else {
        return strrev($var_reverse);
    }  
}   

?>