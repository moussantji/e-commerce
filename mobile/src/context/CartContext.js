import React, { createContext, useContext, useState, useCallback } from "react";
import api from "../api/client";
import { useAuth } from "./AuthContext";

const CartContext = createContext(null);
export const useCart = () => useContext(CartContext);

export function CartProvider({ children }) {
    const { token } = useAuth();
    const [cart, setCart] = useState({ items: [], count: 0, total: 0 });
    const [loading, setLoading] = useState(false);

    const refresh = useCallback(async () => {
        if (!token) {
            setCart({ items: [], count: 0, total: 0 });
            return;
        }
        setLoading(true);
        try {
            const { data } = await api.get("/cart");
            setCart(data);
        } finally {
            setLoading(false);
        }
    }, [token]);

    const add = async (productId, quantity = 1) => {
        const { data } = await api.post("/cart", {
            product_id: productId,
            quantity,
        });
        setCart(data);
        return data;
    };

    const update = async (productId, quantity) => {
        const { data } = await api.put(`/cart/${productId}`, { quantity });
        setCart(data);
    };

    const remove = async (productId) => {
        const { data } = await api.delete(`/cart/${productId}`);
        setCart(data);
    };

    return (
        <CartContext.Provider
            value={{ cart, loading, refresh, add, update, remove }}
        >
            {children}
        </CartContext.Provider>
    );
}
