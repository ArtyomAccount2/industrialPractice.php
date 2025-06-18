<?php
session_start();
require_once("../config/link.php");

$isLoggedIn = isset($_SESSION['user_id']);

$categories = [];
$sql_categories = "SELECT * FROM `vacancy_categories`";
$result_categories = mysqli_query($conn, $sql_categories);

while ($row = mysqli_fetch_assoc($result_categories)) 
{
    $categories[$row['id']] = $row['name'];
}

if (isset($_GET['category'])) 
{
    $category_filter = (int)$_GET['category'];
} 
else 
{
    $category_filter = 0;
}

$employment_filter = isset($_GET['employment']) ? $_GET['employment'] : '';

if (isset($_GET['search'])) 
{
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
} 
else 
{
    $search_query = '';
}

if (isset($_GET['page'])) 
{
    $current_page = (int)$_GET['page'];
} 
else 
{
    $current_page = 1;
}

if ($current_page < 1) 
{
    $current_page = 1;
}

$limit = 9;
$offset = ($current_page - 1) * $limit;

$sql_where = "WHERE 1=1";

if ($category_filter > 0) 
{
    $sql_where .= " AND v.category_id = $category_filter";
}

if (!empty($employment_filter)) 
{
    $sql_where .= " AND v.employment_type = '$employment_filter'";
}

if (!empty($search_query)) 
{
    $sql_where .= " AND (v.title LIKE '%$search_query%' OR v.description LIKE '%$search_query%' OR v.requirements LIKE '%$search_query%')";
}

$sql = "SELECT v.*, u.name as company_name, u.user_type, c.name as category_name FROM vacancies v JOIN users u ON v.user_id = u.id JOIN vacancy_categories c ON v.category_id = c.id $sql_where ORDER BY v.created_at DESC LIMIT $limit OFFSET $offset";
$vacancies = mysqli_query($conn, $sql);

$sql_total = "SELECT COUNT(*) as count FROM vacancies v $sql_where";
$total_result = mysqli_query($conn, $sql_total);
$total_items = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_items / $limit);

if ($current_page > $total_pages && $total_pages > 0) 
{
    $current_page = $total_pages;
}

$sql_where = "WHERE v.status = 'approved'";

if ($isLoggedIn && ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'moderator')) 
{
    $sql_where = "WHERE 1=1";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вакансии | СтудМаркет</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php if ($isLoggedIn) { echo '../profile.php'; } else { echo '../index.php'; } ?>">
                <img class="logo" src="../img/img5.png" alt="Логотип">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link active" aria-current="page" href="vacancies.php">Вакансии</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_reviews.php">Отзывы</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php 
                    if ($isLoggedIn)
                    {
                    ?>
                        <a href="../files/logout.php" class="btn btn-outline-danger">
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

    <section class="vacancies-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h2 class="display-5 fw-bold">Вакансии для студентов</h2>
                <p class="lead text-muted">Актуальные предложения от работодателей</p>
                <div class="divider mx-auto"></div>
            </div>
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="vacancies.php" class="row g-2">
                        <div class="col-md-4">
                            <select class="form-select" name="category">
                                <option value="0">Все категории</option>
                                <?php 
                                foreach ($categories as $id => $name)
                                {
                                ?>
                                    <option value="<?= $id ?>" <?= $category_filter == $id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($name) ?>
                                    </option>
                                <?php 
                                } 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="employment">
                                <option value="">Любая занятость</option>
                                <option value="full" <?= $employment_filter == 'full' ? 'selected' : '' ?>>Полная</option>
                                <option value="part" <?= $employment_filter == 'part' ? 'selected' : '' ?>>Частичная</option>
                                <option value="internship" <?= $employment_filter == 'internship' ? 'selected' : '' ?>>Стажировка</option>
                                <option value="remote" <?= $employment_filter == 'remote' ? 'selected' : '' ?>>Удалённая</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search" placeholder="Поиск по вакансиям..." value="<?= htmlspecialchars($search_query) ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Поиск</button>
                        </div>
                    </form>
                    <?php 
                    if ($isLoggedIn && $_SESSION['user_type'] == 'employer') 
                    {
                    ?>
                        <div class="col-md-12 mt-3 text-center">
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVacancyModal">
                                <i class="bi bi-plus-circle"></i> Добавить вакансию
                            </button>
                        </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
            <div class="row g-4">
                <?php 
                if (mysqli_num_rows($vacancies) > 0) 
                {
                ?>
                    <?php 
                    while ($vacancy = mysqli_fetch_assoc($vacancies))
                    {
                    ?>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <img src="<?= htmlspecialchars($vacancy['image_path'] ?: '../img/no-image.png') ?>" class="card-img-top portfolio-img" alt="<?= htmlspecialchars($vacancy['title']) ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary"><?= htmlspecialchars($vacancy['category_name']) ?></span>
                                        <p class="text-muted small border-0"><?= date('d.m.Y', strtotime($vacancy['created_at'])) ?></p>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($vacancy['title']) ?></h5>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-building"></i> <?= htmlspecialchars($vacancy['company_name']) ?>
                                        <span class="badge bg-<?php if ($vacancy['user_type'] == 'employer') { echo 'warning'; } ?>">
                                            <?php 
                                            if ($vacancy['user_type'] == 'employer') 
                                            { 
                                                echo 'Работодатель'; 
                                            } 
                                            ?>
                                        </span>
                                    </p>
                                    <div class="vacancy-meta mb-3">
                                        <p class="mb-1">
                                            <i class="bi bi-geo-alt"></i> 
                                            <?= htmlspecialchars($vacancy['location'] ?: 'Не указано') ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-cash-coin"></i> 
                                            <?= $vacancy['salary'] ? htmlspecialchars($vacancy['salary']) . ' ₽' : 'По договорённости' ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="bi bi-clock"></i> 
                                            <?php 
                                            switch($vacancy['employment_type']) 
                                            {
                                                case 'full': echo 'Полная занятость'; break;
                                                case 'part': echo 'Частичная занятость'; break;
                                                case 'internship': echo 'Стажировка'; break;
                                                case 'remote': echo 'Удалённая работа'; break;
                                                default: echo 'Не указано';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <p class="card-text">
                                        <?= mb_substr(htmlspecialchars($vacancy['description']), 0, 100) ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <a href="vacancy_details.php?id=<?= $vacancy['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            Подробнее
                                        </a>
                                        <?php 
                                        if ($isLoggedIn && $_SESSION['user_type'] == 'student') 
                                        {
                                        ?>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#applyModal<?= $vacancy['id'] ?>">
                                                Откликнуться
                                            </button>
                                        <?php 
                                        } 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        <?php 
                        if ($isLoggedIn && $_SESSION['user_type'] == 'student')
                        {
                        ?>
                            <div class="modal fade" id="applyModal<?= $vacancy['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Отклик на вакансию</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="../files/apply_vacancy.php">
                                            <input type="hidden" name="vacancy_id" value="<?= $vacancy['id'] ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Вакансия</label>
                                                    <input type="text" class="form-control" value="<?= htmlspecialchars($vacancy['title']) ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Компания</label>
                                                    <input type="text" class="form-control" value="<?= htmlspecialchars($vacancy['company_name']) ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="message" class="form-label">Сопроводительное письмо <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="resume" class="form-label">Резюме</label>
                                                    <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                                <button type="submit" class="btn btn-primary">Отправить отклик</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php
                        } 
                        ?>
                    <?php 
                    } 
                    ?>
                <?php 
                }
                else 
                { 
                ?>
                    <div class="col-12 text-center py-4">
                        <i class="bi bi-file-earmark-excel" style="font-size: 3rem;"></i>
                        <h4>Вакансии не найдены</h4>
                        <p>Попробуйте изменить параметры поиска</p>
                    </div>
                <?php 
                } 
                ?>
            </div>
            <?php 
            if ($total_pages > 1) 
            {
            ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($current_page == 1) { echo 'disabled'; } ?>">
                            <a class="page-link" href="vacancies.php?page=<?= $current_page - 1 ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= $employment_filter ? '&employment='.$employment_filter : '' ?><?= $search_query ? '&search='.$search_query : '' ?>">Назад</a>
                        </li>
                        <?php 
                        for ($i = 1; $i <= $total_pages; $i++) 
                        {
                        ?>
                            <li class="page-item <?php if ($current_page == $i) { echo 'active'; } ?>">
                                <a class="page-link" href="vacancies.php?page=<?= $i ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= $employment_filter ? '&employment='.$employment_filter : '' ?><?= $search_query ? '&search='.$search_query : '' ?>"><?= $i ?></a>
                            </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item <?php if ($current_page >= $total_pages) { echo 'disabled'; } ?>">
                            <a class="page-link" href="vacancies.php?page=<?= $current_page + 1 ?><?= $category_filter ? '&category='.$category_filter : '' ?><?= $employment_filter ? '&employment='.$employment_filter : '' ?><?= $search_query ? '&search='.$search_query : '' ?>">Вперед</a>
                        </li>
                    </ul>
                </nav>
            <?php 
            } 
            ?>
        </div>
    </section>
</div>

<?php 
if ($isLoggedIn && $_SESSION['user_type'] == 'employer') 
{
?>
<div class="modal fade" id="addVacancyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Мастер добавления вакансии</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/add_vacancy.php" enctype="multipart/form-data" id="vacancyForm">
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <ul class="nav nav-pills mb-4" id="vacancyWizard" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="vstep1-tab" type="button" role="tab">1. Основное</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="vstep2-tab" type="button" role="tab">2. Описание</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="vstep3-tab" type="button" role="tab">3. Требования</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link disabled" id="vstep4-tab" type="button" role="tab">4. Дополнительно</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="vacancyWizardContent">
                        <div class="tab-pane fade show active" id="vstep1" role="tabpanel" aria-labelledby="vstep1-tab">
                            <div class="mb-3">
                                <label for="title" class="form-label">Название вакансии <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                                <div class="invalid-feedback">Пожалуйста, укажите название вакансии</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Категория <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category_id" required>
                                        <?php 
                                        $categories = mysqli_query($conn, "SELECT * FROM `vacancy_categories`");

                                        while ($cat = mysqli_fetch_assoc($categories)) 
                                        {
                                        ?>
                                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php 
                                        } 
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите категорию</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="employment" class="form-label">Тип занятости <span class="text-danger">*</span></label>
                                    <select class="form-select" id="employment" name="employment_type" required>
                                        <option value="full">Полная занятость</option>
                                        <option value="part">Частичная занятость</option>
                                        <option value="internship">Стажировка</option>
                                        <option value="remote">Удалённая работа</option>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите тип занятости</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary next-step" data-next="vstep2">Далее <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="vstep2" role="tabpanel" aria-labelledby="vstep2-tab">
                            <div class="mb-3">
                                <label for="description" class="form-label">Описание вакансии <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                <div class="form-text">
                                    <span id="descriptionCounter">0</span>/1000 символов. Минимум 100 символов.
                                </div>
                                <div class="invalid-feedback">Описание должно содержать не менее 100 символов</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary prev-step" data-prev="vstep1"><i class="bi bi-arrow-left"></i> Назад</button>
                                <button type="button" class="btn btn-primary next-step" data-next="vstep3">Далее <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="vstep3" role="tabpanel" aria-labelledby="vstep3-tab">
                            <div class="mb-3">
                                <label for="requirements" class="form-label">Требования <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="requirements" name="requirements" rows="5" required></textarea>
                                <div class="form-text">
                                    <span id="requirementsCounter">0</span>/1000 символов. Минимум 50 символов.
                                </div>
                                <div class="invalid-feedback">Требования должны содержать не менее 50 символов</div>
                            </div>
                            <div class="mb-3">
                                <label for="benefits" class="form-label">Условия и бонусы</label>
                                <textarea class="form-control" id="benefits" name="benefits" rows="3"></textarea>
                                <div class="form-text">
                                    <span id="benefitsCounter">0</span>/500 символов.
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary prev-step" data-prev="vstep2"><i class="bi bi-arrow-left"></i> Назад</button>
                                <button type="button" class="btn btn-primary next-step" data-next="vstep4">Далее <i class="bi bi-arrow-right"></i></button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="vstep4" role="tabpanel" aria-labelledby="vstep4-tab">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="salary" class="form-label">Зарплата</label>
                                    <input type="text" class="form-control" id="salary" name="salary" placeholder="Например: 30000-50000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Местоположение</label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="Город или адрес">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="contacts" class="form-label">Контактная информация <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="contacts" name="contacts" rows="2" required></textarea>
                                <div class="invalid-feedback">Пожалуйста, укажите контактную информацию</div>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Изображение <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                <div class="form-text">Макс. размер: 2MB. Допустимые форматы: JPG, PNG.</div>
                                <div class="invalid-feedback">Пожалуйста, загрузите изображение</div>
                                <div class="mt-3 text-center" id="imagePreviewContainer" style="display:none;">
                                    <img id="imagePreview" src="#" alt="Предпросмотр" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary prev-step" data-prev="vstep3"><i class="bi bi-arrow-left"></i> Назад</button>
                                <button type="submit" class="btn btn-success">Опубликовать вакансию</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
} 
?>

<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let currentVacancyStep = 1;
    let totalVacancySteps = 4;

    function goToVacancyStep(step) 
    {
        document.querySelectorAll('#vacancyWizardContent .tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        document.querySelectorAll('#vacancyWizard .nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        document.getElementById(`vstep${step}`).classList.add('show', 'active');

        document.getElementById(`vstep${step}-tab`).classList.add('active');
        
        currentVacancyStep = step;
    }

    document.querySelectorAll('#vacancyForm .next-step').forEach(button => {
        button.addEventListener('click', function() 
        {
            if (validateVacancyStep(`vstep${currentVacancyStep}`)) 
            {
                let nextStep = currentVacancyStep + 1;

                if (nextStep <= totalVacancySteps) 
                {
                    document.getElementById(`vstep${nextStep}-tab`).classList.remove('disabled');
                    goToVacancyStep(nextStep);
                }
            }
        });
    });
    
    document.querySelectorAll('#vacancyForm .prev-step').forEach(button => {
        button.addEventListener('click', function() 
        {
            let prevStep = currentVacancyStep - 1;

            if (prevStep >= 1) 
            {
                goToVacancyStep(prevStep);
            }
        });
    });

    function validateVacancyStep(stepId) 
    {
        let isValid = true;
        let stepElement = document.getElementById(stepId);
        let requiredFields = stepElement.querySelectorAll('[required]');

        requiredFields.forEach(field => {
            if (!field.value.trim()) 
            {
                field.classList.add('is-invalid');
                isValid = false;
            } 
            else 
            {
                field.classList.remove('is-invalid');
            }
        });
 
        if (stepId === 'vstep1') 
        {
            let title = document.getElementById('title');

            if (title.value.length < 5 || title.value.length > 100) 
            {
                title.classList.add('is-invalid');
                isValid = false;
            }
        } 
        else if (stepId === 'vstep2') 
        {
            let description = document.getElementById('description');

            if (description.value.length < 100) 
            {
                description.classList.add('is-invalid');
                isValid = false;
            }
        } 
        else if (stepId === 'vstep3') 
        {
            let requirements = document.getElementById('requirements');

            if (requirements.value.length < 50) 
            {
                requirements.classList.add('is-invalid');
                isValid = false;
            }
        } 
        else if (stepId === 'vstep4') 
        {
            let contacts = document.getElementById('contacts');

            if (contacts.value.length < 10) 
            {
                contacts.classList.add('is-invalid');
                isValid = false;
            }
        }
        
        return isValid;
    }

    let descriptionField = document.getElementById('description');
    let descriptionCounter = document.getElementById('descriptionCounter');
    let requirementsField = document.getElementById('requirements');
    let requirementsCounter = document.getElementById('requirementsCounter');
    let benefitsField = document.getElementById('benefits');
    let benefitsCounter = document.getElementById('benefitsCounter');
    
    if (descriptionField && descriptionCounter) 
    {
        descriptionField.addEventListener('input', function() 
        {
            descriptionCounter.textContent = this.value.length;
        });
    }
    
    if (requirementsField && requirementsCounter) 
    {
        requirementsField.addEventListener('input', function() 
        {
            requirementsCounter.textContent = this.value.length;
        });
    }
    
    if (benefitsField && benefitsCounter) 
    {
        benefitsField.addEventListener('input', function() 
        {
            benefitsCounter.textContent = this.value.length;
        });
    }

    let imageInput = document.getElementById('image');
    let previewContainer = document.getElementById('imagePreviewContainer');
    let preview = document.getElementById('imagePreview');
    
    if (imageInput && previewContainer && preview) 
    {
        imageInput.addEventListener('change', function() 
        {
            if (this.files && this.files[0]) 
            {
                let reader = new FileReader();
                
                reader.onload = function(e) 
                {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            } 
            else 
            {
                previewContainer.style.display = 'none';
            }
        });
    }
    
    let vacancyForm = document.getElementById('vacancyForm');

    if (vacancyForm) 
    {
        vacancyForm.addEventListener('submit', function(e) 
        {
            let allValid = true;
            let steps = ['vstep1', 'vstep2', 'vstep3', 'vstep4'];
            
            steps.forEach(step => {
                if (!validateVacancyStep(step)) 
                {
                    allValid = false;
                    goToVacancyStep(parseInt(step.replace('vstep', '')));
                }
            });
            
            if (!allValid) 
            {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля корректно.');
            }
        });
    }
});
</script>
<?php 
    require_once("modals.php");
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../script.js"></script>
</body>
</html>