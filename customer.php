<!doctype html>
<html>

<head>
	<meta charset="UTF-8">
	<title>New Customer</title>
	<script src="/dmxAppConnect/dmxAppConnect.js"></script>
	<script src="/js/jquery-3.3.1.slim.min.js"></script>
	<link rel="stylesheet" href="/fontawesome4/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="/bootstrap/4/darkly/bootstrap.min.css">

	<link rel="stylesheet" href="/dmxAppConnect/dmxNotifications/dmxNotifications.css">
	<script src="/dmxAppConnect/dmxNotifications/dmxNotifications.js" defer=""></script>
</head>

<body is="dmx-app" id="stripeAPI1">
	<dmx-notifications id="notifies1"></dmx-notifications>
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="container">
					<form id="form1" method="post" is="dmx-serverconnect-form" action="dmxConnect/api/stripe/stripe.php" dmx-on:success="notifies1.success(form1.data.api1.data.id)">
						<div class="row justify-content-center">
							<div class="col-12 col-md-8 col-lg-7 col-md-5 text-center">
								<div class="fdb-box fdb-touch">
									<div class="row">
										<div class="col">
											<h1>Register</h1>
										</div>
									</div>
									<div class="row">
										<div class="col mt-4">
											<input type="text" id="inp_name" name="Name" class="form-control" placeholder="Name">
										</div>
									</div>
									<div class="row mt-4">
										<div class="col">
											<input type="text" id="inp_email" name="Email" class="form-control" placeholder="Email">
										</div>
									</div>
									<div class="row mt-4">
										<div class="col">
											<input type="password" id="inp_password" name="Password" class="form-control mb-1" placeholder="Password">
										</div>
									</div>
									<div class="row mt-4">
										<div class="col">
											<button class="btn text-primary btn-block" type="submit">Create Account</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script src="/bootstrap/4/js/popper.min.js"></script>
	<script src="/bootstrap/4/js/bootstrap.min.js"></script>
</body>

</html>