<head>
	<meta charset="UTF-8">
	<title><?php echo e(G::var('title')); ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Онлайн-бронирование мест на киноночь в Алматы">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="<?php csrf_token() ?>">
	<link rel="icon" type="image/x-icon" href="/template/image/favicon.ico">

	<?php foreach (G::varDirs('css') as $value) { ?>
	<link rel="stylesheet" href="<?php echo e($value); ?>">
	<?php } ?>
</head>
