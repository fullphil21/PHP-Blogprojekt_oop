<?php
#***************************************************************************************#


				#****************************************#
				#********** PAGE CONFIGURATION **********#
				#****************************************#
				
				require_once('./include/config.inc.php');
				require_once('./include/db.inc.php');
				require_once('./include/form.inc.php');
				include_once('./include/dateTime.inc.php');

				#********** INCLUDE CLASSES **********#
				require_once('class/Blog.class.php');
				require_once('class/Category.class.php');
				require_once('class/User.class.php');


#***************************************************************************************#


				#**************************************#
				#********** OUTPUT BUFFERING **********#
				#**************************************#
				
				if( ob_start() === false ) {
					// Fehlerfall
if(DEBUG)		echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten des Output Bufferings! <i>(" . basename(__FILE__) . ")</i></p>\r\n";				
					
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Output Buffering erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\r\n";									
				}


#*******************************************************************************************#


				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#

				$loginError 			= NULL;
				$categoryFilterID		= NULL;

#***************************************************************************************#


				#*******************************************#
				#********** CHECK FOR LOGIN STATE **********#
				#*******************************************#
				
				#********** START|CONTINUE SESSION	**********#			
				session_name("wwwblogde_oop");
				session_start();
	
				
				#********** USER IS NOT LOGGED IN **********#
				if( isset($_SESSION['user']) === false ) {
if(DEBUG)		echo "<p class='debug auth'><b>Line " . __LINE__ . "</b>:1 User ist nicht eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";

					// delete empty session
					session_destroy();
					
					// set Flag
					$login = false;
				
				
				#********** USER IS LOGGED IN **********#
				} else {
if(DEBUG)		echo "<p class='debug auth'><b>Line " . __LINE__ . "</b>: User ist eingeloggt. <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					// set Flag
					$login = true;
				
				} // CHECK FOR LOGIN STATE END			

#***************************************************************************************#
/*
				#*************************************#
				#********** TESTING CLASSES **********#
				#*************************************#

				#********** CLASS STATE CONSTRUCTOR & SETTERS **********#
				$category = new Category();

				// $catID=NULL, $catLabel=NULL
				$category = new Category( 1, 'Lifestyle' );


				#********** CLASS USER CONSTRUCTOR & SETTERS **********#
				$user = new User();
				
				// $userFirstName=NULL, $userLastName=NULL,
				// $userEmail=NULL, $userCity=NULL,
				// $userPassword=NULL, $userID=NULL 

				$user = new User(	'Thomas', 'Schneider', 'tomSchn@mail.de', 'Berlin',
										'test123', 1	);



				#********** CLASS BLOG CONSTRUCTOR & SETTERS **********#
				$blog = new Blog();
				
				// $category=new Category(),
				// $user=new User(),
				// $blogHeadline=NULL, $blogImagePath=NULL, $blogImageAlignment=NULL, 
				// $blogContent=NULL, $blogDate=NULL, $blogID=NULL

				$blog = new Blog(	$category,
										$user, 
										'Eine Testüberschrift', './uploads/images/...', 'right',
										'Dies ist ein schöner Test-Text', 
										'1968-05-22', 1
										);

*/
#***************************************************************************************#

				#****************************************#
				#********** PROCESS FORM LOGIN **********#
				#****************************************#

				#********** PREVIEW POST ARRAY **********#
/*
if(DEBUG_V) echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_POST <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($_POST);					
if(DEBUG_V)	echo "</pre>";
*/


				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formLogin']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'Login' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
						
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** GENERATE NEW USER OBJECT **********#
					// $userFirstName=NULL, $userLastName=NULL,
					// $userEmail=NULL, $userCity=NULL,
					// $userPassword=NULL, $userID=NULL

					$user 		= new User( userEmail:$_POST['f1'] );

					$password 	= sanitizeString( $_POST['f2'] );
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$password: $password <i>(" . basename(__FILE__) . ")</i></p>\n";

					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					/*
						[x] Validieren der Formularwerte (Feldprüfungen)
						[ ] Vorbelegung der Formularfelder für den Fehlerfall 
						[x] Abschließende Prüfung, ob das Formular insgesamt fehlerfrei ist
					*/
					$errorUserEmail 	= validateEmail($user->getUserEmail());
					$errorPassword		= validateInputString($password, minLength:4);
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorUserEmail: $errorUserEmail <i>(" . basename(__FILE__) . ")</i></p>\n";
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorPassword: $errorPassword <i>(" . basename(__FILE__) . ")</i></p>\n";

					
					#********** FINAL FORM VALIDATION (FIELDS VALIDATION) **********#
					if( $errorUserEmail !== NULL OR $errorPassword !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						// NEUTRALE Fehlermeldung an den User
						$loginError = 'Diese Logindaten sind ungültig!';
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						// Schritt 4 FORM: Daten weiterverarbeiten
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Daten werden weiterverarbeitet... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						
						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect(DB_NAME);

						#********** FETCH USER DATA FROM DATABASE BY EMAIL **********#
						if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lese Userdaten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
						$isEmailValid = $user->fetchFromDB($PDO);

						#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($PDO);
	
						#********** 1. VALIDATE EMAIL **********#
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Email-Adresse... <i>(" . basename(__FILE__) . ")</i></p>\n";
	
						if( $isEmailValid === false ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '{$user->getUserEmail()}' wurde nicht in der DB gefunden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							// NEUTRALE Fehlermeldung an den User
							$loginError = 'Diese Logindaten sind ungültig!';							
							
						} else {
							// Erfolgfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Die Email-Adresse '{$user->getUserEmail()}' wurde in der DB gefunden. <i>(" . basename(__FILE__) . ")</i></p>\n";				

							#********** 2. VALIDATE PASSWORD **********#
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Validiere Passwort... <i>(" . basename(__FILE__) . ")</i></p>\n";
							

							if( password_verify($password, $user->getUserPassword()) === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt NICHT mit dem Passwort aus der DB überein! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
								// NEUTRALE Fehlermeldung an den User
								$loginError = 'Diese Logindaten sind ungültig';
							
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Das Passwort aus dem Formular stimmt mit dem Passwort aus der DB überein. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						

								#********** START SESSION **********#
								if( session_start() === false ) {
									// Fehlerfall
if(DEBUG)						echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									$loginError = 'Der Loginvorgang konnte nicht durchgeführt werden!<br>
														Bitte überprüfen Sie die Sicherheitseinstellungen Ihres<br>
														Browsers und aktivieren Sie die Annahme von Cookies für diese Seite.';
								
									
								} else {
									// Erfolgsfall
if(DEBUG)						echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
										
									#********** SAVE USER DATA INTO SESSION **********#
if(DEBUG)						echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Schreibe Userdaten in Session... <i>(" . basename(__FILE__) . ")</i></p>";
									
									$_SESSION['IPAddress'] 			= $_SERVER['REMOTE_ADDR'];
									
									$_SESSION['user']					= $user;
								

if(DEBUG_V)						echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)						print_r($_SESSION);					
if(DEBUG_V)						echo "</pre>";	

									#********** REDIRECT TO DASHBOARD **********#								
									header('Location: dashboard.php');


								} // START SESSION END

							} // 2. VALIDATE PASSWORD END

						} // 1. VALIDATE EMAIL END

					} // FINAL FORM VALIDATION (FIELDS VALIDATION) END

				} // PROCESS FORM LOGIN END
			
#***************************************************************************************#


				#********************************************#
				#********** PROCESS URL PARAMETERS **********#
				#********************************************#
				
				// Schritt 1 URL: Prüfen, ob Parameter übergeben wurde
				if( isset($_GET['action']) ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: URL-Parameter 'action' wurde übergeben... <i>(" . basename(__FILE__) . ")</i></p>";	
			
			
					// Schritt 2 URL: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$action = sanitizeString($_GET['action']);
if(DEBUG_V)		echo "<p class='debug value'>Line <b>" . __LINE__ . "</b>: \$action = $action <i>(" . basename(__FILE__) . ")</i></p>";
		
							
					// Schritt 3 URL: ggf. Verzweigung
					#********** LOGOUT **********#
					if( $_GET['action'] === 'logout' ) {
if(DEBUG)			echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: 'Logout' wird durchgeführt... <i>(" . basename(__FILE__) . ")</i></p>";	
												
						session_destroy();
						header("Location: index.php");
						exit();

					} // LOGOUT BRANCH END

					#*********** CATEGORY FILTER **********#
					if( $action === 'filterByCategory' ) {
if(DEBUG)			echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Kategorie Filter wird gestartet... <i>(" . basename(__FILE__) . ")</i></p>\n";
								
						#*********** CAT ID **********#
						// Schritt 1 URL: Prüfen, ob URL-Parameter übergeben wurde
						if( isset($_GET['catID']) === true ) {
if(DEBUG)				echo "<p class='debug'>🧻 <b>Line " . __LINE__ . "</b>: URL-Parameter 'catID' wurde übergeben. <i>(" . basename(__FILE__) . ")</i></p>\n";										
	
							// Schritt 2 URL: Auslesen, entschärfen und Debug-Ausgabe der übergebenen Parameter-Werte
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
						
							$categoryFilterID = sanitizeString( $_GET['catID'] );
if(DEBUG_V)				echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$categoryFilterID: $categoryFilterID <i>(" . basename(__FILE__) . ")</i></p>\n";

						} // CAT ID END

					} // CATEGORY FILTER BRANCH END

				} // PROCESS URL PARAMETERS END
															

#***************************************************************************************#

				#***************************************************#
				#**********  FETCH BLOG DATA FROM DB ***************#
				#**************************************************#

				// Schritt 1 DB: Verbindung zur Datenbank aufbauen:
				$PDO = dbConnect(DB_NAME);

				#*********** CHECK IF CATEGORY FILTER IS ACTIVE **********#
				if( $categoryFilterID == NULL ) { 
				// Lade alle Blog Daten aus DB
if(DEBUG)	echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Lade alle Blog Daten aus DB aus... <i>(" . basename(__FILE__) . ")</i></p>\n";

				$allBlogsObjectsArray = Blog::fetchAllFromDB($PDO);

				} else {
					// Lade gefilterte Blog Daten aus DB
if(DEBUG)		echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Filtere Blog-Einträge nach Kategorie-ID$categoryFilterID... <i>(" . basename(__FILE__) . ")</i></p>";					

					$allBlogsObjectsArray = Blog::fetchFromDBByCategory($PDO, $categoryFilterID);

				} // CHECK IF CATEGORY FILTER IS ACTIVE END

				#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB) echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO);

#***************************************************************************************#

				#**********************************************#
				#********** FETCH CATEGORIES FROM DB **********#
				#**********************************************#
				
				// Schritt 1 DB: DB-Verbindung herstellen
				$PDO = dbConnect(DB_NAME);
				
				// Lade Kategorien aus DB
if(DEBUG)	echo "<p class='debug'>📑 Line <b>" . __LINE__ . "</b>: Lade Kategorien aus DB... <i>(" . basename(__FILE__) . ")</i></p>";	
				
				$allCategoriesObjectsArray = Category::fetchAllFromDB($PDO);

				#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB) echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
				unset($PDO);

/*
if(DEBUG_V)	echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$allCategoriesObjectsArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)	print_r($allCategoriesObjectsArray);					
if(DEBUG_V)	echo "</pre>";
*/
#***************************************************************************************#	


?>

<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>PHP-Projekt Blog</title>
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">

		<style>
			main {
				width: 60%;
			}
			aside {
				width: 30%;
				overflow: hidden;
			}
		</style>


	</head>

	<body>
		
		<!-- ---------- PAGE HEADER START ---------- -->
		<header class="fright">
			
			<?php if( $login === false ): ?>
				<?php if($loginError): ?>
				<p class="error"><b><?= $loginError ?></b></p>
				<?php endif ?>
				
				<!-- -------- Login Form START -------- -->
				<form action="" method="POST">
					<input type="hidden" name="formLogin">
					<input type="text" name="f1" placeholder="Email">
					<input type="password" name="f2" placeholder="Password">
					<input type="submit" value="Login">
				</form>
				<!-- -------- Login Form END -------- -->
				
			<?php else: ?>
				<!-- -------- PAGE LINKS START -------- -->
				<a href="?action=logout">Logout</a><br>
				<a href='dashboard.php'>zum Dashboard >></a>
				<!-- -------- PAGE LINKS END -------- -->
			<?php endif ?>
		
		</header>
		
		<div class="clearer"></div>
				
		<br>
		<hr>
		<br>		
		<!-- ---------- PAGE HEADER END ---------- -->
		
		
		
		<h1>PHP-Projekt Blog</h1>
		<p><a href='index.php'>:: Alle Einträge anzeigen ::</a></p>
		
		
		
		<!-- ---------- BLOG ENTRIES START ---------- -->		
		<main class="blogs fleft">
			
			<?php if( count($allBlogsObjectsArray) === 0 ): ?>
				<p class="info">Noch keine Blogeinträge vorhanden.</p>
			
			<?php else: ?>
			
				<?php foreach( $allBlogsObjectsArray AS $singleBlogObject ): ?>
					<?php $dateTimeArray = isoToEuDateTime($singleBlogObject->getBlogDate()) ?>
					
					<article class='blogEntry'>
					
						<a name='entry<?= $singleBlogObject->getBlogID() ?>'></a>
						
						<p class='fright'><a href='?action=filterByCategory&catID=<?= $singleBlogObject->getCategory()->getCatID() ?>'>Kategorie: <?= $singleBlogObject->getCategory()->getCatLabel() ?></a></p>
						<div class='clearer'></div>
						<h2><?= $singleBlogObject->getBlogHeadline() ?></h2>

						<p class='blogUserInfo'><?= $singleBlogObject->getUser()->getFullName() ?> (<?= $singleBlogObject->getUser()->getUserCity() ?>) schrieb am <?= $dateTimeArray['date'] ?> um <?= $dateTimeArray['time'] ?> Uhr:</p>
						
						<p class='blogContent'>
						
							<?php if($singleBlogObject->getBlogImagePath()): ?>
								<img class='<?= $singleBlogObject->getBlogImageAlignment() ?>' src='<?= $singleBlogObject->getBlogImagePath() ?>' alt='' title=''>
							<?php endif ?>
							
							<?= nl2br( $singleBlogObject->getBlogContent() ) ?>
						</p>
						
						<div class='clearer'></div>
						
						<br>
						<hr>
						
					</article>
					
				<?php endforeach ?>
			<?php endif ?>
			
		</main>		
		<!-- ---------- BLOG ENTRIES END ---------- -->
		
		
		
		<!-- ---------- CATEGORY FILTER LINKS START ---------- -->		
		<aside class="categories fright">

			<?php if( count($allCategoriesObjectsArray) === 0 ): ?>
				<p class="info">Noch keine Kategorien vorhanden.</p>
			
			<?php else: ?>
			
				<?php foreach( $allCategoriesObjectsArray AS $singleCategoryObject ): ?>
					<p><a href="?action=filterByCategory&catID=<?= $singleCategoryObject->getCatID()?>" <?php if( $singleCategoryObject->getCatID() == $categoryFilterID ) echo 'class="active"' ?>><?= $singleCategoryObject->getCatLabel() ?></a></p>
				<?php endforeach ?>

			<?php endif ?>
		</aside>

		<div class="clearer"></div>
		<!-- ---------- CATEGORY FILTER LINKS END ---------- -->
		
	</body>

</html>
