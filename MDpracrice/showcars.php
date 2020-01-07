<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<title>detail page</title>
	<script src="../dmxAppConnect/dmxAppConnect.js"></script>
	<script src="../js/jquery-3.3.1.slim.min.js"></script>
	<link rel="stylesheet" href="../bootstrap/4/sketchy/bootstrap.min.css">
	<link rel="stylesheet" href="../fontawesome4/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="../dmxAppConnect/dmxMediumEditor/dmxMediumEditor.css">
	<link rel="stylesheet" href="../dmxAppConnect/dmxMediumEditor/themes/default.css">
	<script src="../dmxAppConnect/dmxMediumEditor/dmxMediumEditor.js" defer=""></script>
	<script src="../dmxAppConnect/dmxMediumEditor/medium-editor.js" defer=""></script>
	<link rel="stylesheet" href="../dmxAppConnect/dmxMediumEditor/medium-editor.css">
	<link rel="stylesheet" href="../dmxAppConnect/dmxBootstrap4TableGenerator/dmxBootstrap4TableGenerator.css">
</head>

<body is="dmx-app" id="detail1">
	<h1>Title</h1>
	<section>
		<div class="container">
			<table class="table table-striped table-bordered table-hover table-sm">
				<tbody dmx-generator="bs4table" dmx-populate="serverconnect1.data.query1">
					<tr>
						<th>Id</th>
						<td dmx-text="serverconnect1.data.query1[0].id"></td>
					</tr>
					<tr>
						<th>Make</th>
						<td dmx-text="serverconnect1.data.query1[0].make"></td>
					</tr>
					<tr>
						<th>Model</th>
						<td dmx-text="serverconnect1.data.query1[0].model"></td>
					</tr>
					<tr>
						<th>Year</th>
						<td dmx-text="serverconnect1.data.query1[0].year"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
	<dmx-serverconnect id="serverconnect1" url="../dmxConnect/api/masterDetail/detail.php" dmx-param:filter="query.filter"></dmx-serverconnect>
	<script src="../bootstrap/4/js/popper.min.js"></script>
	<script src="../bootstrap/4/js/bootstrap.min.js"></script>
</body>

</html>