import { useState } from "react";
import { Link } from "react-router-dom";
import { useShop } from "../context/useShop.jsx";

export default function HomePage() {
    const {
        categories,
        isCatalogLoading,
        catalogError,
        subscribeToNewsletter,
    } = useShop();
    const [email, setEmail] = useState("");
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

    async function handleSubscribe() {
        setMessage("");
        setError("");

        if (!email.trim()) {
            setError("Введите e-mail.");
            return;
        }

        setIsSubmitting(true);

        try {
            const result = await subscribeToNewsletter(email.trim());

            if (!result.ok) {
                setError(result.error);
                return;
            }

            setMessage(result.data.message);
            setEmail("");
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <>
            <section className="hero">
                <div className="hero__banner">
                    <div className="hero__center">
                        <div className="hero__headline">Новая коллекция</div>
                        <div className="hero__sub">СМОТРЕТЬ НОВИНКИ →</div>
                    </div>
                </div>
            </section>

            <section className="section container">
                <h2 className="section__title">Категории</h2>

                <div className="cats">
                    {isCatalogLoading ? (
                        <div className="text">Загружаем категории...</div>
                    ) : catalogError ? (
                        <div className="form-error">{catalogError}</div>
                    ) : (
                        categories.map((category) => (
                            <Link
                                className="cat"
                                key={category.id}
                                to={`/catalog?category=${encodeURIComponent(category.slug)}`}
                            >
                                <div
                                    className="cat__img"
                                    style={{
                                        backgroundImage: `url(${category.imageUrl})`,
                                    }}
                                />
                                <div className="cat__label">{category.name}</div>
                            </Link>
                        ))
                    )}
                </div>
            </section>

            <section className="section container center">
                <h2 className="subscribe__title">Узнайте первым о новинках</h2>

                <div className="subscribe">
                    <input
                        className="input"
                        placeholder="Ваш e-mail"
                        value={email}
                        onChange={(event) => setEmail(event.target.value)}
                    />
                    <button
                        className="btn btn--beige"
                        type="button"
                        onClick={handleSubscribe}
                        disabled={isSubmitting}
                    >
                        {isSubmitting ? "СОХРАНЯЕМ..." : "ПОДПИСАТЬСЯ"}
                    </button>
                </div>

                {error ? <div className="form-error form-error--inline">{error}</div> : null}
                {message ? <div className="form-success">{message}</div> : null}

                <div className="fineprint">
                    Нажимая на кнопку «Подписаться», я соглашаюсь на обработку моих
                    персональных данных и ознакомлен(а) с условиями конфиденциальности.
                </div>
            </section>
        </>
    );
}
