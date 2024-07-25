import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { toast } from "vue3-toastify";

interface CartItem {
  book_id: number;
  quantity: number;
  book_name: string;
  quantity_in_stock: number;
  due_date: string;
  thumbnail: string;
}

export const useCartStore = defineStore("cart", () => {
  const cart = ref<CartItem[]>([]);

  const loadCartFromLocalStorage = () => {
    const storedCart = localStorage.getItem("cart");
    if (storedCart) {
      cart.value = JSON.parse(storedCart);
    }
  };

  const saveCartToLocalStorage = () => {
    localStorage.setItem("cart", JSON.stringify(cart.value));
  };

  const addToCart = (
    book_id: number,
    quantity: number,
    quantity_in_stock: number,
    book_name: string,
    thumbnail: string,
    due_date: string = ""
  ) => {
    const existingItem = cart.value.find((item) => item.book_id === book_id);
    if (existingItem) {
      if (existingItem.quantity + quantity > existingItem.quantity_in_stock) {
        toast.error("Quantity exceeds the available stock!");
        return;
      }
      existingItem.quantity += quantity;
    } else {
      if (quantity > quantity_in_stock) {
        toast.error("Quantity exceeds the available stock!");
        return;
      }
      toast.success("Book added to the cart!");

      cart.value.push({
        book_id,
        quantity,
        quantity_in_stock,
        book_name,
        due_date,
        thumbnail,
      });
    }
    saveCartToLocalStorage();
  };

  const updateQuantity = (book_id: number, quantity: number) => {

    const existingItem = cart.value.find((item) => item.book_id === book_id);

    if (existingItem) {
      if (quantity > existingItem.quantity_in_stock) {
        toast.error("Quantity exceeds the available stock!");
        return;
      }
      existingItem.quantity = quantity;
    } else {
      toast.error("Book not found in the cart!");
    }
    saveCartToLocalStorage();
  };

  const updateDueDate = (book_id: number, due_date: string) => {
    const existingItem = cart.value.find((item) => item.book_id === book_id);
    if (existingItem) {
      existingItem.due_date = due_date;
    } else {
      toast.error("Book not found in the cart!");
    }
    saveCartToLocalStorage();
  };

  const removeFromCart = (book_id: number) => {
    const index = cart.value.findIndex((item) => item.book_id === book_id);
    if (index !== -1) {
      cart.value.splice(index, 1);
      toast.success("Book removed from the cart!");
    } else {
      toast.error("Book not found in the cart!");
    }
    saveCartToLocalStorage();
  };

  const getCart = computed(() => {
    return cart.value;
  });

  const totalQuantity = computed(() => {
    return cart.value.reduce((total, item) => total + item.quantity, 0);
  });
  const clearCart = () => {
    cart.value = [];
    saveCartToLocalStorage();
  };
  loadCartFromLocalStorage();

  return {
    cart,
    addToCart,
    updateQuantity,
    updateDueDate,
    removeFromCart,
    getCart,
    totalQuantity,
    clearCart,
  };
});
