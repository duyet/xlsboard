<?php require_once ('load.php'); ?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"><![endif]--><!--[if IE 7 ]><html class="ie ie7" lang="en"><![endif]--><!--[if IE 8 ]><html class="ie ie8" lang="en"><![endif]--><!--[if (gte IE 9)|!(IE)]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="UTF-8">
<title>LvDuit</title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<link rel="shortcut icon" href="images/ico/favicon.png">
<!--[if IE]><![endif]-->
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">

<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<!-- LvDuit.Com -->

</head>
<body>


<div class="fullwidth bodycontainer colour1 clearfix" id="topcontainer">
	
	<h5>xlsboard</h5>

</div>


<div class="fullwidth clearfix">
		
		<table class="table table-bordered">
			<?php
				for ($i = 1; $i <= $maxRow; $i++) {
					echo '<tr>';
					for ($j = 'A'; $j <= $maxCol; $j++) {
						echo '<td width="'. $tableColumnWidth .'%">'. (isset($finalData["$j$i"]) ? $finalData["$j$i"] : '&nbsp;') .'</td>';
					}
					echo '</tr>';
				}
			?>
		</table>
		
	
</div>

<div class="fullwidth  clearfix">
	<div class="bodycontainer clearfix">

	<p>
		(c) 2015
	</p>

	
	</div>
</div>

   
</body>
</html>
