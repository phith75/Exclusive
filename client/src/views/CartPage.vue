<template>
  <div class="cart-page">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>Cart</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="table-container">
      <div v-if="cart.length > 0">
        <div class="cart-header">
          <div class="header-item">Product</div>
          <div class="header-item">Quantity</div>
          <div class="header-item">Due Date</div>
          <div class="header-item" style="text-align: center">Actions</div>
        </div>
        <div class="cart-list">
          <div v-for="row in cart" :key="row.book_id" class="cart-item">
            <div class="product-column">
              <el-image
                :src="getImageUrl(row.thumbnail)"
                alt="Book Thumbnail"
              />
              <span>{{ row.book_name }}</span>
            </div>
            <div class="quantity-column">
              <el-input-number
                min="1"
                :max="row.quantity_in_stock"
                v-model="row.quantity"
                @change="updateQuantity(row.book_id, row.quantity)"
                controls-position="right"
              />
            </div>
            <div class="due-date-column">
              <el-date-picker
                v-model="row.due_date"
                type="date"
                :disabled-date="disabledDate"
                placeholder="Select Date"
                @change="updateDueDate(row.book_id, row.due_date)"
              />
              <div v-if="row.error" class="error-message">{{ row.error }}</div>
            </div>
            <div class="actions-column" style="text-align: center">
              <el-button
                type="danger"
                @click="removeBookFromCart(row.book_id)"
                :icon="Delete"
              />
            </div>
          </div>
        </div>
      </div>
      <el-empty v-else description="You have no books in cart  :(" />
    </div>
    <div class="cart-actions">
      <el-button @click="returnToShop">Return To Shop</el-button>
      <el-button @click="updateCart">Update Cart</el-button>
    </div>
    <el-row class="total-price-row" justify="end">
      <el-col :span="10" :xs="24">
        <el-card class="cart-total">
          <h3>Cart Total</h3>
          <div class="cart-total-item">
            <span>Shipping:</span>
            <span>Free</span>
          </div>
          <div class="cart-total-item">
            <span>Total quantity:</span>
            <span>{{ cartStore.totalQuantity }}</span>
          </div>
          <el-button
            type="primary"
            class="checkout-button"
            @click="checkout"
            :disabled="!cart.length"
          >
            LendTicket Books
          </el-button>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script lang="ts" setup>
import { useCartStore } from "@/stores/cartStore";
import { useRouter } from "vue-router";
import { reactive } from "vue";
import moment from "moment";
import { Delete } from "@element-plus/icons-vue";

const cartStore = useCartStore();
const cart = reactive(cartStore.getCart);
const router = useRouter();

const updateQuantity = (book_id: number, quantity: number) => {
  cartStore.updateQuantity(book_id, quantity);
};

const updateDueDate = (book_id: number, due_date: string) => {
  const formattedDate = formatDate(due_date);
  cartStore.updateDueDate(book_id, formattedDate);
  validateDueDate(book_id);
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  return `${year}-${month}-${day}`;
};
const disabledDate = (time: Date) => {
  return moment(time) < moment().subtract(1, "day");
};
const removeBookFromCart = (book_id: number) => {
  cartStore.removeFromCart(book_id);
};

const returnToShop = () => {
  router.push("/home");
};

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};

const updateCart = () => {};

const isDateValid = (due_date: string) => {
  const selectedDate = new Date(due_date);
  const currentDate = new Date();
  currentDate.setHours(0, 0, 0, 0);
  return selectedDate >= currentDate;
};

const validateDueDate = (book_id: number) => {
  const item = cart.find((book) => book.book_id === book_id);

  if (item) {
    if (!isDateValid(item.due_date)) {
      item.error = `Due date must be greater than or equal to today.`;
    } else {
      item.error = "";
    }
  }
};

const checkout = () => {
  let isValid = true;
  if (!cart.length) {
    isValid = false;
  } else {
    cart.forEach((item) => {
      if (!isDateValid(item.due_date)) {
        isValid = false;
        item.error = `please select a due date.`;
      } else {
        item.error = "";
      }
    });
  }
  if (isValid) {
    router.push("/checkout");
  }
};
</script>

<style lang="scss" scoped>
.cart-page {
  width: 100%;
  max-width: 1170px;
  margin: 40px auto 140px auto;

  .el-breadcrumb {
    margin: 1rem 0;

    .el-breadcrumb__item {
      :global(.el-breadcrumb__inner) {
        color: var(--primary-color-light);
        font-size: 0.875rem;
        font-style: normal;
        line-height: 1.3125rem;
      }
    }
  }

  .table-container {
    -webkit-overflow-scrolling: touch;
    position: relative;
    overflow-x: auto;

    .cart-header {
      min-width: 800px;
      width: 99%;
      display: grid;
      grid-template-columns: 2.1fr 1fr 1fr 0.5fr;
      font-weight: 400;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
      margin: 10px 3px;
      min-height: 72px;
      position: sticky;
      top: 0;
    }

    .cart-list {
      min-width: 800px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      overflow-x: auto;
    }

    .cart-item {
      width: 99%;

      display: grid;
      height: 102px;
      grid-template-columns: 2fr 1fr 1fr 0.5fr;
      align-items: center;
      padding: 20px;
      margin: 10px 3px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }
    .header-item {
      font-weight: 400;
    }

    .product-column {
      display: flex;
      align-items: center;
      gap: 10px;

      span {
        font-size: 14px;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        overflow: hidden;
      }
    }

    .el-image {
      max-height: 100px;
      width: 54px;
    }

    .quantity-column .el-input-number {
      width: 100%;
      max-width: 72px;
      min-height: 40px;
      :deep(el-input-number__decrease) {
        background-color: none !important;
        border: none !important;
      }
    }

    .due-date-column {
      display: flex;
      flex-direction: column;
      text-align: center;

      .error-message {
        color: red;
        text-align: start;
        font-size: 12px;
        margin-top: 5px;
      }
    }
  }

  .cart-actions {
    width: 99%;
    display: flex;
    justify-content: space-between;
    margin: 20px 3px 0px 3px;

    button {
      width: 100%;
      max-width: 195px;
      height: 56px;
    }
  }

  .total-price-row {
    margin-top: 80px;
  }

  .cart-total {
    padding: 23px;
    width: 99%;
    border: 1px solid #ebeef5;
    background-color: #f9fafc;

    .cart-total-item {
      display: flex;
      justify-content: space-between;
      margin: 24px 0px;
      border-bottom: 1px solid #ebeef5;

      &.total {
        font-weight: bold;
      }
    }

    .checkout-button {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      max-width: 260px;
      @media (max-width: 768px) {
        max-width: 100%;
      }
      width: 100%;
      height: 56px;
      color: white;
      margin: 0px 70px;
      &:disabled {
        background-color: #d3d3d3;
        border-color: #d3d3d3;
        cursor: not-allowed;
      }
    }
  }

  .el-empty {
    margin-top: 20px;
    width: 100%;
    height: 356px;
    border-bottom: 1px solid #ebeef5;
    border-top: 1px solid #ebeef5;
  }
}

@media (max-width: 768px) {
  .cart-page {
    .table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      .cart-header {
        grid-template-columns: 1.7fr 1fr 1fr 0.5fr;
      }
      .cart-list {
        flex-direction: column;
      }
    }
    .cart-actions {
      display: flex;
      flex-direction: column-reverse;
      gap: 20px;
      margin-top: 20px;

      button {
        max-width: 100%;
        height: 56px;
        margin: 0px;
      }
    }
    .total-price-row {
      margin-top: 40px;
      .checkout-button {
        margin: 0px;
      }
    }
  }
}
</style>
