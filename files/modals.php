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
                    <div class="alert alert-danger m-3"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php 
                } 
                ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                        <div class="text-end mt-1">
                            <a href="#forgotPasswordModal" data-bs-toggle="modal" data-bs-dismiss="modal" class="text-decoration-none">Забыли пароль?</a>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>
                    <div class="social-login text-center mt-4">
                        <p class="text-muted">Или войти через:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../files/social_auth.php?provider=vk" class="btn btn-outline-primary">
                                <i class="bi bi-people-fill"></i> ВКонтакте
                            </a>
                            <a href="../files/social_auth.php?provider=telegram" class="btn btn-outline-info">
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
                    <div class="alert alert-danger m-3"><?php echo $_SESSION['error_register']; unset($_SESSION['error_register']); ?></div>
                <?php 
                } 
                ?>
                <div class="modal-body">
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
                    <div class="social-register text-center mt-4">
                        <p class="text-muted small border-0">Или зарегистрироваться через:</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../files/social_auth.php?provider=vk&action=register" class="btn btn-outline-primary">
                                <i class="bi bi-people-fill"></i> ВКонтакте
                            </a>
                            <a href="../files/social_auth.php?provider=telegram&action=register" class="btn btn-outline-info">
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

<div class="modal fade" id="forgotPasswordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Восстановление пароля</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/forgot_password.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="forgotEmail" class="form-label">Введите ваш Email</label>
                        <input type="email" class="form-control" id="forgotEmail" name="email" required>
                    </div>
                    <div class="alert alert-info">
                        На указанный email будет отправлена ссылка для сброса пароля.
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal" class="btn btn-secondary" data-bs-dismiss="modal">Назад</a>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>