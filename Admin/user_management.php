<?php
session_start();
include '../dbconfig.php';

// Pastikan hanya pengguna dengan role 'Admin' yang bisa mengakses halaman ini
if ($_SESSION['role'] != 'Admin') {
    header("Location: ../index.php?error=Unauthorized Access");
    exit();
}

// Batasi data pengguna yang bisa dilihat Admin
$query = "SELECT users.id, users.username, users.email, roles.role_name 
          FROM users 
          JOIN roles ON users.role_id = roles.id 
          WHERE roles.role_name IN ('Staf Administrasi', 'Pimpinan')";
$result = $conn->query($query);


// Tambah pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash password
    $role_id = $_POST['role_id'];

    $query = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $username, $email, $password, $role_id);
    if ($stmt->execute()) {
        $message = "Pengguna berhasil ditambahkan.";
    } else {
        $error = "Gagal menambah pengguna.";
    }
    $stmt->close();
}

// Edit pengguna yang ada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['edit_username'];
    $email = $_POST['edit_email'];
    $role_id = $_POST['edit_role_id'];

    $query = "UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssii", $username, $email, $role_id, $user_id);
    if ($stmt->execute()) {
        $message = "Pengguna berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui pengguna.";
    }
    $stmt->close();
}

// Hapus pengguna
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Pengguna berhasil dihapus.";
    } else {
        $error = "Gagal menghapus pengguna.";
    }
    $stmt->close();
}

// Ambil data pengguna
$query = "SELECT users.id, users.username, users.email, roles.role_name, users.role_id FROM users JOIN roles ON users.role_id = roles.id";
$result = $conn->query($query);

include '../navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna - AccessGuard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2 class="mb-4">Manajemen Pengguna</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php elseif (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Tabel Daftar Pengguna -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['role_name']); ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editUserModal" data-id="<?= $row['id'] ?>" data-username="<?= htmlspecialchars($row['username']); ?>" data-email="<?= htmlspecialchars($row['email']); ?>" data-role_id="<?= $row['role_id'] ?>">Edit</button>
                        <a href="user_management.php?delete_id=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Button Tambah Pengguna -->
    <button class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Tambah Pengguna</button>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="user_management.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
    <label>Role</label>
    <select name="role_id" class="form-control" required>
        <?php
        $role_query = "SELECT id, role_name FROM roles WHERE role_name IN ('Admin')";
        $roles_result = $conn->query($role_query);
        while ($role = $roles_result->fetch_assoc()):
        ?>
            <option value="<?= $role['id']; ?>"><?= htmlspecialchars($role['role_name']); ?></option>
        <?php endwhile; ?>
    </select>
</div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="add_user" class="btn btn-primary">Tambah Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="user_management.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="edit_username" id="editUsername" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="edit_email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="edit_role_id" id="editRoleId" class="form-control" required>
                            <?php
                            $roles_result = $conn->query($role_query);
                            while ($role = $roles_result->fetch_assoc()):
                            ?>
                                <option value="<?= $role['id']; ?>"><?= htmlspecialchars($role['role_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="edit_user" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Isi data di modal Edit Pengguna
    $('#editUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var username = button.data('username');
        var email = button.data('email');
        var role_id = button.data('role_id');

        var modal = $(this);
        modal.find('#editUserId').val(id);
        modal.find('#editUsername').val(username);
        modal.find('#editEmail').val(email);
        modal.find('#editRoleId').val(role_id);
    });
</script>
</body>
</html>

<?php
$conn->close();
?>
