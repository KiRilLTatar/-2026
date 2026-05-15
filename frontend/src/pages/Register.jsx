import { useState } from "react";
import { Navigate, Link, useNavigate } from "react-router-dom";
import { useShop } from "../context/useShop.jsx";

export default function RegisterPage() {
    const navigate = useNavigate();
    const { currentUser, isSessionLoading, registerUser } = useShop();
    const [form, setForm] = useState({
        name: "",
        email: "",
        phone: "",
        password: "",
        passwordConfirmation: "",
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

        if (form.name.trim().length < 2) {
            setError("Введите имя минимум из 2 символов.");
            return;
        }

        if (!form.email.trim() || !form.email.includes("@")) {
            setError("Введите корректный e-mail.");
            return;
        }

        if (form.phone.trim().length > 30) {
            setError("Телефон не должен быть длиннее 30 символов.");
            return;
        }

        if (form.password.length < 6) {
            setError("Пароль должен содержать минимум 6 символов.");
            return;
        }

        if (form.password !== form.passwordConfirmation) {
            setError("Пароли не совпадают.");
            return;
        }

        setIsSubmitting(true);

        try {
            const result = await registerUser(form);

            if (!result.ok) {
                setError(
                    result.fieldErrors.name ||
                        result.fieldErrors.email ||
                        result.fieldErrors.phone ||
                        result.fieldErrors.password ||
                        result.fieldErrors.passwordConfirmation ||
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
            <div className="breadcrumbs">Главная › Регистрация</div>
            <div className="auth-shell auth-shell--wide">
                <div className="auth-card">
                    <h1 className="page__title">Регистрация</h1>
                    <p className="text">
                        Создайте аккаунт, чтобы сохранить избранное, видеть
                        корзину и быстро возвращаться к товарам.
                    </p>

                    <form className="form-stack" onSubmit={handleSubmit}>
                        <div className="form-grid">
                            <label className="form-field">
                                <span>Имя</span>
                                <input
                                    className="input"
                                    type="text"
                                    name="name"
                                    value={form.name}
                                    onChange={updateField}
                                    placeholder="Ваше имя"
                                    autoComplete="name"
                                    required
                                />
                            </label>

                            <label className="form-field">
                                <span>Телефон</span>
                                <input
                                    className="input"
                                    type="text"
                                    name="phone"
                                    value={form.phone}
                                    onChange={updateField}
                                    placeholder="+7 (900) 000-00-00"
                                    autoComplete="tel"
                                />
                            </label>
                        </div>

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

                        <div className="form-grid">
                            <label className="form-field">
                                <span>Пароль</span>
                                <input
                                    className="input"
                                    type="password"
                                    name="password"
                                    value={form.password}
                                    onChange={updateField}
                                    placeholder="Минимум 6 символов"
                                    autoComplete="new-password"
                                    required
                                />
                            </label>

                            <label className="form-field">
                                <span>Повторите пароль</span>
                                <input
                                    className="input"
                                    type="password"
                                    name="passwordConfirmation"
                                    value={form.passwordConfirmation}
                                    onChange={updateField}
                                    placeholder="Повторите пароль"
                                    autoComplete="new-password"
                                    required
                                />
                            </label>
                        </div>

                        {error ? <div className="form-error">{error}</div> : null}

                        <div className="form-actions">
                            <button
                                className="btn btn--beige"
                                type="submit"
                                disabled={isSubmitting}
                            >
                                {isSubmitting
                                    ? "Создаём аккаунт..."
                                    : "Зарегистрироваться"}
                            </button>
                            <Link className="btn" to="/login">
                                Уже есть аккаунт
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    );
}
