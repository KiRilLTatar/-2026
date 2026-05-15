const API_BASE = import.meta.env.VITE_API_BASE || "/api";

export class ApiError extends Error {
    constructor(message, status, errors = {}) {
        super(message);
        this.name = "ApiError";
        this.status = status;
        this.errors = errors;
    }
}

function buildUrl(path) {
    if (path.startsWith("http://") || path.startsWith("https://")) {
        return path;
    }

    return `${API_BASE}${path.startsWith("/") ? path : `/${path}`}`;
}

async function parseResponse(response) {
    const text = await response.text();
    let payload = {};

    if (text) {
        try {
            payload = JSON.parse(text);
        } catch {
            throw new ApiError("Сервер вернул непонятный ответ.", response.status);
        }
    }

    if (!response.ok || payload.success === false) {
        throw new ApiError(
            payload.message || "Не удалось выполнить запрос.",
            response.status,
            payload.errors || {}
        );
    }

    return payload;
}

export async function apiGet(path) {
    const response = await fetch(buildUrl(path), {
        method: "GET",
        credentials: "same-origin",
        headers: {
            Accept: "application/json",
        },
    });

    return parseResponse(response);
}

export async function apiPost(path, payload = {}, csrfToken = "") {
    const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
    };

    if (csrfToken) {
        headers["X-CSRF-Token"] = csrfToken;
    }

    const response = await fetch(buildUrl(path), {
        method: "POST",
        credentials: "same-origin",
        headers,
        body: JSON.stringify(payload),
    });

    return parseResponse(response);
}
