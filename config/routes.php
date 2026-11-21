<?php
//*******************************************************************************
//							SETTING START INFO
//*******************************************************************************
//
//	'uri regexp' => 'controller/action/param1/param2...'

return
[
	'ticket([0-9]{1,10})' => 'ticket/index/$1',
	'wipe([0-9]{1,10})'   => 'ticket/wipe/$1',
	'login/chlogin'       => 'login/chlogin',
	'login/chreg'         => 'login/chreg',
	'cabinet/taken'       => 'cabinet/taken',
	'cabinet/seats'       => 'cabinet/seats',
	'cabinet/logout'      => 'cabinet/logout',
	'cabinet/ch'          => 'cabinet/ch',
	'cabinet/approve'     => 'cabinet/approve',
	'cabinet/reject'      => 'cabinet/reject',
	'login'               => 'login/index',
	'doc'                 => 'doc/index',
	'cabinet'             => 'cabinet/index',
	''                    => 'main/index'
];
