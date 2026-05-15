import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./app/App.jsx";
import { ShopProvider } from "./context/ShopContext.jsx";
import "./styles/globals.css";

ReactDOM.createRoot(document.getElementById("root")).render(
    <React.StrictMode>
        <ShopProvider>
            <BrowserRouter>
                <App />
            </BrowserRouter>
        </ShopProvider>
    </React.StrictMode>
);
