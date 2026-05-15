export default function DeliveryPage() {
    return (
        <section className="section container">
            <div className="breadcrumbs">Главная › Оплата и доставка</div>
            <h1 className="page__title">Оплата и доставка</h1>

            <div className="two-cols">
                <div className="two-cols__col">
                    <div className="subheading">Вы можете оплатить покупку одним из способов:</div>
                    <ul className="list">
                        <li>Наложенным платежом</li>
                        <li>Денежным переводом Visa/MasterCard</li>
                        <li>Полной предоплатой</li>
                    </ul>

                    <div className="subheading">Процесс оформления заказа:</div>
                    <div className="steps">
                        <div className="step">01 — Выбрать способ оплаты</div>
                        <div className="step">02 — Подтвердить заказ</div>
                        <div className="step">03 — Оплатить на странице сервиса</div>
                    </div>
                </div>

                <div className="two-cols__col">
                    <div className="subheading">Мы готовы предложить несколько вариантов доставки:</div>
                    <ul className="list">
                        <li>По России — 1–2 дня</li>
                        <li>Международная — 3–7 дней</li>
                        <li>Срок обработки — 1–2 дня</li>
                    </ul>
                </div>
            </div>
        </section>
    );
}