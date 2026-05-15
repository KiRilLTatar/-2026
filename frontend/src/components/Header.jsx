import { NavLink, Link } from "react-router-dom";
import { useShop } from "../context/useShop.jsx";

export default function Header() {
    const { currentUser, favoriteCount, cartCount, isSessionLoading, logoutUser } = useShop();

    function actionClassName({ isActive }) {
        return `action-link${isActive ? " active" : ""}`;
    }

    return (
        <header className="header">
            <div className="container header__in">
                <button className="burger" type="button" aria-label="Меню">
                    ☰
                </button>

                <nav className="nav">
                    <NavLink className="nav__link" to="/" end>
                        NEW
                    </NavLink>
                    <NavLink className="nav__link" to="/catalog">
                        КАТАЛОГ
                    </NavLink>
                    <NavLink className="nav__link" to="/delivery">
                        ОПЛАТА
                    </NavLink>
                    <NavLink className="nav__link" to="/returns">
                        ВОЗВРАТ
                    </NavLink>
                </nav>

                <Link className="logo" to="/">
                    YANKI
                </Link>

                <div className="actions">
                    <div className="actions__meta">
                        <span className="actions__text">RU</span>
                        <span className="actions__dot">•</span>
                        <span className="actions__text">RUB</span>
                    </div>

                    <div className="actions__icons actions__icons--links">
                        <NavLink className={actionClassName} to="/catalog">
                            Поиск
                        </NavLink>
                        <NavLink className={actionClassName} to="/favorites">
                            Избранное
                            <span className="action-count">{favoriteCount}</span>
                        </NavLink>
                        <NavLink className={actionClassName} to="/cart">
                            Корзина
                            <span className="action-count">{cartCount}</span>
                        </NavLink>

                        {isSessionLoading ? (
                            <span className="action-link action-link--muted">
                                Загрузка...
                            </span>
                        ) : currentUser ? (
                            <>
                                <NavLink className={actionClassName} to="/profile">
                                    Профиль
                                </NavLink>
                                <button
                                    className="action-link action-link--button"
                                    type="button"
                                    onClick={() => {
                                        void logoutUser();
                                    }}
                                >
                                    Выйти
                                </button>
                            </>
                        ) : (
                            <>
                                <NavLink className={actionClassName} to="/login">
                                    Войти
                                </NavLink>
                                <NavLink className={actionClassName} to="/register">
                                    Регистрация
                                </NavLink>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
}
