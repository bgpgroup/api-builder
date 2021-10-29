<?php

return [

	'template_path' => base_path('resources/stubs'),

	'base_api' => 'v1',

	'requests' => [

		'namespace' => 'App\Http\Requests',

		'base' => 'app/',
	],

	'models' => [

		'extends' => 'Illuminate\Database\Eloquent\Model',

		'traits' => [

		],

		'namespace' => 'App\Models',

		'base' => 'app/',
	],

	'controllers' => [

		'extends' => 'App\Http\Controllers\Controller',

		'searchable' => false,

		'namespace' => 'App\Http\Controllers\Api',


	],

	'tests' => [

		'hide_fields' => ['id', 'team_id', 'created_at', 'updated_at'],
	],

	'dto' => [

		'namespace' => 'App\Dto',

		'base' => 'app/',
	],

	'dto-collection' => [

		'namespace' => 'App\Dto',

		'base' => 'app/',

		'extends' => 'Illuminate\Support\Collection',
	],

];