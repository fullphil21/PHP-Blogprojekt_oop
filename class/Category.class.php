<?php
#*******************************************************************************************#
				
				
				#******************************************#
				#********** ENABLE STRICT TYPING **********#
				#******************************************#
				
				/*
					Erklärung zu 'strict types' in Projekt '01a_klassen_und_instanzen'
				*/
				declare(strict_types=1);
				
				
#*******************************************************************************************#


				#************************************#
				#********** CLASS CATEGORY **********#
				#************************************#

				/**
				*
				*	Class represents a Category
				*	
				*
				*/
				class Category {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
				
					private $catID;
					private $catLabel;
					
					
					#***********************************************************#
								
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#
					
					/**
					*
					* CONSTRUCTOR FOR CATEGORY CLASS
					*
					* @param int|null 	$catID 		The category ID (default is null).
 					* @param string|null $catLabel 	The category label (default is null).
					*
					*/
					public function __construct(  $catID=NULL, $catLabel=NULL )	{

if(DEBUG_CC)		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
					
						// Setter nur aufrufen, wenn der jeweilige Parameter keinen Leerstring und nicht NULL enthält
					
						if( $catID 			!== '' 	AND $catID 			!== NULL )		$this->setCatID($catID);
						if( $catLabel 		!== '' 	AND $catLabel 		!== NULL )		$this->setCatLabel($catLabel);

/*
if(DEBUG_CC)		echo "<pre class='debug class value'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_CC)		print_r($this);					
if(DEBUG_CC)		echo "</pre>";	
*/
					}
					
					
					#********** DESTRUCTOR **********#
					
					public function __destruct() {
if(DEBUG_CC)		echo "<p class='debug class'>☠️  <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
					}
	
					#***********************************************************#


				
					#*************************************#
					#********** GETTER & SETTER **********#
					#*************************************#
				
					#********** CAT ID **********#
					public function getCatID() : NULL|int {
						return $this->catID;
					}
					public function setCatID(int|string $value):void {
						
						#********** VALIDATE DATA FORMAT **********#
						if( filter_var($value, FILTER_VALIDATE_INT) === false ) {
							// Fehlerfall (nicht erlaubter Datentyp)
if(DEBUG_C)				echo "<p class='debug class err'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): Der Wert muss inhaltlich einem Integer entsprechen! (<i>" . basename(__FILE__) . "</i>)</p>\n";
							
						} else {
							// Erfolgsfall
							// Datentyp umwandeln
							$this->catID = intval($value);
						}
					}


					#********** CAT LABEL **********#
					public function getCatLabel():NULL|string {
						return $this->catLabel;
					}
					public function setCatLabel(string $value):void {
						$this->catLabel = sanitizeString($value);
					}
					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#

					#********** CHECK IF CATEGORY ALREADY EXITS **********#
					/**
					*
					*	CHECKS IF CATEGORY EXITS IN DB
					*	VIA THE 'CATLABEL' ATTRIBUTE
					*	IF EXITS RETURNED COUNT > 0
					*  
					*
					*	@param	PDO $PDO			DB-Connection object
					*
					*	@return	INTEGER $row	number of existing data rows
					*
					*/
					public function checkIfExists(PDO $PDO) {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
		
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 		= 'SELECT COUNT(catLabel) FROM Category WHERE catLabel = ?';
						
						$params 	= array( $this->getCatLabel() );


						// Schritt 3 DB: Prepared Statements:
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($params);
							
						} catch(PDOException $error) {
if(DEBUG_C)				echo "<p class='debug class db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}

						// Schritt 4 DB: Datenbankoperation auswerten
						$row = $PDOStatement->fetchColumn();
if(DEBUG_V)			echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$row: $row <i>(" . basename(__FILE__) . ")</i></p>";
						
						return $row;
	
					}


					#********** SAVE CATEGORY TO DB **********#
					/**
					*
					*	SAVES CATEGORY-OBJECT DATA TO DB
					*	WRITES LAST INSERT ID INTO CATEGORY-OBJECT
					*	
					*  
					*
					*	@param	PDO $PDO			DB-Connection object
					*
					*	@return	BOOLEAN			true if writing was successful, else false
					*
					*/
					public function saveToDB(PDO $PDO) {
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 		= 'INSERT INTO Category (catLabel) 
										VALUES (?)';
						
						$params 	= array( $this->getCatLabel() );

						// Schritt 3 DB: Prepared Statements:
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($params);
							
						} catch(PDOException $error) {
if(DEBUG_C)				echo "<p class='debug class db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
							$dbError = 'Fehler beim Zugriff auf die Datenbank!';
						}

						$rowCount = $PDOStatement->rowCount();
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
								
						if( $rowCount === 0 ) {
							// Fehlerfall
							return false;
															
						} else {
							// Erfolgsfall
							$this->setCatID( $PDO->lastInsertId() );
							return true;
						}	
					}
				

					#********** FETCH ALL CATEGORY DATA FROM DB **********#
					/**
					*
					*	FETCH ALL CATEGORY DATA FROM DB AND RETURNS ARRAY WITH CATEGORY OBJECTS
					*
					*	@param	PDO $PDO		DB-Connection object
					*
					*	@return	ARRAY			An array containing all categories as category objects
					*
					*/
					public static function fetchAllFromDB(PDO $PDO) {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
												
						$categoriesObjectsArray = array();

						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen
						$sql 		= 'SELECT * FROM Category';
						
						$params 	= NULL;

						// Schritt 3 DB: Prepared Statements
						try {
							// Prepare: SQL-Statement vorbereiten
							$PDOStatement = $PDO->prepare($sql);
							
							// Execute: SQL-Statement ausführen und ggf. Platzhalter füllen
							$PDOStatement->execute($params);
							
						} catch(PDOException $error) {
if(DEBUG_C) 			echo "<p class='debug class db err'><b>Line " . __LINE__ . "</b>: FEHLER: " . $error->GetMessage() . "<i>(" . basename(__FILE__) . ")</i></p>\n";										
						}

						// Schritt 4 DB: Daten weiterverarbeiten
						while( $row = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {

							$categoriesObjectsArray[ $row['catID'] ] = new Category( catID: $row['catID'], catLabel: $row['catLabel']);

						}
/*
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$categoriesObjectsArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($categoriesObjectsArray);					
if(DEBUG_V)			echo "</pre>";
*/					
						return $categoriesObjectsArray;	

					}

						#***********************************************************#
				}
				
#*******************************************************************************************#
?>


















