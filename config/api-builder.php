<?php

return [

	'template_path' => base_path('resources/stubs'),

	'base_api' => 'v1',

	'models' => [

		'extends' => 'Illuminate\Database\Eloquent\Model',

		'traits' => [

		],
	],

	'controllers' => [

		'extends' => 'App\Http\Controllers\Controller',

		'searchable' => false,

		'namespace' => 'App\Http\Controllers\Api',


	],

	'tests' => [

		'hide_fields' => ['id', 'team_id', 'created_at', 'updated_at'],
	]

];