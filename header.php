
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title)?$page_title:'Go Offers' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="assets/main.css" rel="stylesheet"  crossorigin="anonymous">
    <link href="vendor/select2/css/select2.min.css" rel="stylesheet" />
</head>
<body>
<header class="navbar-dark bg-dark">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark container">
    <a class="navbar-brand" href="/offers-today/">Go Offers</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
        <ul class="navbar-nav me-5 text-light gap-3 ">
            <li class="nav-item">
                <a class="nav-link text-light" href="/offers-today/">Offers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-light" href="/offers-today/dashboard.php">Dashboard</a>
            </li>
        </ul>
        <?php
            if (isset($_SESSION['user_id'])) {
            echo"<button class='btn btn-danger' id='logoutBtn'>Logout</button>";
            }
        ?>
        <?php
            if (!isset($_SESSION['user_id'])) {
            echo"<a href='/offers-today/login.php' class='btn btn-success'>Login as owner</a>";
            }
        ?>
</nav>
</header>
 
