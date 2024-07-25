<template>
  <div class="benefits-container">
    <el-row :gutter="20">
      <el-col
        :span="8"
        :xs="24"
        :sm="24"
        :md="8"
        v-for="benefit in benefits"
        :key="benefit.title"
      >
        <div class="benefit-item">
          <img :src="benefit.icon" alt="icon" class="icon" />
          <h3 class="benefit-title">{{ benefit.title }}</h3>
          <p class="benefit-description">{{ benefit.description }}</p>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script setup lang="ts" generic>
import { ref } from "vue";
import { DeliveryService, RefundService, CustomerService } from "@/assets";

const benefits = ref([
  {
    icon: DeliveryService,
    title: "FREE AND FAST DELIVERY",
    description: "Free delivery for all orders over $140",
  },
  {
    icon: CustomerService,
    title: "24/7 CUSTOMER SERVICE",
    description: "Friendly 24/7 customer support",
  },
  {
    icon: RefundService,
    title: "MONEY BACK GUARANTEE",
    description: "We return money within 30 days",
  },
]);
</script>

<style scoped>
.benefits-container {
  margin-top: 80px;
  text-align: center;
}

.benefit-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

.icon {
  width: 80px;
  height: 80px;
}

.benefit-title {
  font-size: 20px;
  font-weight: 600;
  line-height: 28px;
  margin: 24px 0px 8px 0px;
}

.benefit-description {
  font-size: 14px;
}
</style>
