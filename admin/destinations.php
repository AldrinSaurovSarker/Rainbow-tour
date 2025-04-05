<?php include '../template/header.php'; ?>
<?php include '../db/connect-db.php'; ?>
<?php include '../auth/connect-session.php'; ?>

<?php
    if (isset($_POST['add_destination'])) {
        $name = $conn->real_escape_string($_POST['dest_name']);
        $location = $conn->real_escape_string($_POST['dest_location']);
        $description = $conn->real_escape_string($_POST['dest_description']);

        $conn->query("INSERT INTO destinations (name, description, location) VALUES ('$name', '$description', '$location')");
        $dest_id = $conn->insert_id;

        if (!empty($_POST['activities'])) {
            foreach ($_POST['activities'] as $activity_id) {
                $conn->query("INSERT INTO destination_activities (activity_id, destination_id) VALUES ($activity_id, $dest_id)");
            }
        }

        echo "<script>location.href='destinations.php';</script>";
        exit;
    }

    if (isset($_POST['update_destination'])) {
        $id = (int) $_POST['dest_id'];
        $name = $conn->real_escape_string($_POST['dest_name']);
        $location = $conn->real_escape_string($_POST['dest_location']);
        $description = $conn->real_escape_string($_POST['dest_description']);

        $conn->query("UPDATE destinations SET name='$name', description='$description', location='$location' WHERE id=$id");
        $conn->query("DELETE FROM destination_activities WHERE destination_id = $id");

        if (!empty($_POST['activities'])) {
            foreach ($_POST['activities'] as $activity_id) {
                $conn->query("INSERT INTO destination_activities (activity_id, destination_id) VALUES ($activity_id, $id)");
            }
        }

        echo "<script>location.href='destinations.php';</script>";
        exit;
    }

    if (isset($_GET['delete_dest'])) {
        $id = (int) $_GET['delete_dest'];
        $conn->query("DELETE FROM destinations_gallery WHERE destination_id = $id");
        $conn->query("DELETE FROM destination_packages WHERE destination_id = $id");
        $conn->query("DELETE FROM destination_activities WHERE destination_id = $id");
        $conn->query("DELETE FROM destinations WHERE id = $id");
        echo "<script>location.href='destinations.php';</script>";
        exit;
    }
?>

<head>
    <link href="admin-style.css" rel="stylesheet" />
    <link href="../images/banners/icon.png" rel="icon">
</head>

<body class="bg-info">
    <div class="container admin-container my-5">
        <div class="d-flex mb-4">
            <h2>Admin Panel</h2>
            
            <div class="d-flex ms-auto justify-content-center">
                <a href="dashboard.php" class="btn-dashboard d-flex align-items-center">
                    <i class="fas fa-qrcode me-2"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Destinations</h4>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal-destinations">
                <i class="fas fa-plus"> Add Destination</i>
            </button>
        </div>

        <table class="table table-bordered table-striped mb-5">
            <thead class="table-dark">
                <tr>
                    <th>SL.</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Activities</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $i = 1;
                    $destResult = $conn->query("SELECT * FROM destinations");
                    while ($row = $destResult->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <?= $i++ ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['name']) ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['location']) ?>
                        </td>
                        <td style="max-width: 300px;">
                            <?php
                                $acts = $conn->query("SELECT a.name FROM activities a 
                                    JOIN destination_activities da ON da.activity_id = a.id 
                                    WHERE da.destination_id = {$row['id']}
                                    ORDER BY a.name");
                                while ($a = $acts->fetch_assoc()):
                            ?>
                            <span class="badge bg-primary activity-badge"><?= htmlspecialchars($a['name']) ?></span>
                            <?php endwhile; ?>
                        </td>

                        <td style="max-width: 300px;">
                            <?= nl2br(htmlspecialchars($row['description'])) ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editDest<?= $row['id'] ?>">
                                <i class="fas fa-pencil-alt"> Edit</i>
                            </button>
                            <a href="?delete_dest=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this destination?')">
                                <i class="fas fa-trash-alt"> Delete</i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="editDest<?= $row['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form method="POST">
                                    <input type="hidden" name="dest_id" value="<?= $row['id'] ?>">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Destination</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label>Name</label>
                                                <input type="text" name="dest_name"
                                                    value="<?= htmlspecialchars($row['name']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Location</label>
                                                <input type="text" name="dest_location"
                                                    value="<?= htmlspecialchars($row['location']) ?>" class="form-control"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Description</label>
                                                <textarea name="dest_description" class="form-control"
                                                    rows="4"><?= htmlspecialchars($row['description']) ?></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Select Activities</label>
                                            <div class="d-flex flex-wrap gap-2 border rounded p-3">
                                                <?php
                                            $selectedActs = [];
                                            $getActs = $conn->query("SELECT activity_id FROM destination_activities WHERE destination_id = {$row['id']}");
                                            while ($act = $getActs->fetch_assoc()) {
                                                $selectedActs[] = $act['activity_id'];
                                            }
                                            $activities = $conn->query("SELECT DISTINCT id, name FROM activities ORDER BY name");
                                            while ($act = $activities->fetch_assoc()):
                                            ?>
                                                <label class="activity-label">
                                                    <input type="checkbox" name="activities[]" value="<?= $act['id'] ?>"
                                                        class="d-none" <?=in_array($act['id'], $selectedActs) ? 'checked'
                                                        : '' ?>>
                                                    <span
                                                        class="btn btn-sm <?= in_array($act['id'], $selectedActs) ? 'btn-primary text-white' : 'btn-outline-primary' ?>">
                                                        <?= htmlspecialchars($act['name']) ?>
                                                    </span>
                                                </label>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button name="update_destination" class="btn btn-primary">Update</button>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="modal fade" id="addModal-destinations" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Destination</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" name="dest_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Location</label>
                                    <input type="text" name="dest_location" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description</label>
                                    <textarea name="dest_description" class="form-control" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Select Activities</label>
                                <div class="d-flex flex-wrap gap-2 border rounded p-3">
                                    <?php
                                    $activities = $conn->query("SELECT DISTINCT id, name FROM activities ORDER BY name");
                                    while ($act = $activities->fetch_assoc()):
                                    ?>
                                        <label class="activity-label">
                                            <input type="checkbox" name="activities[]" value="<?= $act['id'] ?>" class="d-none">
                                            <span class="btn btn-sm btn-outline-primary">
                                                <?= htmlspecialchars($act['name']) ?>
                                            </span>
                                        </label>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button name="add_destination" class="btn btn-success">Add</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.activity-label input').forEach(input => {
            input.addEventListener('change', function () {
                const span = this.nextElementSibling;
                if (this.checked) {
                    span.classList.remove('btn-outline-primary');
                    span.classList.add('btn-primary', 'text-white');
                } else {
                    span.classList.remove('btn-primary', 'text-white');
                    span.classList.add('btn-outline-primary');
                }
            });
        });
    </script>
</body>