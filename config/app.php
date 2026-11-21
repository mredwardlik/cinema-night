<?php
//*******************************************************************************
//							SETTING START INFO
//*******************************************************************************

return [
	// Show error details on the page (turn off in production)
	'debug'    => getenv('APP_DEBUG') !== false ? (bool) getenv('APP_DEBUG') : true,

	'timezone' => 'Asia/Almaty',

	// Cinema hall: number of seats in every row
	'hall'     => [16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16],

	// Max seats in one booking request
	'max_seats_per_booking' => 10
];
