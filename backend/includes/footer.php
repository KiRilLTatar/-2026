<?php
declare(strict_types=1);
?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <div class="footer-title">Компания</div>
            <p class="footer-text">Учебный PHP-backend для интернет-каталога одежды.</p>
        </div>
        <div>
            <div class="footer-title">Полезное</div>
            <a href="<?= e(url('delivery.php')) ?>">Оплата и доставка</a>
            <a href="<?= e(url('returns.php')) ?>">Условия возврата</a>
            <a href="<?= e(url('catalog.php?flag=new')) ?>">Новинки</a>
        </div>
        <div>
            <div class="footer-title">Покупателю</div>
            <a href="<?= e(url('favorites.php')) ?>">Избранное</a>
            <a href="<?= e(url('profile.php')) ?>">Профиль</a>
            <a href="<?= e(url('register.php')) ?>">Регистрация</a>
        </div>
        <div>
            <div class="footer-title">Контакты</div>
            <p class="footer-text">+38 (073) 096 36 44</p>
            <p class="footer-text">info@yanki.com</p>
        </div>
    </div>
    <div class="footer-bottom">© 2026 Yanki. Учебный проект на чистом PHP.</div>
</footer>
</body>
</html>
