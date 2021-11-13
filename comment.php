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
|   > Kommentare zu Downloads
|
|   > Dieses Script ist KEINE Freeware. Bitte lesen Sie die Lizenz-
|   > bedingungen (read-me.html) für weitere Informationen.
|   >-----------------------------------------------------------------
|   > This script is NOT freeware! Please read the Copyright Notice
|   > (read-me_eng.html) for further information. 
|
|   > $Id: comment.php 6 2005-10-08 10:12:03Z alex $
|
+--------------------------------------------------------------------------
*/

include_once('lib.inc.php');
include_once($_ENGINE['eng_dir']."admin/enginelib/function.".ENG_TYPE.".php");
include_once($_ENGINE['eng_dir']."admin/enginelib/class.comment.php");

/*
Comments per Page
Set this to 0 to show all comments on one page (deactivate)
*/
$comments_per_page = 10;

if($_GET['action'] != "popup") {
    $tpl->register(array('comment_js_too_long' => $lang['comment_js_too_long'],
                        'comment_js_maximum' => $lang['comment_js_maximum'],
                        'comment_js_long1' => $lang['comment_js_long1'],
                        'comment_js_long2' => $lang['comment_js_long2'],
                        'comment_js1' => $lang['comment_js1'],
                        'comment_js2' => $lang['comment_js2'],
                        'comment_js3' => $lang['comment_js3'],
                        'comment_js4' => $lang['comment_js4'],                        
                        'max_comment_length' => $config['max_comment_length']));
    
    $tpl->loadFile('meta', 'comment_js.html'); 
    $tpl->register('header_add', $tpl->pget('meta'));
    
    $tpl->loadFile('main', 'comment.html'); 
    $tpl->register('title', $lang['title_comment']);
    
    $conditions = array("table"=>$dlcomment_table,
                        "id"=>"comid",
                        "userid"=>"userid",
                        "headline"=>"com_headline",
                        "comment"=>"dl_comment",
                        "date"=>"com_date",
                        "status"=>"com_status",
                        "postid"=>"dlid",
                        "ip"=>"user_ip",
                        "username"=>"user_comname",
                        "posticon"=>"posticon");
                        
    $ct = new engineComment($conditions);    
    
} else {
    define('IS_POPUP',true);
    $tpl->loadFile('meta', 'comment_js.html'); 
    $tpl->register('header_add', $tpl->pget('meta'));
    
    $tpl->loadFile('main', 'comment_popup.html'); 
    
    $img = $db_sql->query_array("SELECT dltitle,thumb FROM $dl_table WHERE dlid='".intval($_GET['dlid'])."'");
    $size = @getimagesize("./thumbnail/".$img['thumb']);
    if($size[0] != "") $width = "width=\"".$size[0]."\"";
    if($size[1] != "") $height = "height=\"".$size[1]."\"";
    $alt = $img['dltitle']; 
    $title = $img['dltitle'];   
    $pic = $config['thumburl']."/".$img['thumb'];
    
    $tpl->register(array('alt' => $img['dltitle'],
                        'title' => $img['dltitle'],
                        'pic' => $config['thumburl']."/".$img['thumb'],
                        'width' => $width,
                        'height' => $height));
    
    $tpl->pprint('main'); 
    exit;
}

if (!isset($_POST['dlid']) && !isset($_GET['dlid'])) {
    header("Location: ".$sess->url("index.php"));
    exit;
}

if (isset($_POST['comment']) && $_POST['comment']=='send') {
    if ($auth->user['canpostcomments']) {
        if ($_POST['comment_message'] == "") {
            rideSite($sess->url('comment.php?dlid='.$_POST['dlid']), $lang['rec_error11']);
            exit();      
        } else {
            $comment['postid'] = $_POST['dlid'];
        
            $direct_post = $ct->writeComment($comment);
            
            if ($config['directpost'] == 1 && !$direct_post) {
                $com_status = 2;
            } else {
                $com_status = 1;
                $db_sql->sql_query("UPDATE $dl_table SET comment_count=comment_count+1 WHERE dlid='".$_POST['dlid']."'");
            }              
                    
            if ($config['directpost'] == 1 && !$direct_post) {
                rideSite($sess->url('comment.php?dlid='.$_POST['dlid']), $lang['rec_error9']);
                exit();         
            } else {
                rideSite($sess->url('comment.php?dlid='.$_POST['dlid']), $lang['rec_error10']);
                exit();         
            }
        }
    }
}

if (isset($_POST['vote'])) {
    $error_title = VoteFile($_POST['vote'],$_POST['dlid'],$auth->user['userid']);
    rideSite($sess->url('comment.php?dlid='.$_POST['dlid']), $error_title);
    exit();        
}
$dlid = "";
if($_POST['dlid']) $dlid = $_POST['dlid'];
if($_GET['dlid']) $dlid = $_GET['dlid'];   
   
if ($config['top_list']) {
    $result4 = $db_sql->sql_query("SELECT dlhits FROM $dl_table");
    while (list($dlhits) =mysql_fetch_row($result4)) {
        $hitsalt = $dlhits;
        $total_dls += $hitsalt;
    }
}   

$file = GetFile($dlid);

if($file['status'] == '3') {
    rideSite($sess->url('index.php?subcat='.$file['catid']), $lang['rec_error55']);
    exit();     
}

include_once($_ENGINE['eng_dir']."admin/enginelib/class.bbcode.php");
$bbcode = new engineBBCode(1,0,1,0,$config['smilieurl']);   
            
if ($file['dlpoints'] != 0) {
    $dl_divp = $file['dlpoints'] / $file['dlvotes'];
    $points = round($dl_divp,2);
}

if($config['active_image_resizer']) {
    include_once($_ENGINE['eng_dir']."admin/enginelib/function.img.php");
    if(chkgd2() >= 2) {
        $resize_img = true;
    } else {
        $resize_img = false;
    }
     $pic = buildThumbnail($file,$resize_img);
} else {
    if ($file['thumb'] != "0" && $file['thumb'] != "") {
        $size = @getimagesize("./thumbnail/".$file['thumb']);
        if($size[0] != "") $thumb_width = "width=\"".$size[0]."\"";
        if($size[1] != "") $thumb_height = "height=\"".$size[1]."\"";
        $pic = "<img src=\"$config[thumburl]/$file[thumb]\" $thumb_width $thumb_height border=\"0\" align=\"left\" />";
    } else {
        $pic = "";
    }       
}

if($pic && file_exists("./thumbnail/thumb_".$file['thumb'])) {  
    $parse_link_to_image = true;
    $link_to_pic = "<a href=\"javascript:popupWindow('".$sess->url('comment.php?action=popup&dlid='.$file['dlid'])."')\">".$lang['comment_click_to_enlarge']."</a><br><br>";
}


if ($file['onlyreg'] == 0 || $auth->user['canaccessregisteredfiles'] == "1") {
    $headlink = "<a class=\"list_headline\" href=\"".$sess->url("redirect.php?dlid=".$file['dlid'])."\">".trim($file['dltitle'])."</a>";
    $piclink = "<a class=\"cat\" href=\"".$sess->url("redirect.php?dlid=".$file['dlid'])."\"><img src=\"".$_ENGINE['languageurl']."/img_download.gif\" width=\"105\" height=\"22\" border=\"0\" align=\"absmiddle\"></a>";
} else {
    $headlink = trim($file['dltitle'])." *<br><font size=\"1\">".$lang['index_registered_members_only']."</font>";
    $piclink = "<img src=\"".$_ENGINE['languageurl']."/img_disabled_download.gif\" width=\"105\" height=\"22\" border=\"0\" align=\"absmiddle\">";
}       

$file_author = trim($file['dlauthor']);
    
if ($file['hplink'] != "" && $file['hplink'] != "0") {
    $homep = " <a href=\"".trim(htmlspecialchars($file['hplink']))."\"><img src=\"$config[grafurl]/img_homepage.gif\" alt=\"$lang[php_goto_hp] $file_author\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\" /></a> ";
} else {
    $homep = "";
}

if ($file['authormail'] != "") $email_author = "<a class=\"infile\" href=\"".$sess->url("misc.php?action=formmailer&dlid=".$file['dlid'])."\"><img src=\"$config[grafurl]/img_email.gif\" alt=\"$lang[php_sendmail] $file_author\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\" /></a>";

if ($file['status'] == 1 ) {
    $deadlink = " <a class=\"infile\" href=\"".$sess->url("deadlink.php?subcat=".$file['catid']."&dlid=".$file['dlid'])."\">".$lang['index_report_deadlink']."</a>";
} else {
    $deadlink = " ".$lang['comment_link_will_be_checked_soon'];
}

if ($config['newmark']) $graph = newgraph($file['dl_date']);

if($config['updatemark'] && $file['update_date']) $update_mark = buildUpdate($file['update_date']);
    
if ($config['top_list']) $cool = CoolDL($file['dlhits'],$total_dls);
$stars = Stars($points);
$loadtime = LoadTime($file['dlsize']);

$tpl->register(array('headlink' => $headlink,
                        'index_comment' => $lang['index_comment'],
                        'index_rate_file' => $lang['index_rate_file'],
                        'index_mail_a_friend' => $lang['index_mail_a_friend'],
                        'index_options' => $lang['index_options'],
                        'gfx_graph' => $graph,
                        'gfx_cool' => $cool,
                        'gfx_stars' => $stars,
                        'gfx_update' => $update_mark,
                        'file_description' => $bbcode->rebuildText($file['dldesc']),
                        'file_total_downloads' => sprintf($lang['index_total_downloads'],$file['dlhits']),
                        'file_total_time_to_download' => LoadTime($file['dlsize']),
                        'options_deadlink' => $deadlink,
                        'options_comment_count' => $file['comment_count'],
                        'options_comment_url' => $sess->url('comment.php?dlid='.$file['dlid']).'#comm',
                        'options_rate_url' => $sess->url('comment.php?dlid='.$file['dlid']).'#rate',
                        'options_recommend_url' => $sess->url('recommend.php?dlid='.$file['dlid']),
                        'file_pic' => $pic,
                        'link_to_pic' => $link_to_pic,
                        'comment_total_votes_on_rating' => sprintf($lang['comment_total_votes_on_rating'],$file['dlvotes']),
                        'comment_current_rating' => sprintf($lang['comment_current_rating'],$points),
                        'dlid' => $file['dlid'],
                        'comment_alternative_download' => $lang['comment_alternative_download'],
                        'comment_rating' => $lang['comment_rating'],
                        'comment_only_rate_once_for_a_file' => $lang['comment_only_rate_once_for_a_file'],
                        'comment_rate_with_your_real_opinion' => $lang['comment_rate_with_your_real_opinion'],
                        'comment_excelent_file' => $lang['comment_excelent_file'],
                        'comment_really_bad' => $lang['comment_really_bad'],                        
                        'direct_download_link' => $piclink,
                        'author_information' => sprintf($lang['index_author_of_file'],trim($file['dlauthor']),$email_author,$homep,aseDate($config['shortdate'],$file['dl_date']))));

$result_mirror = $db_sql->sql_query("SELECT * FROM $mirror_table WHERE dlid='".$file['dlid']."' ORDER BY mirror_date DESC");
if($db_sql->num_rows($result_mirror) >= 1) { 
    $parse_alternate_downloads = true;
    $alternate_download_loop = array();
    while($mirror = $db_sql->fetch_array($result_mirror)) {
        $mirror = stripslashes_array($mirror);
        $added_date = aseDate($config['longdate'],$mirror['mirror_date'],1);
        $mirror_id = $mirror['mirror_id'];
        $alternate_download_loop[] = array(
                                            'mirror_text' => trim($mirror['mirror_text']),
                                            'added_date' => sprintf($lang['comment_mirror_added_at'],aseDate($config['longdate'],$mirror['mirror_date'],1)),
                                            'mirror_url' => $sess->url('redirect.php?mirror_id='.$mirror['mirror_id']));
    }
    $tpl->parseLoop('main', 'alternate_download_loop');
}

if($comments_per_page >= 1) {
    $over_all = $db_sql->query_array("SELECT Count(*) as total FROM ".$ct->table." WHERE ".$ct->postid."='".intval($file['dlid'])."' AND com_status='1'");
    
    include_once($_ENGINE['eng_dir']."admin/enginelib/class.nav.php");
    if(!isset($_GET['start'])) {
        $start = 0;
    } else {
        $start = intval($_GET['start']);
    }
    $nav = new Nav_Link();
    $nav->overAll = $over_all['total'];
    $nav->perPage = $comments_per_page;
    $nav->DisplayLast = 1;
    $nav->DisplayFirst = 1;    
    $nav->MyLink = $sess->url("comment.php?dlid=".intval($file['dlid']))."&amp;";
    $nav->LinkClass = "page_step";
    $nav->start = $start;
    $pagecount = $nav->BuildLinks();
    if(!$pagecount) $pagecount = "<b>1</b>";
    $pages = intval($over_all['total'] / $nav->perPage);
    if($over_all['total'] % $nav->perPage) $pages++;        
    $tpl->register('pagecount', sprintf($lang['comment_pagecount'],$pages,$pagecount,$over_all['total']));
}

if($auth->user['canpostcomments'] == 1) {
    if($auth->user['userid'] == 2) {
        if($config['guestpost'] == 1) {
            $com_post_name = $lang['php_guest'];
            $comment_details = $ct->buildComments($file['dlid']);
            $comment_form = $ct->displayCommentForm($file['dlid'],$auth->user['userid'],$com_post_name);
        } else {
            $com_post_name = "Name_Input";
            $comment_details = $ct->buildComments($file['dlid']);
            $comment_form = $ct->displayCommentForm($file['dlid'],$auth->user['userid'],$com_post_name);
        }
    } else {
        $comment_details = $ct->buildComments($file['dlid']);
        $comment_form = $ct->displayCommentForm($file['dlid'],$auth->user['userid'],$auth->user['username']);
    }
} else {
    $comment_form = array('user_can_post_comments' => false, 'user_can_post_no_comments' => true);  
    $com_post_name = $lang['php_guest'];
    $comment_details = $ct->buildComments($file['dlid']);
}

$tpl->register('comments', $comment_details);

$user_can_post_comments = $comment_form['user_can_post_comments'];
$user_can_post_no_comments = $comment_form['user_can_post_no_comments'];

if($comment_form['user_can_post_comments']) {
    $tpl->register(array('hidden' => $comment_form['hidden'],
                         'input' => $comment_form['autor'],
                         'newsid' => $newsid,
                         'user_ip' => $comment_form['user_ip']));
}                    

$tpl->register('breadcrumb', buildBreadCrumb(array($lang['php_overall_home'] => $config['mainurl'], $lang['title_engine'] => $sess->url('index.php'), trim($file['titel']) => $sess->url('index.php?subcat='.$file['catid']) ,trim($file['dltitle']) => $sess->url('comment.php?dlid='.$file['dlid']),sprintf($lang['comment_comments'],aseDate($config['shortdate'],$entry['postdate'])) => '')));

$tpl->register(array(
                    'comment_comments' => sprintf($lang['comment_comments'],aseDate($config['shortdate'],$entry['postdate'])),
                    'comment_comments_for_the_file' => sprintf($lang['comment_comments_for_the_file'],trim($file['dltitle'])),
                    'comment_button_alt_answer' => $lang['comment_button_alt_answer'],
                    'comment_link_write_answer' => $lang['comment_link_write_answer'],
                    'comment_author' => $lang['comment_author'],
                    'comment_message' => $lang['comment_message'],
                    'comment_write_your_comment_here' => $lang['comment_write_your_comment_here'],
                    'comment_username' => $lang['comment_username'],
                    'comment_topic' => $lang['comment_topic'],
                    'comment_posticons' => $lang['comment_posticons'],
                    'comment_your_message' => $lang['comment_your_message'],
                    'comment_bold' => $lang['comment_bold'],
                    'comment_italic' => $lang['comment_italic'],
                    'comment_underline' => $lang['comment_underline'],
                    'comment_url' => $lang['comment_url'],
                    'comment_email' => $lang['comment_email'],
                    'comment_code' => $lang['comment_code'],
                    'comment_quote' => $lang['comment_quote'],
                    'comment_center' => $lang['comment_center'],
                    'comment_line' => $lang['comment_line'],
                    'comment_click_smilies' => $lang['comment_click_smilies'],
                    'comment_check_message_length' => $lang['comment_check_message_length'],
                    'comment_add_comment_btn' => $lang['comment_add_comment_btn'],
                    'comment_reset_btn' => $lang['comment_reset_btn'],
                    'comment_not_allowed_to_post_comment' => $lang['comment_not_allowed_to_post_comment']));

$tpl->parseIf('main', 'parse_link_to_image');
$tpl->parseIf('main', 'parse_alternate_downloads');
$tpl->parseIf('main', 'user_can_post_comments');
$tpl->parseIf('main', 'user_can_post_no_comments');

$tpl->register('query', showQueries($develope));
$tpl->register('header', $tpl->pget('header'));

$tpl->register('footer', $tpl->pget('footer'));
$tpl->pprint('main'); 
?>