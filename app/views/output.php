<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Rest API</title>
</head>
<body>
	<h1><?=$msg?></h1>
	Country: <strong><?=$country?></strong><br>
	Country Code: <strong><?=$country_code?></strong><br><br>
	Flag Code:<br>
	<div style="word-break: break-all;width:100%;">
		<?=$flag_code?>
	</div>
	<img src="<?=$flag_code?>" style="width:100px; height: auto;">
	<br><br>Rendered in: {elapsed_time} sec
</body>
</html>