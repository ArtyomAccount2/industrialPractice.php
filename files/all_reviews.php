<?php
session_start();
require_once("../config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) 
{
    $rating = intval($_POST['rating']);
    $text = mysqli_real_escape_string($conn, $_POST['review_text']);
    $userId = $_SESSION['user_id'];
    
    $sql = "INSERT INTO reviews (user_id, rating, text, created_at) VALUES ('$userId', '$rating', '$text', NOW())";
    
    if (mysqli_query($conn, $sql)) 
    {
        $_SESSION['success'] = "Отзыв успешно добавлен!";
        header("Location: all_reviews.php");
        exit();
    } 
    else 
    {
        $_SESSION['error'] = "Ошибка при добавлении отзыва: " . mysqli_error($conn);
    }
}

if ($isLoggedIn && isset($_GET['action'])) 
{
    $reviewId = intval($_GET['id']);
    $userId = $_SESSION['user_id'];
    
    if ($_GET['action'] === 'like') 
    {
        $sql = "INSERT INTO likes (user_id, review_id) VALUES ('$userId', '$reviewId') ON DUPLICATE KEY UPDATE is_active = NOT is_active";
    } 
    else if ($_GET['action'] === 'unlike') 
    {
        $sql = "UPDATE likes SET is_active = 0 WHERE user_id = '$userId' AND review_id = '$reviewId'";
    }
    
    mysqli_query($conn, $sql);
    header("Location: all_reviews.php?page=" . $_GET['page']);
    exit();
}

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($current_page < 1) 
{
    $current_page = 1;
}

$limit = 10;
$offset = ($current_page - 1) * $limit;

$sql = "SELECT r.*, u.name as user_name, u.user_type, u.avatar_path, (SELECT COUNT(*) FROM likes WHERE review_id = r.id AND is_active = 1) as like_count, (SELECT SUM(like_currect) FROM likes WHERE review_id = r.id) as like_currect_sum FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT $limit OFFSET $offset";
$reviews = mysqli_query($conn, $sql);
$total_reviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reviews"))['count'];
$total_pages = ceil($total_reviews / $limit);

if ($current_page > $total_pages) 
{
    $current_page = $total_pages;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все отзывы | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php 
                    if ($isLoggedIn)
                    {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../profile.php">Главная</a>
                    </li>
                    <?php 
                    }
                    else
                    {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Главная</a>
                    </li>
                    <?php 
                    } 
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="portfolio.php">Портфолио</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cooperation.php">Сотрудничество</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="vacancies.php">Вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="all_reviews.php">Отзывы</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php 
                    if ($isLoggedIn)
                    {
                    ?>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Выйти
                        </a>
                    <?php 
                    }
                    else
                    {
                    ?>
                        <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Авторизация
                        </button>
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal">
                            <i class="bi bi-person-add me-2"></i>Регистрация
                        </button>
                    <?php 
                    } 
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="reviews-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Все отзывы о платформе</h2>
                <p class="lead text-muted">Мнения студентов и работодателей о СтудМаркет</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row g-4">
                <?php 
                while($review = mysqli_fetch_assoc($reviews))
                {
                    $isLiked = false;
                    $likeCount = $review['like_count'];
                    $likeTotal = $review['like_count'] + ($review['like_currect_sum'] ?? 0);
                    
                    if ($isLoggedIn) 
                    {
                        $checkLike = mysqli_query($conn, "SELECT * FROM likes WHERE user_id = '$_SESSION[user_id]' AND review_id = '{$review['id']}' AND is_active = 1");
                        $isLiked = mysqli_num_rows($checkLike) > 0;
                    }
                ?>
                <div class="col-lg-6">
                    <div class="review-card p-4 bg-white rounded-3 h-100">
                        <div class="review-header d-flex justify-content-between mb-3">
                            <div class="reviewer-info d-flex align-items-center">
                                <?php
                                $avatarPath = !empty($review['avatar_path']) ? $review['avatar_path'] : '../img/no-image.png';

                                if (!empty($review['avatar_path']) && strpos($review['avatar_path'], '../') === false) 
                                {
                                    $avatarPath = '../' . $review['avatar_path'];
                                }
                                ?>
                                <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Аватар" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($review['user_name']) ?></h6>
                                    <p class="text-muted small mb-0">
                                        <?= $review['user_type'] === 'employer' ? 'Работодатель' : 'Студент' ?>
                                    </p>
                                </div>
                            </div>
                            <div class="rating">
                                <div class="stars">
                                    <?php 
                                    for ($i = 1; $i <= 5; $i++) 
                                    { 
                                    ?>
                                        <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?> text-warning"></i>
                                    <?php 
                                    } 
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-body">
                            <p class="mb-3"><?= nl2br(htmlspecialchars($review['text'])) ?></p>
                        </div>
                        <div class="review-footer d-flex justify-content-between align-items-center">
                            <p class="text-muted small mb-0">
                                <?= date('d.m.Y', strtotime($review['created_at'])) ?>
                            </p>
                            <?php 
                            if ($isLoggedIn)
                            {
                            ?>
                                <a href="all_reviews.php?page=<?= $current_page ?>&action=<?= $isLiked ? 'unlike' : 'like' ?>&id=<?= $review['id'] ?>" 
                                class="btn btn-sm <?= $isLiked ? 'btn-primary' : 'btn-outline-primary' ?> like-btn">
                                    <i class="bi bi-hand-thumbs-up"></i> 
                                    <span class="like-count"><?= $likeTotal ?></span>
                                </a>
                            <?php 
                            }
                            else
                            {
                            ?>
                                <button class="btn btn-sm btn-outline-primary like-btn" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <i class="bi bi-hand-thumbs-up"></i> 
                                    <span class="like-count"><?= $likeTotal ?></span>
                                </button>
                            <?php 
                            } 
                            ?>
                        </div>
                    </div>
                </div>
                <?php 
                } 
                ?>
            </div>
            <div class="text-center mt-5">
                <?php 
                if ($isLoggedIn)
                {
                ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal">
                    <i class="bi bi-pencil-square me-2"></i>Добавить отзыв
                </button>
                <?php 
                }
                else
                {
                ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="bi bi-pencil-square me-2"></i>Добавить отзыв
                </button>
                <?php 
                }
                ?>
            </div>
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($current_page == 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="all_reviews.php?page=<?= $current_page - 1 ?>" tabindex="-1">Назад</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $total_pages; $i++)
                    {
                    ?>
                        <li class="page-item <?= ($current_page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="all_reviews.php?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    } 
                    ?>
                    <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="all_reviews.php?page=<?= $current_page + 1 ?>">Вперед</a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</div>

<?php
    require_once("modals.php");
?>

<div class="modal fade" id="addReviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReviewModalLabel">Оставить отзыв</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="all_reviews.php">
                <div class="modal-body">
                    <?php 
                    if (isset($_SESSION['error']))
                    {
                    ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php 
                    } 
                    ?>
                    <?php 
                    if (isset($_SESSION['success']))
                    {
                    ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php 
                    } 
                    ?>
                    <div class="mb-3">
                        <label class="form-label">Ваша оценка</label>
                        <div class="rating-input">
                            <input type="radio" id="star5" name="rating" value="5" required>
                            <label for="star5" class="bi bi-star-fill"></label>
                            <input type="radio" id="star4" name="rating" value="4">
                            <label for="star4" class="bi bi-star-fill"></label>
                            <input type="radio" id="star3" name="rating" value="3">
                            <label for="star3" class="bi bi-star-fill"></label>
                            <input type="radio" id="star2" name="rating" value="2">
                            <label for="star2" class="bi bi-star-fill"></label>
                            <input type="radio" id="star1" name="rating" value="1">
                            <label for="star1" class="bi bi-star-fill"></label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reviewText" class="form-label">Текст отзыва</label>
                        <textarea class="form-control" id="reviewText" name="review_text" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" name="add_review" class="btn btn-primary">Отправить отзыв</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    require_once("footer.php");
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>