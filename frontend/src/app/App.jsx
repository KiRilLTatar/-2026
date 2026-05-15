import { Routes, Route } from "react-router-dom";
import Header from "../components/Header.jsx";
import Footer from "../components/Footer.jsx";

import HomePage from "../pages/Home.jsx";
import CatalogPage from "../pages/Catalog.jsx";
import ReturnsPage from "../pages/Returns.jsx";
import DeliveryPage from "../pages/Delivery.jsx";
import LoginPage from "../pages/Login.jsx";
import RegisterPage from "../pages/Register.jsx";
import ProfilePage from "../pages/Profile.jsx";
import FavoritesPage from "../pages/Favorites.jsx";
import CartPage from "../pages/Cart.jsx";

export default function App() {
    return (
        <div className="page">
            <Header />

            <main className="main">
                <Routes>
                    <Route path="/" element={<HomePage />} />
                    <Route path="/catalog" element={<CatalogPage />} />
                    <Route path="/returns" element={<ReturnsPage />} />
                    <Route path="/delivery" element={<DeliveryPage />} />
                    <Route path="/login" element={<LoginPage />} />
                    <Route path="/register" element={<RegisterPage />} />
                    <Route path="/profile" element={<ProfilePage />} />
                    <Route path="/favorites" element={<FavoritesPage />} />
                    <Route path="/cart" element={<CartPage />} />
                </Routes>
            </main>

            <Footer />
        </div>
    );
}
