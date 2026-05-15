import { useEffect, useState } from "react";
import ShopContext from "./shopContextValue.js";
import { apiGet, apiPost, ApiError } from "../api/client.js";

const DEFAULT_STATE = {
    isBootstrapping: true,
    isSessionLoading: true,
    isCatalogLoading: true,
    sessionError: "",
    catalogError: "",
    csrfToken: "",
    currentUser: null,
    categories: [],
    colors: [],
    sizes: [],
    products: [],
    favoriteIds: [],
    cart: [],
    favoriteCount: 0,
    cartCount: 0,
};

function normalizeSession(session) {
    return {
        csrfToken: typeof session?.csrfToken === "string" ? session.csrfToken : "",
        currentUser: session?.user ?? null,
        favoriteIds: Array.isArray(session?.favoriteIds) ? session.favoriteIds : [],
        cart: Array.isArray(session?.cart) ? session.cart : [],
        favoriteCount: Number(session?.counts?.favorites ?? 0),
        cartCount: Number(session?.counts?.cart ?? 0),
    };
}

function normalizeCatalog(catalog) {
    return {
        categories: Array.isArray(catalog?.categories) ? catalog.categories : [],
        colors: Array.isArray(catalog?.colors) ? catalog.colors : [],
        sizes: Array.isArray(catalog?.sizes) ? catalog.sizes : [],
        products: Array.isArray(catalog?.products) ? catalog.products : [],
    };
}

function errorMessage(error, fallback) {
    return error instanceof Error ? error.message : fallback;
}

export function ShopProvider({ children }) {
    const [state, setState] = useState(DEFAULT_STATE);

    function applySession(session) {
        setState((current) => ({
            ...current,
            ...normalizeSession(session),
            isSessionLoading: false,
            sessionError: "",
        }));
    }

    function applyCatalog(catalog) {
        setState((current) => ({
            ...current,
            ...normalizeCatalog(catalog),
            isCatalogLoading: false,
            catalogError: "",
        }));
    }

    async function loadSession() {
        const payload = await apiGet("/session.php");
        applySession(payload.session);
        return payload.session;
    }

    async function loadCatalog() {
        const payload = await apiGet("/catalog.php");
        applyCatalog(payload.catalog);
        return payload.catalog;
    }

    useEffect(() => {
        let cancelled = false;

        async function bootstrap() {
            const [sessionResult, catalogResult] = await Promise.allSettled([
                apiGet("/session.php"),
                apiGet("/catalog.php"),
            ]);

            if (cancelled) {
                return;
            }

            setState((current) => {
                const next = {
                    ...current,
                    isBootstrapping: false,
                    isSessionLoading: false,
                    isCatalogLoading: false,
                };

                if (sessionResult.status === "fulfilled") {
                    Object.assign(next, normalizeSession(sessionResult.value.session), {
                        sessionError: "",
                    });
                } else {
                    next.sessionError = errorMessage(
                        sessionResult.reason,
                        "Не удалось загрузить сессию."
                    );
                }

                if (catalogResult.status === "fulfilled") {
                    Object.assign(next, normalizeCatalog(catalogResult.value.catalog), {
                        catalogError: "",
                    });
                } else {
                    next.catalogError = errorMessage(
                        catalogResult.reason,
                        "Не удалось загрузить каталог."
                    );
                }

                return next;
            });
        }

        bootstrap();

        return () => {
            cancelled = true;
        };
    }, []);

    async function ensureCsrfToken() {
        if (state.csrfToken) {
            return state.csrfToken;
        }

        const session = await loadSession();
        return session.csrfToken || "";
    }

    function makeResult(error, fallback) {
        if (error instanceof ApiError) {
            return {
                ok: false,
                error: error.message,
                fieldErrors: error.errors || {},
                status: error.status,
            };
        }

        return {
            ok: false,
            error: errorMessage(error, fallback),
            fieldErrors: {},
            status: 500,
        };
    }

    async function submit(path, payload, fallbackMessage) {
        try {
            const csrfToken = await ensureCsrfToken();
            const response = await apiPost(path, payload, csrfToken);

            if (response.session) {
                applySession(response.session);
            }

            return {
                ok: true,
                data: response,
            };
        } catch (error) {
            return makeResult(error, fallbackMessage);
        }
    }

    async function registerUser(payload) {
        return submit("/auth/register.php", payload, "Не удалось создать аккаунт.");
    }

    async function loginUser(payload) {
        return submit("/auth/login.php", payload, "Не удалось войти в аккаунт.");
    }

    async function logoutUser() {
        if (!state.currentUser) {
            return { ok: true };
        }

        return submit("/auth/logout.php", {}, "Не удалось выйти из аккаунта.");
    }

    async function updateProfile(payload) {
        if (!state.currentUser) {
            return {
                ok: false,
                error: "Сначала войдите в аккаунт.",
                fieldErrors: {},
                status: 401,
            };
        }

        return submit(
            "/profile/update.php",
            payload,
            "Не удалось обновить профиль."
        );
    }

    async function toggleFavorite(productId) {
        if (!state.currentUser) {
            return {
                ok: false,
                error: "Сначала войдите в аккаунт.",
                fieldErrors: {},
                status: 401,
            };
        }

        return submit(
            "/favorites/toggle.php",
            { productId },
            "Не удалось обновить избранное."
        );
    }

    async function addToCart(productId) {
        if (!state.currentUser) {
            return {
                ok: false,
                error: "Сначала войдите в аккаунт.",
                fieldErrors: {},
                status: 401,
            };
        }

        return submit("/cart/add.php", { productId }, "Не удалось добавить товар в корзину.");
    }

    async function updateCartQuantity(productId, quantity) {
        if (!state.currentUser) {
            return {
                ok: false,
                error: "Сначала войдите в аккаунт.",
                fieldErrors: {},
                status: 401,
            };
        }

        return submit(
            "/cart/update.php",
            { productId, quantity },
            "Не удалось обновить корзину."
        );
    }

    async function removeFromCart(productId) {
        return updateCartQuantity(productId, 0);
    }

    async function clearCart() {
        if (!state.currentUser) {
            return {
                ok: false,
                error: "Сначала войдите в аккаунт.",
                fieldErrors: {},
                status: 401,
            };
        }

        return submit("/cart/clear.php", {}, "Не удалось очистить корзину.");
    }

    async function subscribeToNewsletter(email) {
        return submit(
            "/subscribe.php",
            { email },
            "Не удалось сохранить подписку."
        );
    }

    const favoriteProducts = state.products.filter((product) =>
        state.favoriteIds.includes(product.id)
    );
    const cartItemsDetailed = state.cart
        .map((item) => {
            const product =
                state.products.find((entry) => entry.id === item.productId) ?? null;

            if (!product) {
                return null;
            }

            return {
                ...product,
                quantity: item.quantity,
                total: product.price * item.quantity,
            };
        })
        .filter(Boolean);

    return (
        <ShopContext.Provider
            value={{
                ...state,
                isLoggedIn: state.currentUser !== null,
                favoriteProducts,
                cartItemsDetailed,
                loadSession,
                loadCatalog,
                registerUser,
                loginUser,
                logoutUser,
                updateProfile,
                toggleFavorite,
                addToCart,
                updateCartQuantity,
                removeFromCart,
                clearCart,
                subscribeToNewsletter,
            }}
        >
            {children}
        </ShopContext.Provider>
    );
}
