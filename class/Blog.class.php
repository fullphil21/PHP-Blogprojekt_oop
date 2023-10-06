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
				#********** CLASS BLOG **********#
				#********************************#

				/**
				* 
				* CONSTRUCTOR FOR THE BLOG CLASS
				*
				* @param Category 	$category	 			The category of the blog (default is a new Category instance).
				* @param User 			$user 					The user associated with the blog (default is a new User instance).
				* @param string|null	$blogHeadline 			The headline of the blog (default is null).
				* @param string|null $blogImagePath 		The image path for the blog (default is null).
				* @param string|null $blogImageAlignment 	The alignment of the blog image (default is null).
				* @param string|null $blogContent 			The content of the blog (default is null).
				* @param string|null $blogDate 				The date of the blog (default is null).
				* @param int|null 	$blogID 					The ID of the blog (default is null).
				*/
				class Blog {
					
					#*******************************#
					#********** ATTRIBUTE **********#
					#*******************************#
					
					private $blogID;
					private $blogHeadline;
					private $blogImagePath;
					private $blogImageAlignment;
					private $blogContent;
					private $blogDate;

					// $category ist ein eingebettetes Objekt
					private $category;
					// $user ist ein eingebettetes Objekt
					private $user;

					
					#***********************************************************#
					
					
					#*********************************#
					#********** CONSTRUCTOR **********#
					#*********************************#
	
					/**
					*
					*	Constructor for Blog Class
					*
					*/
					public function __construct(	$category=new Category(),
								 							$user=new User(),
															$blogHeadline=NULL, $blogImagePath=NULL, $blogImageAlignment=NULL, 
															$blogContent=NULL, $blogDate=NULL, $blogID=NULL	) 		
					{

if(DEBUG_CC)		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						
							
						$this->setCategory($category);
						$this->setUser($user);
						
						// Setter nur aufrufen, wenn der jeweilige Parameter keinen Leerstring und nicht NULL enthält
						if( $blogHeadline 		!== '' 	AND $blogHeadline			!== NULL )		$this->setBlogHeadline($blogHeadline);
						if( $blogImagePath 		!== '' 	AND $blogImagePath 		!== NULL )		$this->setBlogImagePath($blogImagePath);
						if( $blogImageAlignment !== '' 	AND $blogImageAlignment !== NULL )		$this->setBlogImageAlignment($blogImageAlignment);
						if( $blogContent 			!== '' 	AND $blogContent 			!== NULL )		$this->setBlogContent($blogContent);
						if( $blogDate 				!== '' 	AND $blogDate 				!== NULL )		$this->setBlogDate($blogDate);
						if( $blogID					!== '' 	AND $blogID					!== NULL )		$this->setBlogID($blogID);

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
				
					#********** BLOG ID **********#
					public function getBlogID():NULL|int {
						return $this->blogID;
					}
					public function setBlogID(int|string $value):void {
						
						#********** VALIDATE DATA FORMAT **********#
						if( filter_var($value, FILTER_VALIDATE_INT) === false ) {
							// Fehlerfall (nicht erlaubter Datentyp)
if(DEBUG_C)				echo "<p class='debug class err'><b>Line " . __LINE__ .  "</b> | " . __METHOD__ . "(): Der Wert muss inhaltlich einem Integer entsprechen! (<i>" . basename(__FILE__) . "</i>)</p>\n";
						
						} else {
							// Erfolgsfall
							// Datentyp umwandeln
							$this->blogID = intval($value);
						}
					}
					
					#********** BLOG HEADLINE **********#
					public function getBlogHeadline():NULL|string {
						return $this->blogHeadline;
					}
					public function setBlogHeadline(string $value):void {
						$this->blogHeadline = sanitizeString($value);
					}
					
					#********** BLOG IMAGE PATH **********#
					public function getBlogImagePath():NULL|string {
						return $this->blogImagePath;
					}
					public function setBlogImagePath(string $value):void {
						$this->blogImagePath = sanitizeString($value);
					}

					#********** BLOG IMAGE ALIGNMENT **********#
					public function getBlogImageAlignment():NULL|string {
						return $this->blogImageAlignment;
					}
					public function setBlogImageAlignment(string $value):void {
						$this->blogImageAlignment = sanitizeString($value);
					}

					#********** BLOG IMAGE Content **********#
					public function getBlogContent():NULL|string {
						return $this->blogContent;
					}
					public function setBlogContent(string $value):void {
						$this->blogContent = sanitizeString($value);
					}

					#********** BLOG DATE **********#
					public function getBlogDate():NULL|string {
						return $this->blogDate;
					}
					public function setBlogDate(string $value):void {
						$this->blogDate = sanitizeString($value);
					}
				
					#********** CATEGORY **********#
					public function getCategory():Category {
						return $this->category;
					}
					public function setCategory(Category $value) {
						$this->category = $value;
					}

					#********** USER **********#
					public function getUser():User {
						return $this->user;
					}
					public function setUser(User $value) {
						$this->user = $value;
					}

					
					#***********************************************************#
					

					#******************************#
					#********** METHODEN **********#
					#******************************#

					
					#********** SAVE BLOG DATA TO DB **********#
					/**
					*
					*	SAVES BLOG-OBJECT DATA TO DB
					*	WRITES LAST INSERT ID INTO BLOG-OBJECT
					*	
					*  
					*
					*	@param	PDO $PDO			DB-Connection object
					*
					*	@return	BOOLEAN			true if writing was successful, else false
					*
					*/
					public function saveToDB(PDO $PDO) {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";
							
						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 	=	'	INSERT INTO Blog
										(blogHeadline, blogImagePath, blogImageAlignment, blogContent, catID, userID)
										VALUES
										(?, ?, ?, ?, ?, ?)';
			
						$params 	= array(	$this->getBlogHeadline(),
												$this->getBlogImagePath(),
												$this->getBlogImageAlignment(),
												$this->getBlogContent(),
												$this->getCategory()->getCatID(),
												$this->getUser()->getUserID() );
												
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
						$rowCount = $PDOStatement->rowCount();
if(DEBUG_V)			echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$rowCount: $rowCount <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						if( $rowCount === 0 ) {
							// Fehlerfall
							return false;
															
						} else {
							// Erfolgsfall
							$this->setBlogID( $PDO->lastInsertId() );
							return true;
						}	

					}


					#********** FETCH ALL BLOG DATA FROM DB **********#
					/**
					*
					*	FETCH ALL BLOG DATA FROM DB AND RETURNS ARRAY WITH BLOG-OBJECTS
					*
					*	@param	PDO $PDO		DB-Connection object
					*
					*	@return	ARRAY			An array containing all Blogs as Blog-objects
					*
					*/
					public static function fetchAllFromDb(PDO $PDO) {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";

						$blogsObjectsArray = array();

						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 		= 'SELECT * FROM Blog 
										INNER JOIN User USING(userID)
										INNER JOIN Category USING(catID)
										ORDER BY blogDate DESC';
						
						$params 	= array();
						
						
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

						// Schritt 4 DB: Daten weiterverarbeiten
						while( $row = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {

							// 	// $category=new Category(),
							// 	// $user=new User(),
							// 	// $blogHeadline=NULL, $blogImagePath=NULL, $blogImageAlignment=NULL, 
							// 	// $blogContent=NULL, $blogDate=NULL, $blogID=NULL

							$blogsObjectsArray[ $row['blogID'] ] = new Blog(	new Category( 	catID: $row['catID'], catLabel: $row['catLabel']	),	
																						 		new User( 		userID: $row['userID'], userFirstName: $row['userFirstName'],
																													userLastName: $row['userLastName'], userEmail: $row['userEmail'],
																													userCity: $row['userCity']	),
																								blogID: $row['blogID'], blogHeadline: $row['blogHeadline'], 
																								blogImagePath:	$row['blogImagePath'], blogImageAlignment: $row['blogImageAlignment'],
																								blogContent: $row['blogContent'],  blogDate: $row['blogDate'] 	);																																														
																					
						}
/*
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogsObjectsArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($blogsObjectsArray);					
if(DEBUG_V)			echo "</pre>";
*/			
						return $blogsObjectsArray;	
					}



					#********** FETCH BLOG DATA FROM DB BY CATEGORY **********#
					/**
					*
					*	FETCH BLOG DATA FROM DB FILTERED BY CATEGORY AND RETURNS ARRAY WITH BLOG-OBJECTS
					*
					*	@param	PDO $PDO		DB-Connection object
					*
					*	@return	ARRAY			An array containing Blogs as Blog-objects filtered by CATEGORY
					*
					*/
					public static function fetchFromDBByCategory(PDO $PDO, int $filterID) {
if(DEBUG_C)			echo "<p class='debug class'>🌀 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "() (<i>" . basename(__FILE__) . "</i>)</p>\n";

						$blogsObjectsArray = array();

						// Schritt 2 DB: SQL-Statement und Placeholder-Array erstellen:
						$sql 		= 'SELECT * FROM Blog 
										INNER JOIN User USING(userID)
										INNER JOIN Category USING(catID)
										WHERE catID = ?
										ORDER BY blogDate DESC';
						
						$params 	= array( $filterID );
						
						
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

						// Schritt 4 DB: Daten weiterverarbeiten
						while( $row = $PDOStatement->fetch(PDO::FETCH_ASSOC) ) {

							// 	// $category=new Category(),
							// 	// $user=new User(),
							// 	// $blogHeadline=NULL, $blogImagePath=NULL, $blogImageAlignment=NULL, 
							// 	// $blogContent=NULL, $blogDate=NULL, $blogID=NULL
	
							$blogsObjectsArray[ $row['blogID'] ] = new Blog(	new Category( 	catID: $row['catID'], catLabel: $row['catLabel']	),	
																								new User( 		userID: $row['userID'], userFirstName: $row['userFirstName'],
																													userLastName: $row['userLastName'], userEmail: $row['userEmail'],
																													userCity: $row['userCity'], userPassword: $row['userPassword']	),
																								blogID: $row['blogID'], blogHeadline: $row['blogHeadline'], 
																								blogImagePath:	$row['blogImagePath'], blogImageAlignment: $row['blogImageAlignment'],
																								blogContent: $row['blogContent'],  blogDate: $row['blogDate'] 	);																																														
																						
							}
	/*
	if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blogsObjectsArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
	if(DEBUG_V)			print_r($blogsObjectsArray);					
	if(DEBUG_V)			echo "</pre>";
	*/			
							return $blogsObjectsArray;	

					}

					#***********************************************************#
					
				}
				
				
#*******************************************************************************************#
?>


















