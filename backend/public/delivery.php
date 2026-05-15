<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Оплата и доставка';

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container">
        <div class="page-header">
            <div class="breadcrumbs">Главная › Оплата и доставка</div>
            <h1 class="page-title">Оплата и доставка</h1>
            <p class="page-subtitle">
                Эта страница на текущем фронте статическая, поэтому в минимальном backend она хранится прямо в PHP без отдельной таблицы.
            </p>
        </div>

        <div class="info-grid">
            <section class="panel">
                <div class="panel__body">
                    <h2 class="panel-title">Способы оплаты</h2>
                    <ul class="list">
                        <li>Наложенный платёж.</li>
                        <li>Денежный перевод Visa/MasterCard.</li>
                        <li>Полная предоплата.</li>
                    </ul>
                </div>
            </section>

            <section class="panel">
                <div class="panel__body">
                    <h2 class="panel-title">Процесс заказа</h2>
                    <div class="steps">
                        <div class="step">01 — Выбрать способ оплаты.</div>
                        <div class="step">02 — Подтвердить заказ.</div>
                        <div class="step">03 — Оплатить на странице сервиса.</div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel__body">
                    <h2 class="panel-title">Доставка</h2>
                    <ul class="list">
                        <li>По России — 1–2 дня.</li>
                        <li>Международная — 3–7 дней.</li>
                        <li>Срок обработки — 1–2 дня.</li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
