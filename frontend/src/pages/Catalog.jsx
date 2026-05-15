import {
    Link,
    useNavigate,
    useSearchParams,
} from "react-router-dom";
import { useShop } from "../context/useShop.jsx";
import { formatPrice } from "../utils/formatPrice.js";

export default function CatalogPage() {
    const navigate = useNavigate();
    const [searchParams, setSearchParams] = useSearchParams();
    const {
        isLoggedIn,
        favoriteIds,
        products,
        categories,
        isCatalogLoading,
        catalogError,
        toggleFavorite,
        addToCart,
    } = useShop();
    const activeCategory = searchParams.get("category") || "";
    const activeFlag = searchParams.get("flag") || "";
    const activeSort = searchParams.get("sort") || "newest";

    function updateFilter(key, value) {
        const next = new URLSearchParams(searchParams);

        if (!value) {
            next.delete(key);
        } else {
            next.set(key, value);
        }

        setSearchParams(next);
    }

    async function handleToggleFavorite(productId) {
        if (!isLoggedIn) {
            navigate("/login");
            return;
        }

        await toggleFavorite(productId);
    }

    async function handleAddToCart(productId) {
        if (!isLoggedIn) {
            navigate("/login");
            return;
        }

        await addToCart(productId);
    }

    let visibleProducts = [...products];

    if (activeCategory) {
        visibleProducts = visibleProducts.filter(
            (product) => product.category.slug === activeCategory
        );
    }

    if (activeFlag === "new") {
        visibleProducts = visibleProducts.filter((product) => product.isNew);
    } else if (activeFlag === "bestsellers") {
        visibleProducts = visibleProducts.filter((product) => product.isBestseller);
    } else if (activeFlag === "sale") {
        visibleProducts = visibleProducts.filter((product) => product.isSale);
    }

    if (activeSort === "price_asc") {
        visibleProducts.sort((left, right) => left.price - right.price);
    } else if (activeSort === "price_desc") {
        visibleProducts.sort((left, right) => right.price - left.price);
    } else if (activeSort === "title") {
        visibleProducts.sort((left, right) =>
            left.title.localeCompare(right.title, "ru")
        );
    } else {
        visibleProducts.sort((left, right) => right.id - left.id);
    }

    return (
        <section className="section container">
            <div className="breadcrumbs">Главная › Каталог</div>
            <h1 className="page__title">Каталог</h1>

            <div className="catalog">
                <aside className="sidebar">
                    <div className="sidebar__title">Каталог</div>
                    <button
                        className={`sidebar__item sidebar__item--button${
                            activeFlag === "new" ? " active" : ""
                        }`}
                        type="button"
                        onClick={() => updateFilter("flag", activeFlag === "new" ? "" : "new")}
                    >
                        New
                    </button>
                    <button
                        className={`sidebar__item sidebar__item--button${
                            activeFlag === "bestsellers" ? " active" : ""
                        }`}
                        type="button"
                        onClick={() =>
                            updateFilter(
                                "flag",
                                activeFlag === "bestsellers" ? "" : "bestsellers"
                            )
                        }
                    >
                        Bestsellers
                    </button>
                    <button
                        className={`sidebar__item sidebar__item--button${
                            activeFlag === "sale" ? " active" : ""
                        }`}
                        type="button"
                        onClick={() => updateFilter("flag", activeFlag === "sale" ? "" : "sale")}
                    >
                        Sale
                    </button>
                    {categories.map((category) => (
                        <button
                            className={`sidebar__item sidebar__item--button${
                                activeCategory === category.slug ? " active" : ""
                            }`}
                            key={category.id}
                            type="button"
                            onClick={() =>
                                updateFilter(
                                    "category",
                                    activeCategory === category.slug ? "" : category.slug
                                )
                            }
                        >
                            {category.name}
                        </button>
                    ))}
                    <button
                        className="sidebar__item sidebar__item--button"
                        type="button"
                        onClick={() => setSearchParams(new URLSearchParams())}
                    >
                        Сбросить всё
                    </button>
                </aside>

                <div className="catalog__content">
                    <div className="filters">
                        <div className="filter">
                            <span>Категория</span>
                            <select
                                className="filter-select"
                                value={activeCategory}
                                onChange={(event) =>
                                    updateFilter("category", event.target.value)
                                }
                            >
                                <option value="">Все категории</option>
                                {categories.map((category) => (
                                    <option key={category.id} value={category.slug}>
                                        {category.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div className="filter">
                            <span>Подборка</span>
                            <select
                                className="filter-select"
                                value={activeFlag}
                                onChange={(event) => updateFilter("flag", event.target.value)}
                            >
                                <option value="">Все товары</option>
                                <option value="new">New</option>
                                <option value="bestsellers">Bestsellers</option>
                                <option value="sale">Sale</option>
                            </select>
                        </div>
                        <div className="filter">
                            <span>Сортировка</span>
                            <select
                                className="filter-select"
                                value={activeSort}
                                onChange={(event) => updateFilter("sort", event.target.value)}
                            >
                                <option value="newest">Сначала новые</option>
                                <option value="price_asc">Цена по возрастанию</option>
                                <option value="price_desc">Цена по убыванию</option>
                                <option value="title">По названию</option>
                            </select>
                        </div>
                        <Link className="filter filter--reset" to="/catalog">
                            Сбросить
                        </Link>
                    </div>

                    {isCatalogLoading ? (
                        <div className="text">Загружаем товары...</div>
                    ) : catalogError ? (
                        <div className="form-error">{catalogError}</div>
                    ) : visibleProducts.length === 0 ? (
                        <div className="empty-state">
                            <h2 className="page__title">Ничего не найдено</h2>
                            <p className="text">
                                Попробуйте сбросить фильтры или открыть весь каталог.
                            </p>
                        </div>
                    ) : (
                        <div className="grid">
                            {visibleProducts.map((product) => (
                                <article className="product-card" key={product.id}>
                                    <div
                                        className="product-card__img"
                                        style={{
                                            backgroundImage: `url(${product.imageUrl})`,
                                        }}
                                    >
                                        <button
                                            className={`product-card__fav${
                                                favoriteIds.includes(product.id)
                                                    ? " product-card__fav--active"
                                                    : ""
                                            }`}
                                            type="button"
                                            onClick={() => handleToggleFavorite(product.id)}
                                            aria-label={`Добавить ${product.title} в избранное`}
                                        >
                                            {favoriteIds.includes(product.id) ? "♥" : "♡"}
                                        </button>
                                    </div>

                                    <div className="product-card__body">
                                        <div className="product-card__title">
                                            {product.title}
                                        </div>
                                        <div className="product-card__price">
                                            {formatPrice(product.price)}
                                        </div>
                                        <div className="product-card__meta-text">
                                            {product.category.name}
                                            {product.color.name
                                                ? ` · ${product.color.name}`
                                                : ""}
                                        </div>
                                        <div className="product-card__sizes">
                                            {product.sizes.join(" ")}
                                        </div>
                                        <div className="product-card__actions">
                                            <button
                                                className="btn btn--beige"
                                                type="button"
                                                onClick={() => handleAddToCart(product.id)}
                                            >
                                                В корзину
                                            </button>
                                            <button
                                                className="btn"
                                                type="button"
                                                onClick={() =>
                                                    handleToggleFavorite(product.id)
                                                }
                                            >
                                                {favoriteIds.includes(product.id)
                                                    ? "Убрать"
                                                    : "В избранное"}
                                            </button>
                                        </div>
                                    </div>
                                </article>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}
