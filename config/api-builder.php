<?php

return [

	'template_path' => base_path('resources/stubs'),

	'models' => [

		'extends' => 'Illuminate\Database\Eloquent\Model',

		'traits' => [

		],
	],

	'controllers' => [

		'extends' => 'App\Http\Controllers\Controller',

		'searchable' => false,

		'namespace' => 'App\Http\Controllers\Api',


	]

];