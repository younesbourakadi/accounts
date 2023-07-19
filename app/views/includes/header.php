<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Mes Comptes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>

    <div class="container-fluid">
        <header class="row flex-wrap justify-content-between align-items-center p-3 mb-4 border-bottom">
            <a href="index.php?page=home" class="col-1">
                <i class="bi bi-piggy-bank-fill text-primary fs-1"></i>
            </a>
            <nav class="col-11 col-md-7">
                <ul class="nav">
                    <li class="nav-item">
                        <a href="index.php?page=home" class="nav-link <?= ($pageTitle === 'Opérations de Juillet 2023') ? 'link-secondary' : 'link-body-emphasis' ?>" aria-current="page">Opérations</a>
                    </li>
                    <li class="nav-item">
                        <a href="summary.php" class="nav-link <?= ($pageTitle === 'Synthèse de Juillet 2023') ? 'link-secondary' : 'link-body-emphasis' ?>">Synthèses</a>
                    </li>
                    <li class="nav-item">
                        <a href="categories.php" class="nav-link <?= ($pageTitle === 'Catégories') ? 'link-secondary' : 'link-body-emphasis' ?>">Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a href="import.php" class="nav-link <?= ($pageTitle === 'Importer des opérations') ? 'link-secondary' : 'link-body-emphasis' ?>">Importer</a>
                    </li>
                    <li class="nav-item">
                        <a href="add.php" class="nav-link link-body-emphasis">Ajouter</a>
                    </li>
                </ul>
            </nav>
            <form action="" class="col-12 col-md-4" role="search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Rechercher..." aria-describedby="button-search">
                    <button class="btn btn-primary" type="submit" id="button-search">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </header>
    </div>