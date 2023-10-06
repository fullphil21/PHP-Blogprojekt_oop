<?php
#****************************************************************************************************#
				
				
				#***************************************************#
				#********** GLOBALE PROJECT CONFIGURATION **********#
				#***************************************************#
			
				
				#********** DATABASE CONFIGURATION **********#
				define('DB_SYSTEM',							'mysql');
				define('DB_HOST',								'localhost');
				define('DB_NAME',								'blog_oop');
				define('DB_USER',								'root');
				define('DB_PWD',								'');
				
				
				#********** EXTERNAL INPUT STRING CONFIGURATION **********#
				define('INPUT_MAX_LENGTH',	255);
				define('INPUT_MIN_LENGTH',	0);
				
				
				#********** IMAGE UPLOAD CONFIGURATION **********#
				define('IMAGE_MAX_WIDTH',					800);
				define('IMAGE_MAX_HEIGHT',					800);
				define('IMAGE_MIN_SIZE',					1024);
				define('IMAGE_MAX_SIZE',					128*1024);
				define('IMAGE_ALLOWED_MIME_TYPES',		array('image/jpeg'=>'.jpg', 'image/jpg'=>'.jpg', 'image/gif'=>'.gif', 'image/png'=>'.png'));
				
				
				#********** STANDARD PATHS CONFIGURATION **********#
				define('IMAGE_UPLOAD_PATH',				'./uploads/blogimages/');
				define('AVATAR_DUMMY_PATH',				'../css/images/avatar_dummy.png');
				define('CLASS_PATH',							'./class/');
				define('INTERFACE_PATH',					'./class/');
				define('TRAIT_PATH',							'../trait/');
				
				
				#********** STANDARD FILE EXTENSIONS CONFIGURATION **********#
				define('CLASS_FILE_EXTENSION',			'.class.php');
				define('INTERFACE_FILE_EXTENSION',		'.class.php');
				define('TRAIT_FILE_EXTENSION',			'.trait.php');
				
				
				#********** DEBUGGING **********#
				define('DEBUG', 								true);	// Debugging for main document
				define('DEBUG_V', 							true);	// Debugging for values
				define('DEBUG_F', 							true);	// Debugging for functions
				define('DEBUG_DB', 							true);	// Debugging for DB operations
				define('DEBUG_C', 							true);	// Debugging for classes
				define('DEBUG_CC', 							true);	// Debugging for class constructors and destructors
				define('DEBUG_T', 							true);	// Debugging for traits
				define('DEBUG_TC', 							true);	// Debugging for trait constructors and destructors
	


#****************************************************************************************************#