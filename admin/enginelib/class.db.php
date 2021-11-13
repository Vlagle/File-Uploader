<?php
// +----------------------------------------------------------------------+
// | EngineLib - Database Class                                           |
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
// $Id: class.db.php 6 2005-10-08 10:12:03Z alex $

/**
* class db_sql
* 
* Basisklasse der Engines wird in allen Engines ben&ouml;tigt um
* eine Verbindung zur MySQL DB herzustellen
* 
* @access public
* @author Alex Höntschel <info@alexscriptengine.de>
* @version $Id: class.db.php 6 2005-10-08 10:12:03Z alex $
* @copyright Alexscriptengine 2002,2003
* @link http://www.alexscriptengine.de
*/

    class db_sql {
        
	var $database = "";
  	var $server   = "";
  	var $user     = "";
  	var $password = "";
	var $link_id  = 0;
	var $query_id = 0;
	var $q_cache = array();
    
        function db_sql($dbName,$hostname,$dbUname,$dbPasswort) {
            $this->database = $dbName;
            $this->server = $hostname;
            $this->user = $dbUname;
            $this->password = $dbPasswort;
            $this->myconnect();            
        
        }

		/**
		 * db_sql::myconnect()
		 * 
		 * Konstruktor:
		 * $db_sql = new db_sql;
		 * $db_sql->database=$db;
		 * $db_sql->server=$sql_host;
		 * $db_sql->user=$sql_user;
		 * $db_sql->password=$sql_pass;
		 * $db_sql->myconnect();		 * 
		 * @return link_id
		 */
		function myconnect()
		{
			$this->link_id = @MYSQL_CONNECT($this->server, $this->user, $this->password);
				 	if (!$this->link_id)
						die( "Keine Verbindung zum Datenbankserver möglich!" );
			$db_select = @mysql_select_db($this->database,$this->link_id);
				 	if (!$db_select)
						die( "Die Datenbank konnte nicht ausgewählt werden: ".$this->server.",".$this->user.",".$this->password.",".$this->database." ".mysql_error() );
			return $this->link_id;
		}

		
		/**
		 * db_sql::sql_query()
		 * Normale SQL-Query Abfrage, kann für Insert, Update, Delete etc. verwendet werden
		 * Verwendung:
		 * $db_sql->sql_query("SELECT * FROM $user_table WHERE gender='1'");
		 * oder
		 * $db_sql->sql_query("INSER INTO $user_table VALUES('1','TEST')");
		 * 
		 * @param $query_statement
		 * @return query_id
		 */
		function sql_query($query_statement) {
    		global $query_count;
    		$this->query_id = mysql_query($query_statement,$this->link_id);
    		if(!$this->query_id)
                trigger_error("Query fatal error:<br><b>Query:</b>".$query_statement."<br><b>Fehlermeldung:</b>  ".mysql_error(),E_USER_ERROR); 
    		$query_count++;	
    		$this->test['q_cache'][] = $query_statement;	
    		return $this->query_id;		
		}
		

		/**
		 * db_sql::query_array()
		 * Führt eine Query - Abfrage aus und packt das Ergebnis in ein assoziatives Array
		 * Verwendung: 
		 * $config = $db_sql->query_array("SELECT * from $set_table WHERE styleid='1'");
		 * anschliessend kann mit dem Array normal weitergearbeitet werden
		 * 
		 * @param $query_statement
		 * @return gibt Array zurück
		 */
		function query_array($query_statement) {
    		$query_id = $this->sql_query($query_statement);
    		$return_array = $this->fetch_array($query_id);
    
    		$this->free_result($query_id);
    		return $return_array;	
		}
		
		
		/**
		 * db_sql::fetch_array()
		 * Normale Fetch_Array muss in Verbindung mit der normalen query - Abfrage verwendet werden
		 * Beispielsweise, wenn alles in eine Schleife läuft
		 * Verwendung:
		 * $result = $db_sql->sql_query("SELECT * from $user_table");
		 * while($User = $db_sql->fetch_array($result)) {
		 * echo "<br>".$User[username]."<br>";
		 * }
		 * 
		 * @param $query_id
		 * @return gibt Array zurück
		 */
		function fetch_array($query_id=-1) {
    		if ($query_id!=-1) {
    			$this->query_id = $query_id;
			}
    				
    		$this->result = mysql_fetch_array($this->query_id);
    		return $this->result;	
		}
	
		
		/**
		 * db_sql::insert_id()
		 * Gibt die zuletzt eingefügte auto_increment Zeile der Datenbank zurück
		 * 
		 * @return 
		 */
		function insert_id() {
    		return mysql_insert_id($this->link_id);
		}
		
		
		/**
		 * db_sql::sql_fetch_row()
		 * Liefert den nächsten Datensatz als skalares Array
		 * Verwendung:
		 * list ($newsid) = $db_sql->sql_fetch_row("SELECT newsid FROM $news_table WHERE catid='2'");
		 * 
		 * @param $query_statement
		 * @return 
		 */
		function sql_fetch_row($query_statement) {
    		$this->result = mysql_fetch_row($this->sql_query($query_statement));
    		return $this->result;		 
		}
        
        
		/**
		 * db_sql::fetch_row()
		 * Liefert den nächsten Datensatz als skalares Array, ohne Query vorher aufzurufen
		 * 
		 * @param $result_set
		 * @return 
		 */        
		function fetch_row($result_set) {
    		$this->result = mysql_fetch_row($result_set);
    		return $this->result;		 
		}
        
		
		
		/**
		 * db_sql::num_rows()
		 * Zählt die Reihen in einer Tabelle
		 * Verwendung:
		 * $result2 = $db_sql->sql_query("SELECT * from $user_table WHERE gender='1'");
		 * $row = $db_sql->num_rows($result2);
		 * 
		 * @param $query_id
		 * @return 
		 */
		function num_rows($query_id=-1) {
    		if ($query_id!=-1) {
    			$this->query_id = $query_id;
            }
    		return mysql_num_rows($this->query_id);
		}

		
		/**
		 * db_sql::free_result()
		 * Gibt MySQL speicher frei
		 * 
		 * @param $query_id
		 * @return 
		 */
		function free_result($query_id=-1) {
    		if ($query_id!=-1) {
    			$this->query_id=$query_id;
            }
    		return @mysql_free_result($this->query_id);
		}
				
		
 		/**
 		 * db_sql::closeSQL()
		 * Schliesst DB-Verbindung
 		 * 
 		 * @return 
 		 */
 		function closeSQL() {
    		@mysql_close($this->link_id);
		}
		
    } 
		 
		 
?>