import { useState } from "react";
import { Navigate, Link, useNavigate } from "react-router-dom";
import { useShop } from "../context/useShop.jsx";

export default function LoginPage() {
    const navigate = useNavigate();
    const { currentUser, isSessionLoading, loginUser } = useShop();
    const [form, setForm] = useState({
        email: "",
        password: "",
    });
    const [error, setError] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

    if (isSessionLoading) {
        return (
            <section className="section container">
                <div className="text">Проверяем текущую сессию...</div>
            </section>
        );
    }

    if (currentUser) {
        return <Navigate to="/profile" replace />;
    }

    async function handleSubmit(event) {
        event.preventDefault();
        setError("");

        const email = form.email.trim();

        if (!email || !email.includes("@")) {
            setError("Введите корректный e-mail.");
            return;
        }

        if (!form.password) {
            setError("Введите пароль.");
            return;
        }

        setIsSubmitting(true);

        try {
            const result = await loginUser(form);

            if (!result.ok) {
                setError(
                    result.fieldErrors.credentials ||
                        result.fieldErrors.email ||
                        result.fieldErrors.password ||
                        result.error
                );
                return;
            }

            navigate("/profile");
        } finally {
            setIsSubmitting(false);
        }
    }

    function updateField(event) {
        const { name, value } = event.target;
        setForm((current) => ({
            ...current,
            [name]: value,
        }));
    }

    return (
        <section className="section container">
            <div className="breadcrumbs">Главная › Вход</div>
            <div className="auth-shell">
                <div className="auth-card">
                    <h1 className="page__title">Вход в аккаунт</h1>
                    <p className="text">
                        Войдите, чтобы открыть профиль, сохранять избранное и
                        быстрее оформлять заказы.
                    </p>

                    <form className="form-stack" onSubmit={handleSubmit}>
                        <label className="form-field">
                            <span>E-mail</span>
                            <input
                                className="input"
                                type="email"
                                name="email"
                                value={form.email}
                                onChange={updateField}
                                placeholder="example@mail.com"
                                autoComplete="email"
                                required
                            />
                        </label>

                        <label className="form-field">
                            <span>Пароль</span>
                            <input
                                className="input"
                                type="password"
                                name="password"
                                value={form.password}
                                onChange={updateField}
                                placeholder="Введите пароль"
                                autoComplete="current-password"
                                required
                            />
                        </label>

                        {error ? <div className="form-error">{error}</div> : null}

                        <div className="form-actions">
                            <button
                                className="btn btn--beige"
                                type="submit"
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? "Входим..." : "Войти"}
                            </button>
                            <Link className="btn" to="/register">
                                Регистрация
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    );
}
