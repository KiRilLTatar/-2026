import { Link } from "react-router-dom";
import { useShop } from "../context/useShop.jsx";
import { formatPrice } from "../utils/formatPrice.js";

export default function CartPage() {
    const {
        currentUser,
        cartItemsDetailed,
        isSessionLoading,
        isCatalogLoading,
        updateCartQuantity,
        removeFromCart,
        clearCart,
    } = useShop();
    const cartProducts = cartItemsDetailed;

    const total = cartProducts.reduce((sum, item) => sum + item.total, 0);

    if (isSessionLoading || isCatalogLoading) {
        return (
            <section className="section container">
                <div className="text">Загружаем корзину...</div>
            </section>
        );
    }

    if (!currentUser) {
        return (
            <section className="section container">
                <div className="breadcrumbs">Главная › Корзина</div>
                <div className="empty-state">
                    <h1 className="page__title">Корзина доступна после входа</h1>
                    <p className="text">
                        Войдите или зарегистрируйтесь, чтобы собирать товары в
                        корзину и хранить их на стороне сервера.
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

    if (cartProducts.length === 0) {
        return (
            <section className="section container">
                <div className="breadcrumbs">Главная › Корзина</div>
                <div className="empty-state">
                    <h1 className="page__title">Корзина пока пуста</h1>
                    <p className="text">
                        Добавьте товары из каталога, и они появятся здесь.
                    </p>
                    <div className="page-actions">
                        <Link className="btn btn--beige" to="/catalog">
                            Перейти в каталог
                        </Link>
                    </div>
                </div>
            </section>
        );
    }

    return (
        <section className="section container">
            <div className="breadcrumbs">Главная › Корзина</div>
            <div className="section-head">
                <div>
                    <h1 className="page__title">Корзина</h1>
                    <p className="text">
                        Проверьте состав заказа и количество товаров перед
                        оформлением.
                    </p>
                </div>
                <button className="btn" type="button" onClick={clearCart}>
                    Очистить корзину
                </button>
            </div>

            <div className="cart-layout">
                <div className="cart-list">
                    {cartProducts.map((product) => (
                        <article className="cart-item" key={product.id}>
                            <div
                                className="cart-item__image"
                                style={{
                                    backgroundImage: `url(${product.imageUrl})`,
                                }}
                            />

                            <div className="cart-item__content">
                                <div className="cart-item__title">{product.title}</div>
                                <div className="cart-item__meta">
                                    Размеры: {product.sizes.join(" ")}
                                </div>
                                <div className="cart-item__price">
                                    {formatPrice(product.price)}
                                </div>
                            </div>

                            <div className="cart-item__controls">
                                <div className="qty-control">
                                    <button
                                        type="button"
                                        onClick={() =>
                                            updateCartQuantity(product.id, product.quantity - 1)
                                        }
                                    >
                                        −
                                    </button>
                                    <span>{product.quantity}</span>
                                    <button
                                        type="button"
                                        onClick={() =>
                                            updateCartQuantity(product.id, product.quantity + 1)
                                        }
                                    >
                                        +
                                    </button>
                                </div>

                                <div className="cart-item__total">
                                    {formatPrice(product.total)}
                                </div>

                                <button
                                    className="btn btn--small"
                                    type="button"
                                    onClick={() => removeFromCart(product.id)}
                                >
                                    Удалить
                                </button>
                            </div>
                        </article>
                    ))}
                </div>

                <aside className="summary-card">
                    <div className="subheading">Ваш заказ</div>
                    <div className="summary-row">
                        <span>Товаров</span>
                        <strong>{cartProducts.length}</strong>
                    </div>
                    <div className="summary-row">
                        <span>Итого</span>
                        <strong>{formatPrice(total)}</strong>
                    </div>
                    <p className="text">
                        Макет оформления уже подготовлен: корзина считает сумму,
                        хранит количество и позволяет удалить позиции.
                    </p>
                    <Link className="btn btn--beige" to="/profile">
                        Перейти в профиль
                    </Link>
                </aside>
            </div>
        </section>
    );
}
