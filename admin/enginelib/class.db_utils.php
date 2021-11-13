<?php
// +----------------------------------------------------------------------+
// | EngineLib - DB Utilities Class                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003,2004 AlexScriptEngine - e-Visions                 |
// +----------------------------------------------------------------------+
// | This code is not freeware. Please read our licence condition care-   |
// | fully to find out more. If there are any doubts please ask at the    |
// | Support Forum.                                                       |
// | Inspired by PHPMyAdmin, small apps from phpclasses.org and           |
// | the mysqlop.class.php from Ben Yacoub Hatem                          |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Author: Alex Höntschel <info@alexscriptengine.de>                    |
// | Web: http://www.alexscriptengine.de                                  |
// | IMPORTANT: No email support, please use the support forum at         |
// |            http://www.alexscriptengine.de                            |
// +----------------------------------------------------------------------+
//

/**
* class engineMysqlUtils
*
* Klasse enthält Hilffunktionen für die Datenbank
* Benötigt wird die normale DB-Klasse und Session-Klasse.
*
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.db_utils.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2004,2005
* @link http://www.alexscriptengine.de
*/
class engineMysqlUtils {

    /**
    * engineMysqlUtils::$options
    *
    * Array mit div. Optionen
    * für die Klasse
    * @var array
    */
    var $options = array();
    
    /**
    * engineMysqlUtils::engineMysqlUtils()
    *
    * Konstruktor der Klasse
    *
    * @param array $options
    * @access public
    */
    function engineMysqlUtils($options=array()) {
        if(is_array($options) && $options['time_limit'] != "") {
            $this->options['time_limit'] = $options['time_limit'];
        } else {
            $this->options['time_limit'] = 1200;
        }

        if(is_array($options) && $options['backup_dir'] != "") {
            $this->options['backup_dir'] = $options['backup_dir'];
        } else {
            $this->options['backup_dir'] = "./backup";
        }
    }

    /**
    * engineMysqlUtils::readBackups()
    *
    * Gibt im AC eine Übersicht der vorhandenen
    * Backups aus und stellt Optionen zur
    * Verfügung
    *
    * @param string $backup_folder
    * @access public
    */
    function readBackups($backup_folder = "./backup") {
        global $config, $a_lang, $sess;
        if ($backup_folder == "") $backup_folder = "./backup";
        $file_list = array();
        $handle = @opendir($backup_folder);
        while ($file = @readdir($handle)) {
            if (@is_file($backup_folder."/".$file) && $file!="index.html" && $file != "." && $file != "..") $file_list[] = $file;
        }
        closedir($handle);
        if (empty($file_list) || !is_array($file_list)) {
            $message = $a_lang['adminutil_1'];
            return false;
        } else {
            for($i = 0; $i < sizeof($file_list); $i++) {
                $size = rebuildFileSize(@filesize($backup_folder."/".$file_list[$i]));
                $title = "<b>".$file_list[$i]."</b> ($size)";
                $value = "<a href=\"".$sess->adminUrl("adminutil.php?action=restore&bckfile=".$file_list[$i])."\">".$a_lang['adminutil_2']."</a>&nbsp;|&nbsp;<a href=\"".$sess->adminUrl("adminutil.php?action=download&bckfile=".$file_list[$i])."\">".$a_lang['adminutil_3']."</a>&nbsp;|&nbsp;<a href=\"".$sess->adminUrl("adminutil.php?action=delete&bckfile=".$file_list[$i])."\">".$a_lang['adminutil_4']."</a>";
                buildStandardRow($title,$value);
            }
        }
    }

    /**
    * engineMysqlUtils::splitDump()
    *
    * Splittet eingelesenes SQL-File in
    * unterschiedliche Zeilen und somit
    * in einzelne SQL-Befehle auf
    *
    * @param string $sql
    * @access private
    */
    function splitDump($sql) {
        $sql = preg_replace("/\r/s", "\n", $sql);
        $sql = preg_replace("/[\n]{2,}/s", "\n", $sql);
        $lines = explode("\n", $sql);
        $queries = array();
        $in_query = 0;
        $i = 0;
        foreach($lines as $line){
            $line = trim($line);
            if(!$in_query) {
                if(preg_match("/^CREATE/i", $line)) {
                    $in_query = 1;
                    $queries[$i] = $line;
                } elseif(!empty($line) && $line[0] != "#") {
                    $queries[$i] = preg_replace("/;$/i", "", $line);
                    $i++;
                }
            } elseif($in_query) {
                if(preg_match("/^[\)]/", $line)) {
                    $in_query = 0;
                    $queries[$i] .= preg_replace("/;$/i", "", $line);
                    $i++;
                } elseif(!empty($line) && $line[0] != "#") {
                    $queries[$i] .= $line;
                }
            }
        }
        return $queries;
    }

    /**
    * engineMysqlUtils::getTableDefinitions()
    *
    * Baut den Create-Table Abschnitt der *sql-Files
    *
    * @param string $table
    * @param string $crlf
    * @access private
    */
    function getTableDefinitions($table, $crlf) {
        global $db_sql, $tables_info;
        $dump  = "DROP TABLE IF EXISTS $table;$crlf";
        if($this->getMysqlVersion() >= 32321) {
            $db_sql->sql_query('SET SQL_QUOTE_SHOW_CREATE = 0');
                if($row = $db_sql->query_array("SHOW CREATE TABLE $table")) $dump .= str_replace("\n", $crlf, $row[1]);
                $db_sql->free_result();
                echo $dump.";".$crlf;
                return true;
        }
        $dump .= "CREATE TABLE $table (".$crlf;
        $result = $db_sql->sql_query("SHOW FIELDS FROM $table");
        while($row = $db_sql->fetch_array($result)) {
            $dump .= "   ".$row['Field']." ".$row['Type'];
            if($row['Null'] != "YES") $dump .= " NOT NULL";
            if(isset($row['Default']) && $row['Default'] != "") $dump .= " DEFAULT '".$row['Default']."'";
            if($row['Extra'] != "") $dump .= " ".$row['Extra'];
            $dump .= ",".$crlf;
        }
        $dump = preg_replace("/,".$crlf."$/", "", $dump);
        $db_sql->free_result();

        $result = $db_sql->sql_query("SHOW KEYS FROM $table");
        $index = array();
        while($row = $db_sql->fetch_array($result)) {
            if($row['Key_name'] != "PRIMARY" && $row['Non_unique'] == 0) $row['Key_name'] = "UNIQUE|".$row['Key_name'];
            if(isset($row['Comment']) && $row['Comment'] == "FULLTEXT") $row['Key_name'] = "FULLTEXT|".$row['Key_name'];
            if(!isset($index[$row['Key_name']])) {
                $index[$row['Key_name']] = $row['Column_name'];
            } else {
                $index[$row['Key_name']] .= ", ".$row['Column_name'];
            }
        }
        $db_sql->free_result();

        if(!empty($index)) {
            foreach($index as $key => $val) {
                preg_match("/(PRIMARY|UNIQUE|FULLTEXT)?[\|]?(.*)/i", $key, $regs);
                $dump .= ",".$crlf."   ".(!empty($regs[1]) ? $regs[1]." " : "")."KEY".(!empty($regs[2]) ? " ".$regs[2] : "")." (".$val.")";
            }
        }
        $dump .= $crlf.")".((isset($tables_info[$table])) ? " TYPE=".$tables_info[$table] : "").";";
        $dump .= "#----------------------------------------------------------".$crlf;
        echo $dump.$crlf;
        return true;
    }


    /**
    * engineMysqlUtils::getTableContent()
    *
    * Holt einzelne Daten und baut den Inhalts-
    * bereich für die einzelnen Tabellen in den
    * *.sql-Files
    *
    * @param string $table
    * @param string $crlf
    * @access private
    */
    function getTableContent($table, $crlf) {
        global $db_sql;
        $result = $db_sql->sql_query("SELECT * FROM $table");
        if($result && $db_sql->num_rows($result)) {
            echo $crlf."#".$crlf."# Table Data for ".$table.$crlf."#".$crlf;
            $column_list = "";
            $num_fields = @mysql_num_fields($result);
            for($i = 0; $i < $num_fields; $i++) $column_list .= (($column_list != "") ? ", " : "").@mysql_field_name($result, $i);
        }

        while($row = $db_sql->fetch_array($result)) {
            $dump = "INSERT INTO ".$table." (".$column_list.") VALUES (";
            for($i = 0; $i < $num_fields; $i++) {
                $dump .= ($i > 0) ? ", " : "";
                if(!isset($row[$i])) {
                    $dump .= "NULL";
                } elseif($row[$i] == "0" || $row[$i] != "") {
                    $type = @mysql_field_type($result, $i);
                    if($type == "tinyint" || $type == "smallint" || $type == "mediumint" || $type == "int" || $type == "bigint"  || $type == "timestamp") {
                        $dump .= $row[$i];
                    } else {
                        $search_array = array('\\', '\'', "\x00", "\x0a", "\x0d", "\x1a");
                        $replace_array = array('\\\\', '\\\'', '\0', '\n', '\r', '\Z');
                        if(getPHPVersion() >= 405) {
                            $row[$i] = str_replace($search_array, $replace_array, $row[$i]);
                        } else {
                            for ($i = 0; $i < sizeof($search_array); $i++) $row[$i] = str_replace($search_array[$i], $replace_array[$i], $row[$i]);
                        }
                        $dump .= "'".$row[$i]."'";
                    }
                } else {
                    $dump .= "''";
                }
            }
            $dump .= ');';
            echo $dump.$crlf;

        }
        echo "#----------------------------------------------------------".$crlf;
        echo $crlf;
        return true;
    }

    /**
    * engineMysqlUtils::buildDatabaseSize()
    *
    * Gibt die tats. Datenbankgrösse zurück
    *
    * @param string $db_name
    * @access public
    */
    function buildDatabaseSize($db_name) {
        global $db_sql;
        $database_size = 0;
        if($this->getMysqlVersion() >= 32303) {
            $db = ($this->getMysqlVersion() >= 32306)  ? "`$db_name`" : $db_name;
            if($result = $db_sql->sql_query("SHOW TABLE STATUS FROM $db")) {
                while($row = $db_sql->fetch_array($result)) {
                    if(eregi('^(MyISAM|ISAM|HEAP|InnoDB)$', $row['Type'])) $database_size += $row['Data_length'] + $row['Index_length'];
                }
            }
        }
        return $database_size;
    }

    /**
    * engineMysqlUtils::buildBackup()
    *
    * Führt Backup aus und speichert es
    * im vorgegebenem Ordner auf dem Server
    * ab
    *
    * @param string $db_name
    * @param string $structure_only
    * @access public
    */
    function buildBackup($db_name,$structure_only="") {
        global $_POST,$_ENGINE,$db_sql,$config;
        $db_tables = $_POST['db_tables'];
        if(getUserOS() == "WIN") {
            $crlf = "\r\n";
        } elseif(getUserOS() == "MAC") {
            $crlf = "\r";
        } else {
            $crlf = "\n";
        }

        $tables_info = array();
        $db = ($this->getMysqlVersion >= 32306)  ? "`$db_name`" : $db_name;
        $result = $db_sql->sql_query("SHOW TABLE STATUS FROM $db");
        while ($row = $db_sql->fetch_array($result)) $tables_info[$row['Name']] = $row['Type'];
        $db_sql->free_result($result);

        ob_start();
        @ob_implicit_flush(0);

        echo "#----------------------------------------------------------".$crlf;
        echo "# Database Backup for ".$config['scriptname'].$crlf;
        echo "# MySQL-Version: ".$this->getMysqlVersion(true).$crlf;
        echo "# ".date("Y-m-d H:i").$crlf;
        echo "#----------------------------------------------------------".$crlf;
        foreach($db_tables as $table) {
            @set_time_limit($this->time_limit);
            echo $crlf."#".$crlf."# Structure for Table ".$table.$crlf."#".$crlf;
            $this->getTableDefinitions($table, $crlf);
            if(!$structure_only) $this->getTableContent($table, $crlf);
        }

        $contents = ob_get_contents();
        ob_end_clean();

        @umask(0111);
        $filename = $this->options['backup_dir']."/ase_backup_".date("Ymd_Hi").".sql";
        $fp = fopen($filename, "w");
        $done = fwrite($fp, $contents);
        fclose($fp);

        if($done) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * engineMysqlUtils::restoreBackup()
    *
    * Auf dem Server vorhandene Backups
    * werden wieder in die Datenbank
    * eingespielt
    *
    * @param string $bckfile
    * @access public
    */
    function restoreBackup($bckfile) {
        global $db_sql;
        ob_start();
        @ob_implicit_flush(0);

        readfile($this->options['backup_dir']."/".$bckfile);
        $contents = ob_get_contents();
        ob_end_clean();

        if(!empty($contents)) {
            $split_file = $this->splitDump($contents);
            foreach($split_file as $sql) {
                $sql = trim($sql);
                if(!empty($sql) and $sql[0] != "#") {
                    @set_time_limit($this->options['time_limit']);
                    $db_sql->sql_query($sql);
                }
            }
        }
        return true;
    }

    /**
    * engineMysqlUtils::downloadBackup()
    *
    * Download vorhandener *.sql-Files im
    * vorgegebenen Ordner auf dem Server
    *
    * @param string $bckfile
    * @access public
    */
    function downloadBackup($bckfile) {
    	header("Content-type: application/x-unknown");
    	header("Content-Disposition: attachment; filename=$bckfile");
        header('Pragma: no-cache');
        header('Expires: 0');	
    	readfile($this->options['backup_dir']."/".$bckfile);
    	exit;
    }

    /**
    * engineMysqlUtils::deleteBackup()
    *
    * Löscht bestimmtes Backup auf dem Server
    *
    * @param string $bckfile
    * @access public
    */
    function deleteBackup($bckfile) {
    	if(@unlink($this->options['backup_dir']."/".$bckfile)) {
    		return true;
    	} else {
    		return false;
    	}
    }

    /**
    * engineMysqlUtils::getMysqlVersion()
    *
    * Liefert die MySQL-Version zurück
    *
    * @param bool $readable
    * @access public
    */
    function getMysqlVersion($readable=false) {
        global $db_sql;
        $result = $db_sql->sql_query('SELECT VERSION() AS version');
        if($result != FALSE && @$db_sql->num_rows($result) > 0) {
            $row   = $db_sql->fetch_row($result);
            $match = explode('.', $row[0]);
            $db_sql->free_result();
        }
        if(!isset($row)) {
            $mysql_version = 32332;
            $human_readable = '3.23.32';
        } else{
            $mysql_version = (int)sprintf('%d%02d%02d', $match[0], $match[1], intval($match[2]));
            $human_readable = $row[0];
            unset($result, $row, $match);
        }
        if($readable) {
            return $human_readable;
        } else {
            return $mysql_version;
        }
    }
    
	/**
	* engineMysqlUtils::getDbTables()
	*
	* Liest Tabellennamen der DB aus und
	* gibt diese zurück
	*
	* @param string $db_name	
	* @access private
	* @return void
	**/
	function getDbTables($db_name){
        $result = @mysql_list_tables($db_name);
	
	    if ($result) {
            while ($row = mysql_fetch_row($result)) $Tables[] = $row[0];
	    } else {
            return false;
        }
		return $Tables;		
	}

	/**
	* engineMysqlUtils::optimizeTables()
	*
	* Stellt die eigentliche Optimierungsfunktion zur Ver-
	* fügung (REPAIR und ANALYZE, wird aber nicht verwendet)
	* Führt bei Aufruf die Optimierung der Tabellen durch
	*
	* @param string $operation	
	* @access public
	* @return void
	**/
	function optimizeTables($operation = "OPTIMIZE",$db_name){
        global $db_sql,$bgcount,$a_lang,$sess;
		if ($operation!= "OPTIMIZE" or $operation!= "ANALYZE" or $operation!= "REPAIR") {
		    $operation = "OPTIMIZE";
		}
		
		$Tables = $this->getDbTables($db_name);
		$query = "$operation TABLE ";
		foreach($Tables as $k=>$v){
			$query .= " `$v`,";
		}
		$query = substr($query,0,strlen($query)-1);
		$result = @$db_sql->sql_query($query);
		$res = $this->buildMysqlTableDescription(array('Table','Op','Msg_type','Msg_text'));
		while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	        $res .= "\t<tr>\n";
	        foreach ($line as $col_value) {
				if ($col_value == "OK") {
				    $optimize = " <a href=\"".$sess->adminUrl("adminutil.php?action=optimize&operation=OPTIMIZE")."\">Optimize DB</a>";
				} else {
                    unset($optimize);
                }
                $res .= "<td class=\"".switchBgColor()."\">".$col_value." ".$optimize."</td>";
	        }
	        $res .= "\t</tr>\n";
	    }
		$res .= "</table>\n";
		return $res;
	}

	/**
	* engineMysqlUtils::buildMysqlTableDescription()
	*
	* Stellt Funktion für die Auflistung der einzelnen
	* Tabellen zur Verfügung
	*
	* @param array $name	
	* @access private
	* @return void
	**/
    function buildMysqlTableDescription($name="") {
        $ret = "<table bgcolor=\"#000000\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n<tr>\n<td>\n";
        $ret .= "<table cellpadding=\"3\" cellspacing=\"1\" border=\"0\" width=\"100%\">\n";
    	$ret .= "<tr>\n";
    	if(is_array($name)) {
    		for($i=0;$i<count($name);$i++) {
    			$ret .= "<th>".$name[$i]."</th>\n";
    		}
    	} else {
    		$ret .= "<th>".$name."</th>\n";
    	}
    	$ret .= "</tr>";
        return $ret;	
    }      	

}
?>
