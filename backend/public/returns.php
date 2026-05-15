<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Обмен и возврат';

$returnRules = [
    'Если заказ не был использован и сохранён товарный вид.',
    'Возврат и обмен осуществляется в течение двух дней с момента получения заказа.',
    'Возврат и обмен товара возможен только при отправке по Украине.',
    'Бланк возврата присутствует в каждой посылке.',
    'Все расходы по обмену и возврату товара несёт покупатель.',
    'После получения возврата мы делаем перевод денежных средств на карту клиента.',
];

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container">
        <div class="page-header">
            <div class="breadcrumbs">Главная › Обмен и возврат</div>
            <h1 class="page-title">Обмен и возврат</h1>
            <p class="page-subtitle">
                Эта страница во фронтенде полностью информационная, поэтому в учебной версии backend тоже остаётся статической.
            </p>
        </div>

        <div class="info-grid">
            <?php foreach ($returnRules as $rule): ?>
                <div class="info-card"><?= e($rule) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
