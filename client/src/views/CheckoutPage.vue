<template>
  <div class="checkout-page">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/home' }">Account</el-breadcrumb-item>
      <el-breadcrumb-item :to="{ path: '/cart' }">Cart</el-breadcrumb-item>
      <el-breadcrumb-item>CheckOut</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="checkout-container">
      <div class="billing-details">
        <h2>Billing Details</h2>
        <el-form
          :model="form"
          :rules="rules"
          ref="formRef"
          label-position="top"
          require-asterisk-position="right"
        >
          <el-form-item label="Full Name" prop="name" required>
            <el-input v-model="form.name" />
          </el-form-item>
          <el-form-item label="Home Address" prop="address" required>
            <el-input v-model="form.address" />
          </el-form-item>
          <el-form-item label="Province" prop="province_id" required>
            <el-select
              v-model="form.province_id"
              filterable
              placeholder="Select your province"
              @change="handleProvinceSelect"
            >
              <el-option
                v-for="item in provinces"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="District" prop="district_id" required>
            <el-select
              v-model="form.district_id"
              filterable
              placeholder="Select your district"
              @change="handleDistrictSelect"
              :disabled="!form.province_id"
            >
              <el-option
                v-for="item in districts"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Ward" prop="ward_id" required>
            <el-select
              v-model="form.ward_id"
              filterable
              placeholder="Select your ward"
              @change="handleWardSelect"
              :disabled="!form.district_id"
            >
              <el-option
                v-for="item in wards"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Phone Number" prop="phone" required>
            <el-input v-model="form.phone" />
          </el-form-item>
          <el-form-item label="Email Address" prop="email" required>
            <el-input v-model="form.email" />
          </el-form-item>
          <el-form-item label="Note" prop="note">
            <el-input
              v-model="form.note"
              maxlength="200"
              placeholder="Please input"
              :autosize="{ minRows: 8, maxRows: 14 }"
              show-word-limit
              type="textarea"
            />
          </el-form-item>
        </el-form>
      </div>
      <div class="order-summary">
        <el-card>
          <div class="order-summary-header">
            <span>Book</span>
            <span>Quantity</span>
            <span>Due Date</span>
          </div>
          <div
            class="order-summary-item"
            v-for="item in cart"
            :key="item.book_id"
          >
            <el-tooltip
              class="item"
              effect="dark"
              :content="item.book_name"
              placement="top"
            >
              <span class="short-text">{{
                truncateText(item.book_name, 20)
              }}</span>
            </el-tooltip>
            <span class="short-text">{{ item.quantity }}</span>
            <span class="short-text">{{ item.due_date }}</span>
          </div>
          <el-button
            type="primary"
            class="confirm-order-button"
            @click="handleSubmit"
          >
            Confirm Order
          </el-button>
        </el-card>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, reactive, computed, watch } from "vue";
import { useCartStore } from "@/stores/cartStore";
import { useAuthStore } from "@/stores/authStore";
import router from "@/router";
import { toast } from "vue3-toastify";
import ticketService from "@/services/ticketService";
import addressService from "@/services/addressService";

import type { ProvinceData, DistrictData, WardData } from "@/types";

const authStore = useAuthStore();
const cartStore = useCartStore();
const cart = reactive(cartStore.getCart);

const user = computed(() => {
  return authStore.getUser;
});

const formRef = ref();

const form = reactive({
  name: user.value?.name || "",
  address: user.value?.address || "",
  province_id: user.value?.province_id || null,
  district_id: user.value?.district_id || null,
  ward_id: user.value?.ward_id || null,
  phone: user.value?.phone || "",
  email: user.value?.email || "",
  note: "",
});

const rules = reactive({
  name: [
    { required: true, message: "Please input your name", trigger: "blur" },
  ],
  address: [
    { required: true, message: "Please input your address", trigger: "blur" },
  ],
  province_id: [
    {
      required: true,
      message: "Please select your province",
      trigger: "change",
    },
  ],
  district_id: [
    {
      required: true,
      message: "Please select your district",
      trigger: "change",
    },
  ],
  ward_id: [
    { required: true, message: "Please select your ward", trigger: "change" },
  ],
  phone: [
    {
      required: true,
      message: "Please input your phone number",
      trigger: "blur",
    },
    {
      pattern: /^[0-9]*$/,
      message: "Phone number must be numeric",
      trigger: "blur",
    },
  ],
  email: [
    { required: true, message: "Please input your email", trigger: "blur" },
    {
      type: "email",
      message: "Please input a valid email address",
      trigger: ["blur", "change"],
    },
  ],
});

const provinces = ref<ProvinceData[]>([]);
const districts = ref<DistrictData[]>([]);
const wards = ref<WardData[]>([]);

const fetchProvinces = async () => {
  try {
    provinces.value = await addressService.getProvinces();
  } catch (error) {
    console.error("Error fetching provinces:", error);
  }
};

const fetchDistricts = async () => {
  if (form.province_id) {
    try {
      districts.value = await addressService.getDistricts(form.province_id);
    } catch (error) {
      console.error("Error fetching districts:", error);
    }
  }
};

const fetchWards = async () => {
  if (form.district_id) {
    try {
      wards.value = await addressService.getWards(form.district_id);
    } catch (error) {
      console.error("Error fetching wards:", error);
    }
  }
};

const handleProvinceSelect = (value: number) => {
  form.province_id = value;
  form.district_id = null;
  form.ward_id = null;
  districts.value = [];
  wards.value = [];
  fetchDistricts();
};

const handleDistrictSelect = (value: number) => {
  form.district_id = value;
  form.ward_id = null;
  wards.value = [];
  fetchWards();
};

const handleWardSelect = (value: number) => {
  form.ward_id = value;
};

watch(
  () => form.province_id,
  (newVal) => {
    if (newVal) {
      fetchDistricts();
    }
  }
);

watch(
  () => form.district_id,
  (newVal) => {
    if (newVal) {
      fetchWards();
    }
  }
);

fetchProvinces();
fetchDistricts();
fetchWards();

const truncateText = (text: string, length: number) => {
  if (text.length > length) {
    return text.substring(0, length) + "...";
  }
  return text;
};

const handleSubmit = () => {
  formRef.value.validate(async (valid: boolean) => {
    if (valid) {
      const address = `${form.address}, ${form.province_id ? provinces.value.find((p) => p.id === form.province_id).name : ""}, ${form.district_id ? districts.value.find((d) => d.id === form.district_id).name : ""}, ${form.ward_id ? wards.value.find((w) => w.id === form.ward_id).name : ""}`;
      const book_data = cart.map((item) => ({
        book_id: item.book_id,
        book_name: item.book_name,
        quantity: item.quantity,
        expected_refund_time: item.due_date,
      }));

      const createTicketData = {
        user_id: user.value?.id,
        address,
        phone: form.phone,
        book_data,
        note: form.note,
      };

      try {
        const response = await ticketService.createTicket(createTicketData);
        if (response.success) {
          cartStore.clearCart();
          router.push({ name: "TicketPage" });
          setTimeout(() => {
            toast.success("Order placed successfully!");
          }, 300);
        } else {
          toast.error("Some book are not available. Please later.");
        }
      } catch (error) {
        toast.error("Some book are not available. Please later.");
      }
    } else {
      return false;
    }
  });
};
</script>
<style lang="scss" scoped>
.checkout-page {
  max-width: 1170px;
  margin: 40px auto 0 auto;
  padding: 0 20px;

  .el-breadcrumb {
    margin: 1rem 0;

    .el-breadcrumb__item {
      .el-breadcrumb__inner {
        color: var(--el-color-primary-light-2);
        font-size: 0.875rem;
        font-style: normal;
        line-height: 1.3125rem;
      }
    }
  }

  .checkout-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    .billing-details {
      width: 48%;
    }
    .order-summary {
      width: 48%;
      height: 800px;
      overflow-y: auto;
    }

    .billing-details {
      h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: var(--el-color-primary);
      }

      .el-form-item {
        margin-bottom: 20px;

        .el-form-item__label {
          font-weight: bold;
          color: var(--el-color-primary);
        }

        .el-input {
          width: 100%;
          height: 40px;
          background-color: #f3f3f3;
          border-radius: 4px;
        }
        :deep(.el-select__wrapper) {
          height: 40px;
        }
      }

      .el-checkbox {
        margin-top: 20px;
        .el-checkbox__label {
          color: var(--el-color-primary);
        }
      }
    }

    .order-summary {
      .el-card {
        padding: 20px;
        position: relative;
        border: 1px solid #ebeef5;
        background-color: #f9fafc;
        border-radius: 4px;

        .order-summary-header,
        .order-summary-item {
          display: grid;
          grid-template-columns: 1fr 1fr 1fr;
          gap: 10px;
          align-items: center;
          border-bottom: 1px solid #ebeef5;
          padding-bottom: 10px;
          margin-bottom: 10px;
        }

        .order-summary-header {
          position: sticky;
          top: 0;
          left: 0;
          font-weight: bold;
          color: var(--el-color-primary);
        }

        .order-summary-item {
          margin: 10px 0px;

          &.total {
            font-weight: bold;
          }
        }

        .confirm-order-button {
          background-color: var(--el-color-primary);
          border-color: var(--el-color-primary);
          width: 100%;
          height: 56px;
          color: white;
          margin-top: 20px;
          border-radius: 4px;
        }
      }
    }
  }

  .short-text {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 150px;
  }
}

@media (max-width: 1024px) {
  .checkout-page {
    .checkout-container {
      flex-direction: column;

      .billing-details,
      .order-summary {
        width: 100%;
      }

      .order-summary {
        margin-top: 20px;
      }
    }
  }
}

@media (max-width: 768px) {
  .checkout-page {
    .checkout-container {
      .billing-details,
      .order-summary {
        width: 100%;
      }

      .order-summary {
        margin-top: 20px;
      }
    }
  }
}

@media (max-width: 480px) {
  .checkout-page {
    padding: 0 10px;

    .checkout-container {
      .order-summary .el-card {
        padding: 10px;
      }

      .billing-details .el-form-item {
        margin-bottom: 15px;

        .el-input {
          height: 35px;
        }
      }

      .order-summary .confirm-order-button {
        height: 50px;
      }
    }
  }
}
</style>
