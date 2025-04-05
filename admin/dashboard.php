<?php include '../template/header.php'; ?>
<?php include '../db/connect-db.php'; ?>
<?php include '../auth/connect-session.php'; ?>

<head>
	<link href="../images/banners/icon.png" rel="icon">
	<style>
		.card-hover:hover {
			transform: scale(1.05);
			transition: transform 0.3s ease-in-out;
		}
	</style>
</head>

<body class="bg-info">
	<div class="container my-5">
		<h2 class="mb-4">Admin Dashboard</h2>
		<div class="row row-cols-1 row-cols-md-3 g-4">
			<div class="col">
				<a href="destinations.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Destinations</h5>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col">
				<a href="packages.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Packages</h5>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col">
				<a href="schedules.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Schedules</h5>
							</div>
						</div>
					</div>
				</a>
			</div>

			<div class="col">
				<a href="hotels.php" class="text-decoration-none">
					<div class="card card-hover shadow-sm">
						<div class="ratio ratio-1x1">
							<div class="card-body d-flex justify-content-center align-items-center">
								<h5 class="card-title text-center">Hotels</h5>
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>
</body>