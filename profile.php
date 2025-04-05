<?php
    include 'db/connect-db.php';
    include 'auth/connect-session.php';

    if (!isset($_SESSION['user'])) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }

    $user_email = $_SESSION['user'];
    $query = "SELECT * FROM tourists WHERE email = '$user_email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if (!$user) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $nid = mysqli_real_escape_string($conn, $_POST['nid']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $update_query = "UPDATE tourists 
                        SET name = '$name', nid = '$nid', email = '$email', dob = '$dob', phone = '$phone', address = '$address' 
                        WHERE email = '$user_email'";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Profile updated successfully.";
            header("Location: profile.php");
            exit();
        } else {
            $_SESSION['error'] = "Error updating profile.";
        }
    }
?>

<?php include 'template/header.php'; ?>

<body class="profile">
    <?php include 'template/navigation.php'; ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-4">Booked Tours</h3>
                    <?php
                        $userId = $user['id']; 

                        $query = "
                            SELECT tp.name, s.start_date, s.end_date, s.package_id, b.timestamp, p.status, p.amount, b.persons
                            FROM bookings b
                            INNER JOIN schedules s 
                            ON s.id = b.schedule_id
                            INNER JOIN tour_packages tp 
                            ON tp.id = s.package_id
                            INNER JOIN payments p 
                            ON p.booking_id = b.id
                            WHERE b.tourist_id = '$userId'";

                        $result = mysqli_query($conn, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $startDate = $row['start_date'];
                                $endDate = $row['end_date'];
                                $packageId = $row['package_id'];
                                $packageName = $row['name'];
                                $timestamp = $row['timestamp'];
                                $paymentStatus = $row['status'];
                                $amount = $row['amount'];
                                $persons = $row['persons'];
                                ?>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="card mb-2">
                                            <div class="card shadow-lg mb-4">
                                                <div class="card-body">
                                                    <h3 class="card-title text-center text-primary mb-3"><?php echo $packageName; ?></h3>
                                                    <p><strong>Start Date:</strong> <?php echo $startDate; ?></p>
                                                    <p><strong>End Date:</strong> <?php echo $endDate; ?></p>
                                                    <p><strong>Number of Participants:</strong> <?php echo $persons; ?></p>
                                                    <p><strong>Booking Date:</strong> <?php echo $timestamp; ?></p>
                                                    <p><strong>Payment Status:</strong> <?php echo $paymentStatus; ?></p>
                                                    <p><strong>Amount:</strong> <?php echo $amount; ?> TK</p>
                                                    <a href="package-details.php?id=<?php echo $packageId; ?>" class="btn btn-primary w-100 mt-3">View Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                            }
                        } else {
                            echo '<p class="text-center">You have no booked tours.</p>';
                        }
                        ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-4">Profile Information</h3>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo $_SESSION['success']; ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error']; ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="nid" class="form-label">NID</label>
                            <input type="text" class="form-control" id="nid" name="nid" value="<?php echo $user['nid']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required readonly>
                        </div>

                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $user['dob']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success">Update Profile</button>
                            <a href="auth/logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'template/footer.php'; ?>
</body>
