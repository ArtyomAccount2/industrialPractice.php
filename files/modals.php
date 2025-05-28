<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Вход в систему</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="../files/login.php">
                <?php if (isset($_SESSION['error']))
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
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Войти</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
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
                        <label for="regName" class="form-label">Имя</label>
                        <input type="text" class="form-control" id="regName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="regEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="regEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="regPassword" class="form-label">Пароль</label>
                        <input type="password" class="form-control" id="regPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="regPhone" class="form-label">Телефон</label>
                        <input type="phone" class="form-control" id="regPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Вы регистрируетесь как:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type" id="studentType" value="student" checked>
                            <label class="form-check-label" for="studentType">
                                Студент
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="user_type" id="employerType" value="employer">
                            <label class="form-check-label" for="employerType">
                                Работодатель
                            </label>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agreeTerms" required>
                        <label class="form-check-label" for="agreeTerms">Я согласен с условиями использования</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                </div>
            </form>
        </div>
    </div>
</div>