import { Link, useNavigate } from "react-router-dom";
import { useShop } from "../context/useShop.jsx";
import { formatPrice } from "../utils/formatPrice.js";

export default function FavoritesPage() {
    const navigate = useNavigate();
    const {
        currentUser,
        favoriteProducts,
        isSessionLoading,
        isCatalogLoading,
        toggleFavorite,
        addToCart,
    } = useShop();

    if (isSessionLoading || isCatalogLoading) {
        return (
            <section className="section container">
                <div className="text">Загружаем избранное...</div>
            </section>
        );
    }

    if (!currentUser) {
        return (
            <section className="section container">
                <div className="breadcrumbs">Главная › Избранное</div>
                <div className="empty-state">
                    <h1 className="page__title">Избранное доступно после входа</h1>
                    <p className="text">
                        Войдите в аккаунт, чтобы сохранять понравившиеся вещи и
                        возвращаться к ним позже.
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

    if (favoriteProducts.length === 0) {
        return (
            <section className="section container">
                <div className="breadcrumbs">Главная › Избранное</div>
                <div className="empty-state">
                    <h1 className="page__title">В избранном пока пусто</h1>
                    <p className="text">
                        Откройте каталог и нажмите на сердечко на карточке товара.
                    </p>
                    <div className="page-actions">
                        <Link className="btn btn--beige" to="/catalog">
                            Открыть каталог
                        </Link>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className="section container">
            <div className="breadcrumbs">Главная › Избранное</div>
            <div className="section-head">
                <div>
                    <h1 className="page__title">Избранное</h1>
                    <p className="text">
                        Здесь собраны товары, которые вы отметили сердечком.
                    </p>
                </div>
                <button className="btn" type="button" onClick={() => navigate("/catalog")}>
                    Вернуться в каталог
                </button>
            </div>

            <div className="grid">
                {favoriteProducts.map((product) => (
                    <article className="product-card" key={product.id}>
                        <div
                            className="product-card__img"
                            style={{ backgroundImage: `url(${product.imageUrl})` }}
                        >
                            <button
                                className="product-card__fav product-card__fav--active"
                                type="button"
                                onClick={() => toggleFavorite(product.id)}
                                aria-label={`Убрать ${product.title} из избранного`}
                            >
                                ♥
                            </button>
                        </div>

                        <div className="product-card__body">
                            <div className="product-card__title">{product.title}</div>
                            <div className="product-card__price">
                                {formatPrice(product.price)}
                            </div>
                            <div className="product-card__sizes">
                                {product.sizes.join(" ")}
                            </div>

                            <div className="product-card__actions">
                                <button
                                    className="btn btn--beige"
                                    type="button"
                                    onClick={async () => {
                                        const result = await addToCart(product.id);

                                        if (!result.ok && result.status === 401) {
                                            navigate("/login");
                                        }
                                    }}
                                >
                                    В корзину
                                </button>
                                <button
                                    className="btn"
                                    type="button"
                                    onClick={() => toggleFavorite(product.id)}
                                >
                                    Убрать
                                </button>
                            </div>
                        </div>
                    </article>
                ))}
            </div>
        </section>
    );
}
