<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Untitled Document</title>
	<script src="dmxAppConnect/dmxAppConnect.js"></script>
	<script src="js/jquery-3.3.1.slim.min.js"></script>
	<link rel="stylesheet" href="fontawesome4/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="bootstrap/4/sketchy/bootstrap.min.css">
	<script src="dmxAppConnect/dmxTyped/dmxTyped.js" defer=""></script>
	<script src="dmxAppConnect/dmxTyped/typed.min.js" defer=""></script>
</head>

<body is="dmx-app" id="showcustomer">
	<div class="container">
		<dmx-serverconnect id="serverconnect1" url="dmxConnect/api/admin/customerDetails.php" dmx-param:cus="query.cus"></dmx-serverconnect>
		<div class="row">
			<div class="col-sm-12">
				<h1>Details

				</h1>
				<dl>
					<dt class="font-weight-bold text-uppercase">ID
					<dd dmx-text="query.cus">
						{{query.cus}}
					<dt class="font-weight-bold">Creation Date
					<dd dmx-text="serverconnect1.data.api1.data.created">{{serverconnect1.data.api1.data.description}}
					<dt class=" font-weight-bold">Email
					<dd dmx-text="serverconnect1.data.api1.data.email">{{serverconnect1.data.api1.data.email}}</dd>
					</dt>
					</dd>
					</dt>
					</dd>
					</dt>
				</dl>
			</div>
		</div>
	</div>
	<script src="bootstrap/4/js/popper.min.js"></script>
	<script src="bootstrap/4/js/bootstrap.min.js"></script>
</body>

</html>