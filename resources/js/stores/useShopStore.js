import { defineStore } from 'pinia';

const STORAGE_KEY = 'shopsant:shop';

const normalizeImage = (product) => {
    if (Array.isArray(product?.images) && product.images.length > 0) {
        return product.images[0];
    }

    return product?.image ?? null;
};

const createPayload = (product, quantity = 1) => ({
    id: product.id,
    title: product.title,
    slug: product.slug,
    price: product.price ?? 0,
    final_price: product.final_price ?? product.price ?? 0,
    image: normalizeImage(product),
    quantity,
});

export const useShopStore = defineStore('shop', {
    state: () => ({
        cart: [],
        favorites: [],
        initialized: false,
    }),
    getters: {
        cartCount: (state) => state.cart.reduce((total, item) => total + (item.quantity ?? 0), 0),
        cartTotal: (state) => state.cart.reduce((total, item) => total + (item.quantity ?? 0) * (item.final_price ?? item.price ?? 0), 0),
        favoritesCount: (state) => state.favorites.length,
        isFavorite: (state) => (productId) => state.favorites.some((item) => item.id === productId),
        isInCart: (state) => (productId) => state.cart.some((item) => item.id === productId),
    },
    actions: {
        initialize() {
            if (this.initialized || typeof window === 'undefined') {
                return;
            }

            try {
                const raw = window.localStorage.getItem(STORAGE_KEY);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    this.cart = Array.isArray(parsed.cart) ? parsed.cart : [];
                    this.favorites = Array.isArray(parsed.favorites) ? parsed.favorites : [];
                }
            } catch (error) {
                console.error('SHOP::INIT_ERROR', error);
            }

            this.initialized = true;
        },
        persist() {
            if (typeof window === 'undefined') {
                return;
            }

            const payload = {
                cart: this.cart,
                favorites: this.favorites,
            };

            window.localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
        },
        addToCart(product, quantity = 1) {
            this.initialize();

            const normalizedQuantity = Math.max(1, Number(quantity) || 1);
            const existingIndex = this.cart.findIndex((item) => item.id === product.id);

            if (existingIndex >= 0) {
                this.cart[existingIndex].quantity += normalizedQuantity;
            } else {
                this.cart.push(createPayload(product, normalizedQuantity));
            }

            this.persist();
        },
        updateCartQuantity(productId, quantity) {
            this.initialize();

            const item = this.cart.find((payload) => payload.id === productId);
            if (!item) {
                return;
            }

            item.quantity = Math.max(1, Number(quantity) || 1);
            this.persist();
        },
        removeFromCart(productId) {
            this.initialize();
            this.cart = this.cart.filter((payload) => payload.id !== productId);
            this.persist();
        },
        clearCart() {
            this.initialize();
            this.cart = [];
            this.persist();
        },
        toggleFavorite(product) {
            this.initialize();

            const index = this.favorites.findIndex((payload) => payload.id === product.id);
            if (index >= 0) {
                this.favorites.splice(index, 1);
            } else {
                this.favorites.push({
                    id: product.id,
                    title: product.title,
                    slug: product.slug,
                    price: product.price ?? 0,
                    final_price: product.final_price ?? product.price ?? 0,
                    image: normalizeImage(product),
                });
            }

            this.persist();
        },
    },
});

