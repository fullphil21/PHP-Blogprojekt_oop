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


				#****************************************#
				#********** SECURE PAGE ACCESS **********#
				#****************************************#				

				session_name('wwwblogde_oop');
				
				
				#********** START|CONTINUE SESSION	**********#
				if( session_start() === false ) {
					// Fehlerfall
if(DEBUG) 		echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: FEHLER beim Starten der Session! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
				} else {
					// Erfolgsfall
if(DEBUG)		echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Session erfolgreich gestartet. <i>(" . basename(__FILE__) . ")</i></p>\n";				
		
/*
if(DEBUG)		echo "<pre class='debug auth value'>Line <b>" . __LINE__ . "</b>: \$_SESSION <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG)		print_r($_SESSION);					
if(DEBUG)		echo "</pre>";
*/

					#*******************************************#
					#********** CHECK FOR VALID LOGIN **********#
					#*******************************************#					

					#********** A) NO VALID LOGIN **********#
					if( isset($_SESSION['user']) === false OR $_SESSION['IPAddress'] !== $_SERVER['REMOTE_ADDR'] ) {
						// Fehlerfall (User ist nicht eingeloggt)
if(DEBUG)			echo "<p class='debug auth err'><b>Line " . __LINE__ . "</b>: Login konnte nicht validiert werden! <i>(" . basename(__FILE__) . ")</i></p>\n";				
							
							
						#********** DENY PAGE ACCESS **********#
						// 1. Session löschen
						session_destroy();
						
						// 2. User auf öffentliche Seite umleiten
						header('LOCATION: index.php');
						
						// 3. Fallback, falls die Umleitung per HTTP-Header ausgehebelt werden sollte
						exit();

					#********** B) VALID LOGIN **********#
					} else {
						// Erfolgsfall (User ist eingeloggt)
if(DEBUG)			echo "<p class='debug auth ok'><b>Line " . __LINE__ . "</b>: Login wurde erfolgreich validiert. <i>(" . basename(__FILE__) . ")</i></p>\n";				

						session_regenerate_id(true);						
					
						// fetch user data from session
						$user =  $_SESSION['user'];
/*
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$user <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($user);					
if(DEBUG_V)			echo "</pre>";
*/			
					
					} // CHECK FOR VALID LOGIN END

				} // SECURE PAGE ACCESS END

#***************************************************************************************#	

			
				#******************************************#
				#********** INITIALIZE VARIABLES **********#
				#******************************************#
				
				$category				= NULL;
				$blog						= NULL;	
				
				$errorCatLabel			= NULL;
				$errorHeadline 		= NULL;
				$errorImageUpload 	= NULL;
				$errorContent 			= NULL;
				
				$dbError					= NULL;
				$dbSuccess				= NULL;


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

					}
											
				} // PROCESS URL PARAMETERS END
															
									
#***************************************************************************************#


				#*************************************************#
				#********** PROCESS FORM 'NEW CATEGORY' **********#
				#*************************************************#

				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewCategory']) === true ) {
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Category' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
	
			
					// Schritt 2 FORM: Werte auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";
					
					// $catID=NULL, $catLabel=NULL 
					$category = new Category(catLabel:$_POST['f6']);
/*
if(DEBUG_V)		echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$category <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)		print_r($category);					
if(DEBUG_V)		echo "</pre>";
*/				

					// Schritt 3 FORM: Werte ggf. validieren
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					$errorCatLabel = validateInputString($category->getCatLabel());
if(DEBUG_V)		echo "<p class='debug value'><b>Line " . __LINE__ . "</b>: \$errorCatLabel: $errorCatLabel <i>(" . basename(__FILE__) . ")</i></p>\n";					
					
					#********** FINAL FORM VALIDATION **********#
					if( $errorCatLabel !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";						
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";						
						
						// Schritt 4 FORM: Daten weiterverarbeiten

						#***********************************#
						#********** DB OPERATIONS **********#
						#***********************************#
						
						// Schritt 1 DB: DB-Verbindung herstellen
						$PDO = dbConnect(DB_NAME);
						
						#********** CHECK IF CATEGORY NAME ALREADY EXISTS **********#
if(DEBUG)			echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Prüfe ob Kategorie in DB existiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
						$categoryExists = $category->checkIfExists($PDO);

						#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)		echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
						unset($PDO);

						if( $categoryExists !== 0) {
							// Fehlerfall
							echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: Kategorie <b>'{$category->getCatLabel()}'</b> existiert bereits! <i>(" . basename(__FILE__) . ")</i></p>";
							$errorCatLabel = 'Es existiert bereits eine Kategorie mit diesem Namen!'; 
						
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug'>Line <b>" . __LINE__ . "</b>: Neue Kategorie <b>'{$category->getCatLabel()}'</b> wird gespeichert... <i>(" . basename(__FILE__) . ")</i></p>";	
		
							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#

							// Schritt 1 DB: DB-Verbindung herstellen
							$PDO = dbConnect(DB_NAME);

							#********** SAVE CATEGORY INTO DB **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Kategorie in DB... <i>(" . basename(__FILE__) . ")</i></p>\n";
							$saveSuccess = $category->saveToDB($PDO);

							#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)			echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
							unset($PDO);

							if( $saveSuccess === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Speichern der Kategorie in die DB! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
							
							} else {
								// Erfolgsfall								
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Kategorie erfolgreich unter ID: {$category->getCatID()} in die DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$dbSuccess = "Die neue Kategorie mit dem Namen <b>'{$category->getCatLabel()}'</b> wurde erfolgreich gespeichert.";	
								
								// Kategorie-Feld wieder leeren
								$category = NULL;

							} // SAVE CATEGORY INTO DB END

						} // CHECK IF CATEGORY NAME ALREADY EXISTS END

					} // FINAL FORM VALIDATION END

				} // PROCESS FORM 'NEW CATEGORY' END
	
			
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


				#***************************************************#
				#********** PROCESS FORM 'NEW BLOG ENTRY' **********#
				#***************************************************#
				
				// Schritt 1 FORM: Prüfen, ob Formular abgeschickt wurde
				if( isset($_POST['formNewBlogEntry']) === true ) {			
if(DEBUG)		echo "<p class='debug'>🧻 Line <b>" . __LINE__ . "</b>: Formular 'New Blog Entry' wurde abgeschickt... <i>(" . basename(__FILE__) . ")</i></p>";	
					
					// Schritt 2 FORM: Daten auslesen, entschärfen, DEBUG-Ausgabe
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Werte werden ausgelesen und entschärft... <i>(" . basename(__FILE__) . ")</i></p>\n";

					#********** GENERATE NEW BLOG OBJECT **********#
					// $category=new Category(),
					// $user=new User(),
					// $blogHeadline=NULL, $blogImagePath=NULL, $blogImageAlignment=NULL, 
					// $blogContent=NULL, $blogDate=NULL, $blogID=NULL


					$blog = new Blog( category: new Category($_POST['f1']), user: $user,
											blogHeadline: $_POST['f2'], blogImageAlignment: $_POST['f4'],
											blogContent: $_POST['f5'] );

/*									
if(DEBUG_V)		echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$blog <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)		print_r($blog);					
if(DEBUG_V)		echo "</pre>";
*/

					// Schritt 3 FORM: Feldvalidierung
if(DEBUG)		echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Feldwerte werden validiert... <i>(" . basename(__FILE__) . ")</i></p>\n";
					/*
						[x] Validieren der Formularwerte (Feldprüfungen)
						[x] Vorbelegung der Formularfelder für den Fehlerfall 
						{x] Abschließende Prüfung, ob das Formular insgesamt fehlerfrei ist
					*/
					$errorCategory	= validateInputString($blog->getCategory()->getCatID(), minLength:1);
					$errorHeadline = validateInputString($blog->getBlogHeadline());
					$errorContent 	= validateInputString($blog->getBlogContent(), minLength:5, maxLength:20000);

					#********** FINAL FORM VALIDATION PART I (FIELDS VALIDATION) **********#					
					if( $errorHeadline !== NULL OR $errorContent !== NULL OR $errorCategory !== NULL ) {
						// Fehlerfall
if(DEBUG)			echo "<p class='debug err'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART I: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>";
						
					} else {
						// Erfolgsfall
if(DEBUG)			echo "<p class='debug ok'>Line <b>" . __LINE__ . "</b>: FINAL FORM VALIDATION PART I: Das Formular ist formal fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>";

						#****************************************#
						#********** IMAGE UPLOAD START **********#
						#****************************************#
/*
if(DEBUG_V)			echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$_FILES <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)			print_r($_FILES);					
if(DEBUG_V)			echo "</pre>";
*/
						#********** CHECK IF IMAGE UPLOAD IS ACTIVE **********#
						if( $_FILES['f3']['tmp_name'] === '' ) {
							// Image Upload inactive
if(DEBUG)				echo "<p class='debug'><b>Line " . __LINE__ . "</b>: Image Upload inaktiv. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								
						} else {
							// Image upload active
if(DEBUG)				echo "<p class='debug hint'><b>Line " . __LINE__ . "</b>: Image Upload aktiv. <i>(" . basename(__FILE__) . ")</i></p>\n";				
				
							$validateImageUploadReturnArray = validateImageUpload( $_FILES['f3']['tmp_name'] );

/*				
if(DEBUG_V)				echo "<pre class='debug value'><b>Line " . __LINE__ . "</b>: \$validateImageUploadReturnArray <i>(" . basename(__FILE__) . ")</i>:<br>\n";					
if(DEBUG_V)				print_r($validateImageUploadReturnArray);					
if(DEBUG_V)				echo "</pre>";								
*/

							#********** VALIDATE IMAGE UPLOAD **********#
							if( $validateImageUploadReturnArray['imageError'] !== NULL ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Bildupload: $validateImageUploadReturnArray[imageError]! <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
								$errorImageUpload = $validateImageUploadReturnArray['imageError'];
									
							} else {
								// Erfolgsfall
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Bild erfolgreich nach <i>'$validateImageUploadReturnArray[imagePath]' auf den Server geladen.</i>. <i>(" . basename(__FILE__) . ")</i></p>\n";				
									
								$blog->setBlogImagePath($validateImageUploadReturnArray['imagePath']);

							} // VALIDATE IMAGE UPLOAD ENDS HERE
						}
						#********** IMAGE UPLOAD ENDS HERE **********#

						#********** FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION) **********#
						if( $errorImageUpload !== NULL ) {
							// Fehlerfall
if(DEBUG)				echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II: Das Formular enthält noch Fehler! <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
						} else {
							// Erfolgsfall
if(DEBUG)				echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: FINAL FORM VALIDATION II: Das Formular ist komplett fehlerfrei. <i>(" . basename(__FILE__) . ")</i></p>\n";				
						
							
							#***********************************#
							#********** DB OPERATIONS **********#
							#***********************************#

							// Schritt 1 DB: DB-Verbindung herstellen
							$PDO = dbConnect(DB_NAME);

							#********** SAVE BLOG DATA TO DB **********#
if(DEBUG)				echo "<p class='debug'>📑 <b>Line " . __LINE__ . "</b>: Speichere Blog-Daten in DB... <i>(" . basename(__FILE__) . ")</i></p>\n";

							$saveSuccess = $blog->saveToDB($PDO);

							#********** CLOSE DB CONNECTION **********#
if(DEBUG_DB)			echo "<p class='debug db'><b>Line " . __LINE__ . "</b>: DB-Verbindung geschlossen. <i>(" . basename(__FILE__) . ")</i></p>\n";
							unset($PDO);

							// Schritt 4 DB: Datenbankoperation auswerten
							if( $saveSuccess === false ) {
								// Fehlerfall
if(DEBUG)					echo "<p class='debug err'><b>Line " . __LINE__ . "</b>: FEHLER beim Speichern des Blogs in die DB! <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$dbError = 'Es ist ein Fehler aufgetreten! Bitte versuchen Sie es später noch einmal.';
							
							} else {
								// Erfolgsfall								
if(DEBUG)					echo "<p class='debug ok'><b>Line " . __LINE__ . "</b>: Blog erfolgreich unter ID: {$blog->getBlogID()} in die DB gespeichert. <i>(" . basename(__FILE__) . ")</i></p>\n";				
								$dbSuccess = "Der neue Blog mit dem Titel <b>'{$blog->getBlogHeadline()}'</b> wurde erfolgreich gespeichert.";	
								
								// Kategorie-Feld wieder leeren
								$blog = NULL;

							} // SAVE BLOG DATA TO DB END

						} // FINAL FORM VALIDATION II (IMAGE UPLOAD VALIDATION) END

					} // FINAL FORM VALIDATION PART I (FIELDS VALIDATION) END

				} // PROCESS FORM 'NEW BLOG ENTRY' END

#***************************************************************************************#
?>

<!doctype html>

<html>

	<head>
		<meta charset="utf-8">
		<title>PHP-Projekt Blog</title>
		<link rel="stylesheet" href="./css/main.css">
		<link rel="stylesheet" href="./css/debug.css">
	</head>

	<body class="dashboard">

		<!-- ---------- PAGE HEADER START ---------- -->
	
		<header class="fright">
			<a href="?action=logout">Logout</a><br>
			<a href="index.php"><< zum Frontend</a>
		</header>
		<div class="clearer"></div>

		<br>
		<hr>
		<br>
		
		<!-- ---------- PAGE HEADER END ---------- -->
		
		<h1 class="dashboard">PHP-Projekt Blog - Dashboard</h1>
		<p class="name">Aktiver Benutzer: <?= $user->getFullName() ?></p>
		
		
		<!-- ---------- POPUP MESSAGE START ---------- -->
		<?php if( $dbError OR $dbSuccess ): ?>
		<popupBox>
			<?php if($dbError): ?>
			<h3 class="error"><?= $dbError ?></h3>
			<?php elseif($dbSuccess): ?>
			<h3 class="success"><?= $dbSuccess ?></h3>
			<?php endif ?>
			<a class="button" onclick="document.getElementsByTagName('popupBox')[0].style.display = 'none'">Schließen</a>
		</popupBox>		
		<?php endif ?>
		<!-- ---------- POPUP MESSAGE END ---------- -->
		
		
		<!-- ---------- LEFT PAGE COLUMN START ---------- -->
		<main class="forms fleft">			
						
			<h2 class="dashboard">Neuen Blog-Eintrag verfassen</h2>
			<p class="small">
				Um einen Blogeintrag zu verfassen, muss dieser einer Kategorie zugeordnet werden.<br>
				Sollte noch keine Kategorie vorhanden sein, erstellen Sie diese bitte zunächst.
			</p> 
			
			
			<!-- ---------- FORM 'NEW BLOG ENTRY' START ---------- -->
			<form action="" method="POST" enctype="multipart/form-data">
				<input class="dashboard" type="hidden" name="formNewBlogEntry">
				
				<br>
				<label>Kategorie:</label>
				<select class="dashboard bold" name="f1">	
				<?php if( count($allCategoriesObjectsArray) !== 0 ): ?>				
					<?php foreach($allCategoriesObjectsArray AS $categorySingleObject): ?>
						<option value='<?= $categorySingleObject->getCatID() ?>'<?php if( $blog?->getCategory()->getCatID() == $categorySingleObject->getCatID() ) echo 'selected'?>><?= $categorySingleObject->getCatLabel() ?></option>
					<?php endforeach ?>
				<?php else: ?>
					<option value='' style='color: darkred'>Bitte zuerst eine Kategorie anlegen!</option>			
				<?php endif ?>
				</select>
				
				<br>
				
				<label>Überschrift:</label>
				<span class="error"><?= $errorHeadline ?></span><br>
				<input class="dashboard" type="text" name="f2" placeholder="..." value="<?= $blog?->getBlogHeadline() ?>"><br>
				
				
				<!-- ---------- IMAGE UPLOAD START ---------- -->
				<label>[Optional] Bild veröffentlichen:</label>
				<span class="error"><?= $errorImageUpload ?></span>
				<imageUpload>					
					
					<!-- -------- INFOTEXT FOR IMAGE UPLOAD START -------- -->
					<p class="small">
						Erlaubt sind Bilder des Typs 
						<?php $allowedMimetypes = implode( ', ', array_keys(IMAGE_ALLOWED_MIME_TYPES) ) ?>
						<?= strtoupper( str_replace( array(', image/jpeg', 'image/'), '', $allowedMimetypes) ) ?>.
						<br>
						Die Bildbreite darf 	<?= IMAGE_MAX_WIDTH ?> Pixel nicht übersteigen.<br>
						Die Bildhöhe darf 	<?= IMAGE_MAX_HEIGHT ?> Pixel nicht übersteigen.<br>
						Die Dateigröße darf 	<?= IMAGE_MAX_SIZE/1024 ?>kB nicht übersteigen.
					</p>
					<!-- -------- INFOTEXT FOR IMAGE UPLOAD END -------- -->
					<input type="file" name="f3">

					<select class="alignment fright" name="f4">
						<option value="fleft" 	<?php if($blog?->getBlogImageAlignment() == 'fleft') echo 'selected'?>>align left</option>
						<option value="fright" 	<?php if($blog?->getBlogImageAlignment() == 'fright') echo 'selected'?>>align right</option>
					</select>
				</imageUpload>
				<br>	
				<!-- ---------- IMAGE UPLOAD END ---------- -->
				
				
				<label>Inhalt des Blogeintrags:</label>
				<span class="error"><?= $errorContent ?></span><br>
				<textarea class="dashboard" name="f5" placeholder="..."><?= $blog?->getBlogContent() ?></textarea><br>
				
				<div class="clearer"></div>
				
				<input class="dashboard" type="submit" value="Veröffentlichen">
			</form>
			<!-- ---------- FORM 'NEW BLOG ENTRY' END ---------- -->
			
		</main>
		<!-- ---------- LEFT PAGE COLUMN END ---------- -->
		
		
		
		<!-- ---------- RIGHT PAGE COLUMN START ---------- -->
		<aside class="forms fright">
		
			<h2 class="dashboard">Neue Kategorie anlegen</h2>
			
			
			<!-- ---------- FORM 'NEW CATEGORY' START ---------- -->			
			<form class="dashboard" action="" method="POST">
			
				<input class="dashboard" type="hidden" name="formNewCategory">
				
				<span class="error"><?= $errorCatLabel ?></span><br>
				<label>Name der neuen Kategorie:</label>
				<input class="dashboard" type="text" name="f6" placeholder="..." value="<?= $category?->getCatLabel() ?>"><br>

				<input class="dashboard" type="submit" value="Neue Kategorie anlegen">
			</form>
			<!-- ---------- FORM 'NEW CATEGORY' END ---------- -->
			
		
		</aside>

		<div class="clearer"></div>
		<!-- ---------- RIGHT PAGE COLUMN END ---------- -->
		
		
	</body>
</html>






