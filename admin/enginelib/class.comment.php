<?php
// +----------------------------------------------------------------------+
// | EngineLib - Comment Class                                            |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003,2004 AlexScriptEngine - e-Visions                 |
// +----------------------------------------------------------------------+
// | This code is not freeware. Please read our licence condition care-   |
// | fully to find out more. If there are any doubts please ask at the    |
// | Support Forum                                                        |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Author: Alex Höntschel <info@alexscriptengine.de>                    |
// | Web: http://www.alexscriptengine.de                                  |
// | IMPORTANT: No email support, please use the support forum at         |
// |            http://www.alexscriptengine.de                            |
// +----------------------------------------------------------------------+
//

/**
* class engineComment
*
* Kommentar Klasse aller Engines.
* Die Klasse ist beinhaltet alle nötigen Kommentarfunktionen
* für alle Engines.
*
* Benötigt die Auth-Klasse, Session-Klasse, Template-Klasse und DB-Klasse!
* Kommentare schreiben, lesen, löschen, editieren und zählen
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.comment.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003
* @link http://www.alexscriptengine.de
*/
class engineComment {

    /**
    * engineComment::$table
    *
    * Hält den Tabellennamen
    * @var string
    */
    var $table;

    /**
    * engineComment::$comid
    *
    * DB ID des Kommentars
    * @var int
    */
    var $comid;

    /**
    * engineComment::$userid
    *
    * ID des Kommentarschreibers
    * @var int
    */
    var $userid;

    /**
    * engineComment::$headline
    *
    * Überschrift des Kommentars
    * @var string
    */
    var $headline;

    /**
    * engineComment::$comment
    *
    * Kommentar selbst
    * @var string
    */
    var $comment;

    /**
    * engineComment::$date
    *
    * Timestamp des Kommentars
    * @var string
    */
    var $date;

    /**
    * engineComment::$status
    *
    * Status des Kommentars
    * @var int
    */
    var $status;

    /**
    * engineComment::$postid
    *
    * ID zu welchem dieser Kommentar gehört
    * @var int
    */
    var $postid;

    /**
    * engineComment::$ip
    *
    * IP-Adresse des Users
    * @var string
    */
    var $ip;

    /**
    * engineComment::$username
    *
    * Username des Posters
    * @var string
    */
    var $username;

    /**
    * engineComment::$posticon
    *
    * Posticon des Kommentars
    * @var string
    */
    var $posticon;

    /**
    * engineComment::engineComment()
    *
    * Konstruktor für die Klasse, übergibt Tabellenname
    * und lädt die DB Spalten
    * @access public
    */
    function engineComment($con) {
        if(is_array($con)) {
            $this->initTable($con['table']);
            $this->initDBColumns($con);
            return true;
        } else {
            return false;
        }
    }

    /**
    * engineComment::initTable()
    *
    * initialisiert den Tabellenname
    * @access private
    */
    function initTable($table) {
        return $this->table = $table;
    }

    /**
    * engineComment::initDBColumns()
    *
    * initialisiert die DB Spalten
    * @access private
    */
    function initDBColumns($con) {
        $this->comid = $con['id'];
        $this->userid = $con['userid'];
        $this->headline = $con['headline'];
        $this->comment = $con['comment'];
        $this->date = $con['date'];
        $this->status = $con['status'];
        $this->postid = $con['postid'];
        $this->ip = $con['ip'];
        $this->username = $con['username'];
        $this->posticon = $con['posticon'];
    }

    /**
    * engineComment::writeComment()
    *
    * Schreibt einen Kommentar in die Datenbank
    * @access public
    */
    function writeComment($comment) {
        global $db_sql, $config, $auth, $_POST, $lang;
        
        if($auth->user['userid'] != 2) {
            $writerid = $auth->user['userid'];
            $username = "";
        } else {
            $writerid = 0;
            $username = trim(htmlspecialchars(addslashes($_POST['com_post_name'])));
            if($username=="") $username = $lang['php_guest'];
        }
        
        $direct_post = false;   
        if($auth->user['groupid'] == 1) {
            $direct_post = true;
        }

        if ($config['directpost'] == 1 && !$direct_post) {
            $status = 2;
        } else {
            $status = 1;
        }
        
       if ($config['commentmail'] == 1) $this->sendCommentMail($writerid,$_POST[$this->postid]);
                
        $comment_head = InsertPost($comment_head);
        $db_sql->sql_query("INSERT INTO ".$this->table." (".$this->userid.",".$this->headline.",".$this->comment.",".$this->date.",".$this->status.",".$this->postid.",".$this->ip.",".$this->username.",".$this->posticon.")
                          VALUES ('".$writerid."','".htmlspecialchars(addslashes($_POST['comment_head']))."','".addslashes($_POST['comment_message'])."','".time()."','".$status."','".intval($_POST[$this->postid])."','".addslashes(htmlspecialchars($_POST['user_ip']))."','".addslashes(htmlspecialchars($username))."','".$_POST['posticon']."')");

        return $direct_post;
    }

    /**
    * engineComment::readComment()
    *
    * Liest einen  Kommentar aus der DB
    * @access public
    */
    function readComment($id) {
        $sql = $db_sql->query_array("SELECT * FROM ".$this->table." WHERE ".$this->comid."='".intval($id)."'");
        return stripslashes_array($sql);
    }

    /**
    * engineComment::deleteComment()
    *
    * Löscht einen DB-Eintrag
    * @access public
    */
    function deleteComment($id) {
        $db_sql->sql_query("DELETE FROM ".$this->table." WHERE ".$this->comid."='".intval($id)."'");
    }

    /**
    * engineComment::editComment()
    *
    * Bearbeitet einen DB-Eintrag
    * @access public
    */
    function editComment($id,$message,$moderator) {
        global $config,$_POST;
        $comment_message = $message."\n\n\[edit=".$moderator."\]".aseDate($config['shortdate'],time())."\[/edit\]";
        $db_sql->sql_query("UPDATE ".$this->table." SET ".$this->comment."='".$comment_message."' WHERE ".$this->comid."='".intval($id)."'");
    }
    
    /**
    * engineComment::displayCommentPage()
    *
    * Gibt Seite mit Kommentaren aus
    * vormals CommentArticle()
    * @access public
    */
    function displayCommentPage($id,$userid,$post_name) {
        global $lang;
        $comments = 0;
        $comment_postings = $this->buildComments($id);
        if (!$comment_postings) {
            $backcolor = postcolor($comments);
            //eval ("\$comment_postings .= \"".gettemplate("comment_no_post")."\";");
        }
        //eval ("\$comment_details .= \"".gettemplate("comment_list_head")."\";");
        return $comment_details;
    }
    
    /**
    * engineComment::displayCommentForm()
    *
    * Gibt das Formular für neue Kommentare aus
    * vormals GetCommentForm
    * @access public
    */
    function displayCommentForm($postid,$userid,$com_post_name) {
        global $config, $lang, $auth,$sess;
        $user_ip = engineAuth::getUserIp();
        $comment_back_array = array();
        if ($auth->user['blocked'] != 1) {
            if ($com_post_name == "Name_Input" ) {
                $hidden= "";
                $autor = "<input class=\"input\" name=\"com_post_name\" type=\"text\" value=\"\" size=\"100\" maxlength=\"85\" />";
            } else {
                $hidden = "<input type=\"hidden\" name=\"com_post_name\" value=\"".stripslashes($com_post_name)."\" />";
                if($com_post_name != $lang['php_guest']) {
                    $autor = "<b>".stripslashes($com_post_name)."</b>&nbsp;[<a href=\"".$sess->url("misc.php?action=logout")."\">".$lang['comment_log_out']."</a>]";
                } else {
                    $autor = "<b>".stripslashes($com_post_name)."</b>";
                }
            }
            $comment_back_array = array('user_can_post_comments' => true, 'user_can_post_no_comments' => false, 'user_ip' => $user_ip, 'hidden' => $hidden, 'autor' => $autor);
        } else {
            $comment_back_array = array('user_can_post_comments' => false, 'user_can_post_no_comments' => true, 'user_ip' => $user_ip);
        }
        return $comment_back_array; 
    }
    
    /**
    * engineComment::buildComments()
    *
    * Baut die einzelnen Kommentare auf
    * vormals GetComment()
    * @access private
    */
    function buildComments($id) {
        global $user_table, $avat_table, $config, $group_table, $db_sql, $bbcode, $lang, $auth, $sess, $_ENGINE, $tpl;
        global $comment_loop, $existing_comment, $no_existing_comment;
        $tpl->loadFile('comment_l', 'comment_loop.html');
        
        $result = $db_sql->sql_query(getCommentSQL($this->table,$this->userid,$this->postid,$id,$this->status,$this->date));
        
        if($db_sql->num_rows($result) >= 1) {
            $comment_loop = array();
            
            $comno = 0;
            while($comment = $db_sql->fetch_array($result)) {
                $comment = stripslashes_array($comment);
                unset($comid);
                unset($author);
                unset($gruppe);
                unset($com_avat);
                unset($dabei);
                unset($herkunft);
                unset($gender);
                unset($count_post);
                unset($picon);
                unset($headline);
                unset($user_ip);
                unset($download_comment);
                unset($postdate);
                unset($profile);
                unset($userhp);
                unset($users);
                unset($mod);
                
                $comment = stripslashes_array($comment);
                $posted_std = date("d.m.Y",$comment[$this->date]);
                $posted_date = aseDate($config['shortdate'],$comment[$this->date])." um ".aseDate($config['timeformat'],$comment[$this->date]);
                $headline = trim(stripslashes($comment['com_headline']));
                if ($auth->user['caneditcomments'] == 1 && $auth->user['candeletecomments'] == 1) $mod = "<a href=\"".$sess->url("moderator.php?mod=edit&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_edit.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_edit_mod]\" /></a> <a href=\"".$sess->url("moderator.php?mod=del&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_delete.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_del_mod]\" /></a>";
                if ($auth->user['caneditcomments'] == 1 && $auth->user['candeletecomments'] == 0) $mod = "<a href=\"".$sess->url("moderator.php?mod=edit&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_edit.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_edit_mod]\" /></a>";
                if ($auth->user['caneditcomments'] == 0 && $auth->user['candeletecomments'] == 1) $mod = "<a href=\"".$sess->url("moderator.php?mod=del&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_delete.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_del_mod]\" /></a>";
                            
                if ($comment[$this->userid] == 0) {
                    $author = trim($comment[$this->username]);
                    $dabei = $lang['php_not_registered'];
                } else {
                    $author = $comment['username'];
                    $reg = $comment['regdate'];
                    $regdate = getdate($reg);
                    $dabei = GetGerMonth($regdate['mon'])." ".$regdate['year'];         
    
                    if ($moderator['groupid'] != 8) {
                        $profile = "<a href=\"".definedBoardUrls("memberdetail",$comment[$this->userid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_profile.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"".sprintf($lang['php_profile_of'],$author)."\" /></a>";
                    } else {
                        if ($config['showvisitorinfo'] == 0) $profile = "<a href=\"".definedBoardUrls("memberdetail",$comment[$this->userid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_profile.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"".sprintf($lang['php_profile_of'],$author)."\" /></a>";
                    }
                }
                        
                if ($comment[$this->userid] == 0) {
                    $herkunft = "--";
                } else {
                    $herkunft = trim($comment['location']);
                }
    
                if ($comment[$this->userid] == 0) {
                    $gender = "";
                } else {
                    if ($comment['gender'] == "1") {
                        $gender = "<img src=\"$config[grafurl]/img_male.gif\" width=\"10\" heigth=\"10\" border=\"0\" />";
                    } elseif ($comment['gender'] == "2") {
                        $gender = "<img src=\"$config[grafurl]/img_female.gif\" width=\"10\" heigth=\"10\" border=\"0\" />";
                    } else {
                        $gender = "";
                    }
                }
    
                if ($comment[$this->userid] == 0) {
                    $count_post = "--";
                } else {
                    $count_post = 0;
                    $posts = $db_sql->sql_query("SELECT ".$this->comid." FROM ".$this->table." WHERE ".$this->userid."='".$comment[$this->userid]."'");
                    $count_post = $db_sql->num_rows($posts);
                }
                    
                if ($comment['groupid'] != 0) {
                    $gruppe = $comment['title'];
                } else {
                    $gruppe = $lang['php_not_registered'];
                }
                
                if(USE_ENGINE_AVATARS) {
                    if ($comment['avatarid'] != "0" && $comment['avatarid'] != "") {
                        $com_avat = "<br /><img src=\"$config[avaturl]/$comment[avatardata]\" border=\"0\" />";
                    } else {
                        $com_avat = "";
                    }
                }
                    
                if ($comment[$this->posticon] == "") {
                    $picon = "";
                } else {
                    $picon = "<img src=\"".$comment[$this->posticon]."\" />";
                }
    
                if ($comment['userhp'] == "") {
                    $userhp = "";
                } else {
                    $userhp = "<a href=\"".trim($comment['userhp'])."\"><img src=\"".$_ENGINE['languageurl']."/btn_home.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_goto_hp] $author\" /></a>";
                }
    
                if ($comment['userid'] == 0) {
                    $usermail = "&nbsp;";
                } else {
                    if ($comment['show_email_global'] == "0") {
                        $usermail = "";
                    } else {
                        $usermail = "<a href=\"".$sess->url("misc.php?action=formmailer&memberid=".$comment[$this->userid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_email.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_sendmail] $author\" /></a>";
                    }
                }
                $postcolor = postCss($comno);
                $current_comment = $bbcode->rebuildText($comment[$this->comment]);
                $current_comment = trim($current_comment);

                $comment_loop[] = array('posted_date' => $posted_date,
                                      'mod' => $mod,
                                      'author' => $author,
                                      'dabei' => $dabei,
                                      'profile' => $profile,
                                      'herkunft' => $herkunft,
                                      'count_post' => $count_post,
                                      'gruppe' => $gruppe,
                                      'com_avat' => $com_avat,
                                      'picon' => $picon,
                                      'userhp' => $userhp,
                                      'postcolor' => $postcolor,
                                      'usermail' => $usermail,
                                      'headline' => $headline,
                                      'gender' => $gender,
                                      'current_comment' => $current_comment,
                                      'comment_loop_no_comment_available' => $lang['comment_loop_no_comment_available'],
                                      'comment_loop_registered_since' => $lang['comment_loop_registered_since'],
                                      'comment_loop_location' => $lang['comment_loop_location'],
                                      'comment_loop_gender' => $lang['comment_loop_gender'],
                                      'comment_loop_postings' => $lang['comment_loop_postings']);                
                $comno++;
            }
            
            $existing_comment = true;
            $no_existing_comment = false;
            
            $tpl->parseIf('comment_l', 'existing_comment');
            $tpl->parseIf('comment_l', 'no_existing_comment');           
            
            
            $tpl->parseLoop('comment_l', 'comment_loop');
        } else {
            $existing_comment = false;
            $no_existing_comment = true;
            
            $tpl->register('postcolor', 'list_light');
            $tpl->register('comment_loop_no_comment_available', $lang['comment_loop_no_comment_available']);
            
            $tpl->parseIf('comment_l', 'existing_comment');
            $tpl->parseIf('comment_l', 'no_existing_comment');           
        }
        
        return $tpl->pget('comment_l');
    }
    
    /**
    * engineComment::displayCommentPost()
    *
    * Baut einen einzelnen Kommentar auf
    * um diese im Moderatorenbereich anzuzeigen
    * @access public
    */
    function displayCommentPost($id) {
        global $user_table, $avat_table, $config, $group_table, $db_sql, $bbcode, $lang, $auth, $sess, $_ENGINE, $tpl;

        $comment = $db_sql->query_array(getModeratorCommentSQL($this->table,$this->userid,$this->comid,$id));

        $comment = stripslashes_array($comment);
        $posted_std = date("d.m.Y",$comment[$this->date]);

        if ($auth->user['caneditcomments'] == 1 && $auth->user['candeletecomments'] == 1) $mod = "<a href=\"".$sess->url("moderator.php?mod=edit&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_edit.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_edit_mod]\" /></a> <a href=\"".$sess->url("moderator.php?mod=del&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_delete.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_del_mod]\" /></a>";
        if ($auth->user['caneditcomments'] == 1 && $auth->user['candeletecomments'] == 0) $mod = "<a href=\"".$sess->url("moderator.php?mod=edit&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_edit.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_edit_mod]\" /></a>";
        if ($auth->user['caneditcomments'] == 0 && $auth->user['candeletecomments'] == 1) $mod = "<a href=\"".$sess->url("moderator.php?mod=del&comid=".$comment[$this->comid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_delete.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_fu_del_mod]\" /></a>";
                            
        if ($comment[$this->userid] == 0) {
            $author = trim($comment[$this->username]);
            $dabei = $lang['php_not_registered'];
        } else {
            $author = $comment['username'];
            $reg = $comment['regdate'];
            $regdate = getdate($reg);
            $dabei = GetGerMonth($regdate['mon'])." ".$regdate['year'];         

            if ($moderator['groupid'] != 8) {
                $profile = "<a href=\"".definedBoardUrls("memberdetail",$comment[$this->userid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_profile.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"".sprintf($lang['php_profile_of'],$author)."\" /></a>";
            } else {
                if ($config['showvisitorinfo'] == 0) $profile = "<a href=\"".definedBoardUrls("memberdetail",$comment[$this->userid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_profile.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"".sprintf($lang['php_profile_of'],$author)."\" /></a>";
            }
        }
                        
        if ($comment[$this->userid] == 0) {
            $herkunft = "--";
        } else {
            $herkunft = trim($comment['location']);
        }
    
        if ($comment[$this->userid] == 0) {
            $gender = "";
        } else {
            if ($comment['gender'] == "1") {
                $gender = "<img src=\"$config[grafurl]/img_male.gif\" width=\"10\" heigth=\"10\" border=\"0\" />";
            } elseif ($comment['gender'] == "2") {
                $gender = "<img src=\"$config[grafurl]/img_female.gif\" width=\"10\" heigth=\"10\" border=\"0\" />";
            } else {
                $gender = "";
            }
        }
    
        if ($comment[$this->userid] == 0) {
            $count_post = "--";
        } else {
            $count_post = 0;
            $posts = $db_sql->sql_query("SELECT ".$this->comid." FROM ".$this->table." WHERE ".$this->userid."='".$comment[$this->userid]."'");
            $count_post = $db_sql->num_rows($posts);
        }
            
        if ($comment['groupid'] != 0) {
            $gruppe = $comment['title'];
        } else {
            $gruppe = $lang['php_not_registered'];
        }

        if(USE_ENGINE_AVATARS) {
            if ($comment['avatarid'] != "0" && $comment['avatarid'] != "") {
                $com_avat = "<br /><img src=\"$config[avaturl]/$comment[avatardata]\" border=\"0\" />";
            } else {
                $com_avat = "";
            }
        }
                    
        if ($comment[$this->posticon] == "") {
            $picon = "";
        } else {
            $picon = "<img src=\"".$comment[$this->posticon]."\" />";
        }
        
        if ($comment['userhp'] == "") {
            $userhp = "";
        } else {
            $userhp = "<a href=\"".trim($comment['userhp'])."\"><img src=\"".$_ENGINE['languageurl']."/btn_home.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_goto_hp] $author\" /></a>";
        }
        
        if ($comment['userid'] == 0) {
            $usermail = "&nbsp;";
        } else {
            if ($comment['show_email_global'] == "0") {
                $usermail = "";
            } else {
                $usermail = "<a href=\"".$sess->url("misc.php?action=formmailer&memberid=".$comment[$this->userid])."\"><img src=\"".$_ENGINE['languageurl']."/btn_email.gif\" width=\"55\" heigth=\"17\" border=\"0\" alt=\"$lang[php_sendmail] $author\" /></a>";
            }
        }
        $postcolor = postCss($comno);
        $current_comment = $bbcode->rebuildText($comment[$this->comment]);
        
        $tpl->register('ch'.getPosticon($comment[$this->posticon]), "checked=\"checked\"");

        $tpl->register(array('posted_date' => aseDate($config['shortdate'],$comment[$this->date])." um ".aseDate($config['timeformat'],$comment[$this->date]),
                              'mod' => $mod,
                              'comid' => $id,
                              'postid' => $comment[$this->postid],
                              'author' => $author,
                              'dabei' => $dabei,
                              'profile' => $profile,
                              'herkunft' => $herkunft,
                              'count_post' => $count_post,
                              'gruppe' => $gruppe,
                              'com_avat' => $com_avat,
                              'picon' => $picon,
                              'userhp' => $userhp,
                              'postcolor' => $postcolor,
                              'usermail' => $usermail,
                              'headline' => trim(stripslashes($comment['com_headline'])),
                              'gender' => $gender,
                              'current_comment' => trim($current_comment),
                              'textarea_comment' => $comment[$this->comment],
                              'moderator_registered_since' => $lang['moderator_registered_since'],
                              'moderator_location' => $lang['moderator_location'],
                              'moderator_gender' => $lang['moderator_gender'],
                              'moderator_postings' => $lang['moderator_postings']));        
        
        
    
    
    }
    
    /**
    * engineComment::sendCommentMail()
    *
    * Schickt eine Email an den Administrator
    * vormals SendComment()
    * @access private
    */
    function sendCommentMail($userid,$id) {
        global $config, $admin_lang, $lang, $auth, $_ENGINE;
        if($auth->user['userid'] != 2) {
            $poster['username'] = $auth->user['username'];
            $poster['useremail'] = $auth->user['useremail'];
        } else {
            if($config['guestpost'] == 1) {
                $poster['username'] = "Guestposting";
            } else {
                $poster['username'] = trim(htmlspecialchars(addslashes($_POST['com_post_name'])));
            }
            $poster['useremail'] = "";
        }
        
        if($poster['username']=="") $poster['username'] = $lang['php_guest'];
        
        $inhalt = sprintf($lang['mail_comment_inhalt'],$poster['username'],$id).sprintf($lang['mail_footer'],$config['scriptname']);
        include_once($_ENGINE['eng_dir']."admin/enginelib/class.phpmailer.php");
        $mail = new PHPMailer();
        
        $mail->SetLanguage($lang['php_mailer_lang'], $_ENGINE['eng_dir']."lang/".$config['language']."/");
        if($config['use_smtp']) {
            $mail->IsSMTP();
            $mail->Host = $config['smtp_server'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp_username'];
            $mail->Password = $config['smtp_password'];
        } else {
            $mail->IsMail();
        }

        $mail->From = $poster['useremail'];
        $mail->FromName = $poster['username'];
        $mail->AddAddress($config['admin_mail'],$config['scriptname']);
        $mail->Subject = $lang['mail_comment_betreff'];
        $mail->Body = $inhalt;
        $mail->WordWrap = 50;
        $mail->Send();
    }
}

?>