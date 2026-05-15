import { Link } from "react-router-dom";
import { useEffect, useState } from "react";
import { useShop } from "../context/useShop.jsx";

function formatDate(value) {
    return new Intl.DateTimeFormat("ru-RU", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(new Date(value));
}

export default function ProfilePage() {
    const {
        currentUser,
        favoriteCount,
        cartCount,
        isSessionLoading,
        logoutUser,
        updateProfile,
    } = useShop();
    const [form, setForm] = useState({
        name: currentUser?.name || "",
        phone: currentUser?.phone || "",
    });
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        setForm({
            name: currentUser?.name || "",
            phone: currentUser?.phone || "",
        });
    }, [currentUser]);

    if (isSessionLoading) {
        return (
            <section className="section container">
                <div className="text">Загружаем профиль...</div>
            </section>
        );
    }

    if (!currentUser) {
        return (
            <section className="section container">
                <div className="breadcrumbs">Главная › Профиль</div>
                <div className="empty-state">
                    <h1 className="page__title">Профиль недоступен</h1>
                    <p className="text">
                        Сначала войдите в аккаунт или создайте его. После этого
                        здесь появятся ваши данные, избранное и быстрые ссылки.
                    </p>
                    <div className="page-actions">
                        <Link className="btn btn--beige" to="/login">
                            Войти
                        </Link>
                        <Link className="btn" to="/register">
                            Регистрация
                        </Link>
                    </div>
                </div>
            </section>
        );
    }

    async function handleSubmit(event) {
        event.preventDefault();
        setMessage("");
        setError("");
        setIsSubmitting(true);

        try {
            const result = await updateProfile(form);

            if (!result.ok) {
                setError(
                    result.fieldErrors.name ||
                        result.fieldErrors.phone ||
                        result.error
                );
                return;
            }

            setMessage(result.data.message);
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <section className="section container">
            <div className="breadcrumbs">Главная › Профиль</div>
            <div className="section-head">
                <div>
                    <h1 className="page__title">Профиль</h1>
                    <p className="text">
                        Аккаунт создан {formatDate(currentUser.createdAt)}.
                    </p>
                </div>
                <button className="btn" type="button" onClick={logoutUser}>
                    Выйти
                </button>
            </div>

            <div className="account-grid">
                <article className="detail-card">
                    <div className="subheading">Контактные данные</div>
                    <form className="form-stack" onSubmit={handleSubmit}>
                        <label className="form-field">
                            <span>Имя</span>
                            <input
                                className="input"
                                type="text"
                                value={form.name}
                                onChange={(event) =>
                                    setForm((current) => ({
                                        ...current,
                                        name: event.target.value,
                                    }))
                                }
                            />
                        </label>

                        <label className="form-field">
                            <span>Телефон</span>
                            <input
                                className="input"
                                type="text"
                                value={form.phone}
                                onChange={(event) =>
                                    setForm((current) => ({
                                        ...current,
                                        phone: event.target.value,
                                    }))
                                }
                            />
                        </label>

                        <label className="form-field">
                            <span>E-mail</span>
                            <input
                                className="input"
                                type="email"
                                value={currentUser.email}
                                disabled
                            />
                        </label>

                        {error ? <div className="form-error">{error}</div> : null}
                        {message ? <div className="form-success">{message}</div> : null}

                        <div className="form-actions">
                            <button
                                className="btn btn--beige"
                                type="submit"
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? "Сохраняем..." : "Сохранить"}
                            </button>
                        </div>
                    </form>
                </article>

                <article className="detail-card">
                    <div className="subheading">Быстрый доступ</div>
                    <div className="quick-links">
                        <Link className="panel-link" to="/favorites">
                            Избранное
                            <span>{favoriteCount} товаров</span>
                        </Link>
                        <Link className="panel-link" to="/cart">
                            Корзина
                            <span>{cartCount} товаров</span>
                        </Link>
                        <Link className="panel-link" to="/catalog">
                            Каталог
                            <span>Перейти к покупкам</span>
                        </Link>
                    </div>
                </article>
            </div>
        </section>
    );
}
