<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Вход в систему</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/login.php">
                <input type="hidden" name="referer" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <?php 
                if (isset($_SESSION['error']))
                { 
                ?>
                    <div class="alert alert-danger m-3">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php 
                } 
                ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Пароль <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                        <div class="text-end mt-1">
                            <a href="../files/forgot_password.php" class="text-decoration-none">Забыли пароль?</a>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>
                    <div class="social-login text-center mt-4">
                        <p class="text-muted">Или войти через:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../files/vk_auth.php?action=login" class="btn btn-outline-primary">
                                <i class="bi bi-people-fill"></i> ВКонтакте
                            </a>
                            <a href="../files/telegram_auth.php?action=login" class="btn btn-outline-info">
                                <i class="bi bi-telegram"></i> Telegram
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Войти</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Регистрация</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/register.php">
                <input type="hidden" name="referer" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                <?php 
                if (isset($_SESSION['error_register']))
                { 
                ?>
                    <div class="alert alert-danger m-3">
                        <?php echo $_SESSION['error_register']; unset($_SESSION['error_register']); ?>
                    </div>
                <?php 
                } 
                ?>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="pe-3">
                        <div class="mb-3">
                            <label for="regName" class="form-label">Имя <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="regName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="regEmail" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="regEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="regPassword" class="form-label">Пароль <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="regPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="regPhone" class="form-label">Телефон <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="regPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="userTypeSelect" class="form-label">Вы регистрируетесь как: <span class="text-danger">*</span></label>
                            <select class="form-select" id="userTypeSelect" name="user_type" required>
                                <option value="student" selected>Студент</option>
                                <option value="employer">Работодатель (требуется верификация)</option>
                            </select>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agreeTerms" name="agree_terms" required>
                            <label class="form-check-label" for="agreeTerms">Я согласен с <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">условиями использования</a> <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="social-register text-center mt-4 mb-2">
                        <p class="text-muted small border-0">Или зарегистрироваться через:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../files/vk_auth.php?action=register" class="btn btn-outline-primary">
                                <i class="bi bi-people-fill"></i> ВКонтакте
                            </a>
                            <a href="../files/telegram_auth.php?action=register" class="btn btn-outline-info">
                                <i class="bi bi-telegram"></i> Telegram
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Условия использования платформы СтудМаркет</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <div class="pe-3">
                    <h6 class="fw-bold">1. Общие положения</h6>
                    <p>1.1. Платформа СтудМаркет предназначена для взаимодействия студентов Колледжа предпринимательства с потенциальными работодателями.</p>
                    <p>1.2. Используя платформу, вы соглашаетесь с настоящими условиями.</p>
                    
                    <h6 class="fw-bold mt-4">2. Регистрация и учетные записи</h6>
                    <p>2.1. Для использования всех функций платформы необходима регистрация.</p>
                    <p>2.2. Вы обязаны предоставлять достоверную информацию при регистрации.</p>
                    <p>2.3. Запрещено создавать несколько учетных записей для одного пользователя.</p>
                    
                    <h6 class="fw-bold mt-4">3. Конфиденциальность</h6>
                    <p>3.1. Мы защищаем ваши персональные данные в соответствии с законодательством РФ.</p>
                    <p>3.2. Ваши работы и портфолио могут быть видны другим пользователям платформы.</p>
                    
                    <h6 class="fw-bold mt-4">4. Правила поведения</h6>
                    <p>4.1. Запрещено размещение незаконного, оскорбительного или вредоносного контента.</p>
                    <p>4.2. Не допускается спам, флуд и другие нарушения сетевого этикета.</p>
                    <p>4.3. Работодатели обязуются использовать платформу только для законного поиска сотрудников.</p>
                    
                    <h6 class="fw-bold mt-4">5. Интеллектуальная собственность</h6>
                    <p>5.1. Вы сохраняете все права на работы, размещенные в вашем портфолио.</p>
                    <p>5.2. Размещая работы, вы разрешаете их просмотр другим пользователям платформы.</p>
                    
                    <h6 class="fw-bold mt-4">6. Ответственность</h6>
                    <p>6.1. Администрация не несет ответственности за содержание переписки между пользователями.</p>
                    <p>6.2. Мы не гарантируем трудоустройство через платформу.</p>
                    <p>6.3. Администрация оставляет право блокировать учетные записи за нарушения.</p>
                    
                    <h6 class="fw-bold mt-4">7. Изменения условий</h6>
                    <p>7.1. Условия могут быть изменены с уведомлением пользователей.</p>
                    <p>7.2. Продолжение использования платформы означает согласие с новыми условиями.</p>
                    
                    <div class="alert alert-info mt-4">
                        <p class="mb-0">Дата последнего обновления: 31.05.2025</p>
                        <p class="mb-0">По всем вопросам обращайтесь: gaukokp@mail.ru</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#registerModal" data-bs-toggle="modal" class="btn btn-primary" data-bs-dismiss="modal">Я ознакомлен(а)</a>
            </div>
        </div>
    </div>
</div>