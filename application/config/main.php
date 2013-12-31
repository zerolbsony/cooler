<?php
return array(
	'name'=>'Cooler Framework',

	'modules'=>array(
		
	),

	'components'=>array(
		'user'=>array(
			'allowAutoLogin'=>true,
		),
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'home' => 'site/index',
			),
		),
		'db'=>array(
			'connectionString' => 'mysql:host='.HOST.';port='.PORT.';dbname='.DBNAME,
			'tablePrefix' => TABLE_PREFIX,
			'emulatePrepare' => true,
			'username' => USERNAME,
			'password' => PASSWORD,
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
	
	//
	'language'=>'zh_cn',
);
