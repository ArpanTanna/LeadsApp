<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'leadupdate' => [
        'first_name' => 'required',
        'last_name' => 'required',
		'email' => 'required|email',
		'company_name' => 'required',
		'other_info.title' => 'required',
		'other_info.city' => 'required'
    ],

    'funnelupdate' => [
        'name' => 'required',
        'subject' => 'required',
        'content' => 'required'
    ],

    'sendemail' => [
        'leads' => 'required',
        'funnel' => 'required'
    ]

]

?>
