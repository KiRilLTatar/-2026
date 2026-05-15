import { Link } from "react-router-dom";

export default function Footer() {
    return (
        <footer className="footer">
            <div className="container footer__grid">
                <div className="footer__col">
                    <div className="footer__title">КОМПАНИЯ</div>
                    <Link className="footer__link" to="/">
                        О нас
                    </Link>
                    <Link className="footer__link" to="/">
                        Контакты
                    </Link>
                </div>

                <div className="footer__col">
                    <div className="footer__title">ПОЛЕЗНОЕ</div>
                    <Link className="footer__link" to="/delivery">
                        Оплата и доставка
                    </Link>
                    <Link className="footer__link" to="/returns">
                        Условия возврата
                    </Link>
                    <Link className="footer__link" to="/">
                        Бонусная система
                    </Link>
                </div>

                <div className="footer__col">
                    <div className="footer__title">ПОКУПАТЕЛЮ</div>
                    <Link className="footer__link" to="/favorites">
                        Избранное
                    </Link>
                    <Link className="footer__link" to="/cart">
                        Корзина
                    </Link>
                    <Link className="footer__link" to="/">
                        Публичная оферта
                    </Link>
                    <Link className="footer__link" to="/">
                        Политика конфиденциальности
                    </Link>
                </div>

                <div className="footer__col">
                    <div className="footer__title">КОНТАКТЫ</div>
                    <div className="footer__contact">+7 (900) 123 45 67</div>
                    <div className="footer__contact">info@yanki.com</div>
                </div>
            </div>

            <div className="footer__bottom">© 2026 Yanki. All rights reserved</div>
        </footer>
    );
}
