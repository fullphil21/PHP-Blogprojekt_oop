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



				#********************************#
				#********** CLASS USER **********#
				#********************************#

				/**
				*
				*	Class represents a User
				*	
				*
				*/
				class User {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					private $userID;
					private $userFirstName;
					private $userLastName;
					private $userEmail;
					private $userCity;
					private $userPassword;


					
					#***********************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#
					
					/**
					* Constructor for the User class.
					*
					* @param string|null $userFirstName 	The user's first name (default is null).
					* @param string|null $userLastName 		The user's last name (default is null).
					* @param string|null $userEmail 			The user's email address (default is null).
					* @param string|null $userCity 			The user's city (default is null).
					* @param string|null $userPassword 		The user's password (default is null).
					* @param int|null 	$userID 				The user ID (default is null).
					*/
					public function __construct(  $userFirstName=NULL, $userLastName=NULL,
															$userEmail=NULL, $userCity=NULL,
															$userPassword=NULL, $userID=NULL ) 
					{
if(DEBUG_CC)		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
						


					// Setter nur aufrufen, wenn der jeweilige Parameter keinen Leerstring und nicht NULL enthält
					
					if( $userFirstName 			!== '' 	AND $userFirstName 			!== NULL )		$this->setUserFirstName($userFirstName);
					if( $userLastName 			!== '' 	AND $userLastName 			!== NULL )		$this->setUserLastName($userLastName);
					if( $userEmail 				!== '' 	AND $userEmail 				!== NULL )		$this->setUserEmail($userEmail);
					if( $userCity 					!== '' 	AND $userCity 					!== NULL )		$this->setUserCity($userCity);
					if( $userPassword 			!== '' 	AND $userPassword 			!== NULL )		$this->setUserPassword($userPassword);
					if( $userID 					!== '' 	AND $userID 					!== NULL )		$this->setUserID($userID);

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
				
				
					#********** USER ID **********#
					public function getUserID():NULL|int {
						return $this->userID;
					}
					public function setUserID(int|string $value):void {
						
						#********** VALIDATE DATA FORMAT **********#
						if( filter_var($value, FILTER_VALIDATE_INT) === false ) {
							// Fehlerfall (nicht erlaubter Datentyp)
if(DEBUG_C)				echo "<p class='debug class err'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): Der Wert muss inhaltlich einem Integer entsprechen! (<i>" . basename(__FILE__) . "</i>)</p>\n";
							
						} else {
							// Erfolgsfall
							// Datentyp umwandeln
							$this->userID = intval($value);
						}
					}
					
					#********** USER FIRST NAME **********#
					public function getUserFirstName():NULL|string {
						return $this->userFirstName;
					}
					public function setUserFirstName(string $value):void {
						$this->userFirstName = sanitizeString($value);
					}
					
					#********** USER LAST NAME **********#
					public function getUserLastName():NULL|string {
						return $this->userLastName;
					}
					public function setUserLastName(string $value):void {
						$this->userLastName = sanitizeString($value);
					}

					#********** USER EMAIL **********#
					public function getUserEmail():NULL|string {
						return $this->userEmail;
					}
					public function setUserEmail(string $value):void {
						$this->userEmail = sanitizeString($value);
					}

					#********** USER CITY **********#
					public function getUserCity():NULL|string {
						return $this->userCity;
					}
					public function setUserCity(string $value):void {
						$this->userCity = sanitizeString($value);
					}

					#********** USER PASSWORD **********#
					public function getUserPassword():NULL|string {
						return $this->userPassword;
					}
					public function setUserPassword(string $value):void {
						$this->userPassword = $value;
					}


					#********** VIRTUAL ATTRIBUTES **********#
					public function getFullName():string{
						return $this->getUserFirstName() . ' ' . $this->getUserLastName();
					}

					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#

					#********** FETCH SINGLE CUSTOMER FROM DB **********#
					/**
					*
					*	FETCHES A SINGLE CUSTOMER-DATASET FROM DB
					*	VIA THE USER_EMAIL ATTRIBUTE
					*	IF DATASET WAS RETURNED --> WRITES ALL USER-DATA IN
					*  AN USER-OBJECT
					*
					*	@param	PDO $PDO		DB-Connection object
					*
					*	@return	BOOLEAN		true if dataset was found, else false
					*
					*/
					public function fetchFromDB(PDO $PDO) {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
												
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 		= 'SELECT * FROM User
										WHERE userEmail 	= ?';
														
						$params 	= array( $this->getUserEmail() );
														
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
					
						$resultSet = $PDOStatement->fetch(PDO::FETCH_ASSOC);

/*
if(DEBUG_C)			echo "<pre class='debug class value'><b>Line " . __LINE__ . "</b>: \$resultSet <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_C)			print_r($resultSet);					
if(DEBUG_C)			echo "</pre>";
*/		

						// Wurde ein Datensatz zurückgeliefert?
						if( $resultSet === false ) {
							// Fehlerfall
							return false;
							
						} else {
							// Erfolgsfall
							#********** WRITE DATASET VALUES INTO CALLING OBJECT **********#
							#** USER OBJECT'S VALUES **#
							if( $resultSet['userID'] 				!== '' 	AND $resultSet['userID'] 				!== NULL )		$this->setUserID( $resultSet['userID'] );
							if( $resultSet['userFirstName'] 		!== '' 	AND $resultSet['userFirstName'] 		!== NULL )		$this->setUserFirstName( $resultSet['userFirstName'] );
							if( $resultSet['userLastName'] 		!== '' 	AND $resultSet['userLastName']	 	!== NULL )		$this->setUserLastName( $resultSet['userLastName'] );
							if( $resultSet['userEmail'] 			!== '' 	AND $resultSet['userEmail'] 			!== NULL )		$this->setUserEmail( $resultSet['userEmail'] );
							if( $resultSet['userCity'] 			!== '' 	AND $resultSet['userCity'] 			!== NULL )		$this->setUserCity( $resultSet['userCity'] );
							if( $resultSet['userPassword']		!== '' 	AND $resultSet['userPassword'] 		!== NULL )		$this->setUserPassword( $resultSet['userPassword'] );

/*
if(DEBUG_C)				echo "<pre class='debug class value'><b>Line " . __LINE__ . "</b>: \$this <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_C)				print_r($this);					
if(DEBUG_C)				echo "</pre>";
*/				
							return true;

						}

					}


					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#
?>


















