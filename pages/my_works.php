<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['user_id'])) 
{
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) 
{
    $work_id = (int)$_GET['delete'];
    mysqli_begin_transaction($conn);
    
    try 
    {
        $check_sql = "SELECT `image_path` FROM `portfolio` WHERE `id` = $work_id AND `user_id` = $user_id";
        $check_result = mysqli_query($conn, $check_sql);
        $work = mysqli_fetch_assoc($check_result);
        
        if (!$work) 
        {
            throw new Exception("Работа не найдена или нет прав на удаление");
        }
        
        $delete_likes_sql = "DELETE FROM `portfolio_likes` WHERE `work_id` = $work_id";
        mysqli_query($conn, $delete_likes_sql);

        $delete_views_sql = "DELETE FROM `portfolio_views` WHERE `work_id` = $work_id";
        mysqli_query($conn, $delete_views_sql);
        
        $delete_sql = "DELETE FROM `portfolio` WHERE `id` = $work_id AND `user_id` = $user_id";
        mysqli_query($conn, $delete_sql);
        
        if ($work['image_path'] && file_exists($work['image_path'])) 
        {
            unlink($work['image_path']);
        }

        mysqli_commit($conn);
        $_SESSION['success'] = "Работа и все связанные данные успешно удалены";
    } 
    catch (Exception $e) 
    {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Ошибка при удалении: " . $e->getMessage();
    }
    
    header("Location: my_works.php");
    exit();
}

$works = mysqli_query($conn, "SELECT p.*, c.name as category_name, (SELECT COUNT(*) FROM portfolio_likes WHERE work_id = p.id) as likes_count, (SELECT COUNT(*) FROM portfolio_views WHERE work_id = p.id) as views_count FROM portfolio p JOIN portfolio_categories c ON p.category_id = c.id WHERE p.user_id = $user_id ORDER BY p.created_at DESC");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои работы - СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../profile.php">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="../profile.php">Назад в кабинет</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="my_works.php">Мои работы</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Добавить работу</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="../files/logout.php" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Выйти
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Мои работы</h2>
                <p class="lead text-muted">Управляйте своими работами в портфолио</p>
                <div class="divider mx-auto"></div>
            </div>
            <?php 
            if (isset($_SESSION['success']))
            { 
            ?>
                <div class="alert alert-success alert-dismissible fade show text-center">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php 
            } 
            ?>
            <?php 
            if (isset($_SESSION['error']))
            {
            ?>
                <div class="alert alert-danger alert-dismissible fade show text-center">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php 
            } 
            ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php 
                if (mysqli_num_rows($works) > 0)
                {
                ?>
                    <?php 
                    while($work = mysqli_fetch_assoc($works))
                    {
                    ?>
                        <div class="col">
                            <div class="card h-100 work-card">
                                <button class="btn btn-danger btn-sm btn-delete" onclick="if(confirm('Вы уверены, что хотите полностью удалить эту работу?')) window.location='my_works.php?delete=<?= $work['id'] ?>'">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button class="btn btn-primary btn-sm btn-edit" data-bs-toggle="modal" data-bs-target="#editWorkModal" data-id="<?= $work['id'] ?>" data-title="<?= htmlspecialchars($work['title']) ?>" data-category="<?= $work['category_id'] ?>" data-description="<?= htmlspecialchars($work['description']) ?>" data-tags="<?= htmlspecialchars($work['tags']) ?>" data-image="<?= htmlspecialchars($work['image_path']) ?>" data-links='<?= htmlspecialchars($work['external_links'] ?? '') ?>'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <img src="<?= htmlspecialchars($work['image_path']) ?>" class="card-img-top work-img" alt="<?= htmlspecialchars($work['title']) ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($work['title'], ENT_QUOTES, 'UTF-8', false) ?></h5>
                                    <span class="badge bg-primary mb-2"><?= htmlspecialchars($work['category_name']) ?></span>
                                    <p class="card-text"><?= mb_substr(htmlspecialchars($work['description'], ENT_QUOTES, 'UTF-8', false), 0, 100) ?>...</p>
                                </div>
                                <div class="card-footer border-0 bg-white d-flex justify-content-between">
                                    <p class="text-muted small border-0">
                                        <i class="bi bi-calendar me-1"></i> <?= date('d.m.Y', strtotime($work['created_at'])) ?>
                                    </p>
                                    <span class="stats-badge">
                                        <i class="bi bi-eye me-1"></i><?= $work['views_count'] ?> 
                                        <i class="bi bi-heart ms-2 me-1"></i><?= $work['likes_count'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php 
                    } 
                    ?>
                <?php 
                }
                else
                {
                 ?>
                    <div class="col-12">
                        <div class="card empty-portfolio">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-folder-x display-4 text-muted mb-4"></i>
                                <h4>Ваше портфолио пусто</h4>
                                <p class="text-muted mb-4">Добавьте свою первую работу, чтобы она появилась здесь</p>
                                <a href="portfolio.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-plus-circle me-1"></i> Добавить работу
                                </a>
                            </div>
                        </div>
                    </div>
                <?php 
                } 
                ?>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="editWorkModal" tabindex="-1" aria-labelledby="editWorkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWorkModalLabel">Редактирование работы</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/edit_work.php" enctype="multipart/form-data">
                <input type="hidden" name="work_id" id="editWorkId">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="pe-3">
                        <div class="mb-3">
                            <label for="editTitle" class="form-label">Название работы <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCategory" class="form-label">Категория <span class="text-danger">*</span></label>
                            <select class="form-select" id="editCategory" name="category_id" required>
                                <?php 
                                $categories_query = mysqli_query($conn, "SELECT * FROM portfolio_categories");
                                
                                while($category = mysqli_fetch_assoc($categories_query)) 
                                {
                                    echo '<option value="'.$category['id'].'">'.htmlspecialchars($category['name']).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Описание <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editTags" class="form-label">Теги (через запятую) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editTags" name="tags" placeholder="Например: дизайн, логотип, брендинг" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Изображение</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Макс. размер: 2MB. Допустимые форматы: JPG, PNG.</div>
                            <div class="mt-2">
                                <p>Текущее изображение:</p>
                                <img id="editCurrentImage" src="" class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Внешние ссылки</label>
                            <div class="row g-2" id="externalLinksContainer">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-behance"></i></span>
                                            <input type="url" class="form-control" name="external_links[Behance]" placeholder="Ссылка на Behance" id="editBehanceLink">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-github"></i></span>
                                            <input type="url" class="form-control" name="external_links[GitHub]" placeholder="Ссылка на GitHub" id="editGithubLink">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-dribbble"></i></span>
                                            <input type="url" class="form-control" name="external_links[Dribbble]" placeholder="Ссылка на Dribbble" id="editDribbbleLink">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-2">
                                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                            <input type="url" class="form-control" name="external_links[Website]" placeholder="Другая ссылка" id="editWebsiteLink">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5 class="mb-3">СтудМаркет</h5>
                <p>Специализированная платформа для студентов Колледжа предпринимательства, где можно демонстрировать работы, находить вакансии и взаимодействовать с работодателями.</p>
                <div class="social-icons">
                    <a href="https://vk.com/studmarket39" class="text-white me-2"><i class="bi bi-people-fill"></i></a>
                    <a href="https://t.me/StudMarket_bot" class="text-white"><i class="bi bi-telegram"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4 mb-md-0">
                <h5 class="navigation mb-3">Навигация</h5>
                <ul class="list-navigation list-unstyled">
                    <li class="mb-2"><a href="../profile.php" class="text-white text-decoration-none">Назад в кабинет</a></li>
                    <li class="mb-2"><a href="my_works.php" class="text-white text-decoration-none">Мои работы</a></li>
                    <li><a href="portfolio.php" class="text-white text-decoration-none">Добавить работу</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <h5 class="mb-3">Контакты</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> г. Калининград, ул.Брамса, д.9</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i> gaukokp@mail.ru</li>
                    <li><i class="bi bi-telephone me-2"></i> +7 (4012) 95-77-75</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5 class="mb-3">Подписаться</h5>
                <p>Будьте в курсе новых возможностей</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Ваш email">
                    <button class="btn btn-primary" type="button">OK</button>
                </div>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">© 2025 СтудМаркет. Все права защищены.</p>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() 
{
    var editWorkModal = document.getElementById('editWorkModal');

    if (editWorkModal) 
    {
        editWorkModal.addEventListener('show.bs.modal', function(event) 
        {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var title = button.getAttribute('data-title');
            var category = button.getAttribute('data-category');
            var description = button.getAttribute('data-description');
            var tags = button.getAttribute('data-tags');
            var image = button.getAttribute('data-image');
            var links = button.getAttribute('data-links');
            
            document.getElementById('editWorkId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editCategory').value = category;
            document.getElementById('editDescription').value = description;
            document.getElementById('editTags').value = tags;
            document.getElementById('editCurrentImage').src = image;
            
            try 
            {
                var linksObj;

                if (links) 
                {
                    linksObj = JSON.parse(links);
                } 
                else 
                {
                    linksObj = {};
                }

                document.getElementById('editBehanceLink').value = linksObj.Behance || '';
                document.getElementById('editGithubLink').value = linksObj.GitHub || '';
                document.getElementById('editDribbbleLink').value = linksObj.Dribbble || '';
                document.getElementById('editWebsiteLink').value = linksObj.Website || '';
            } 
            catch (e) 
            {
                console.error('Error parsing links:', e);
            }
        });
    }
});
</script>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>