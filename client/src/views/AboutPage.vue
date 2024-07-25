<script lang="ts" setup>
import { BannerAbout } from "@/assets";
import StatisticItem from "@/components/StatisticComponent.vue";
import ShoppingBag from "@/assets/icons/ShoppingBag.vue";
import SaleIcon from "@/assets/icons/SaleIcon.vue";
import ShopIcon from "@/assets/icons/ShopIcon.vue";
import GrossSaleIcon from "@/assets/icons/GrossSaleIcon.vue";
import OurService from "@/components/OurService.vue";
</script>

<template>
  <el-container class="about-page">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ name: 'Homepage' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>About</el-breadcrumb-item>
    </el-breadcrumb>
    <el-row class="banner">
      <el-col :lg="12" class="banner-info">
        <el-text class="title" size="large" tag="h3"> Our Story </el-text>
        <el-text tag="p">
          Launced in 2015, Exclusive is South Asia’s premier online shopping
          makterplace with an active presense in Bangladesh. Supported by wide
          range of tailored marketing, data and service solutions, Exclusive has
          10,500 sallers and 300 brands and serves 3 millioons customers across
          the region.
        </el-text>
        <el-text tag="p">
          Exclusive has more than 1 Million products to offer, growing at a very
          fast. Exclusive offers a diverse assotment in categories ranging from
          consumer.
        </el-text>
      </el-col>
      <el-col :lg="12">
        <el-image :src="BannerAbout" alt="Banner About" loading="lazy" />
      </el-col>
    </el-row>
    <el-row class="statistic-group" justify="space-between">
      <el-col :lg="5">
        <statistic-item :value="102330" title="Sellers active our site">
          <ShopIcon />
        </statistic-item>
      </el-col>
      <el-col :lg="5">
        <statistic-item :value="493920" title="Produduct Sale">
          <SaleIcon />
        </statistic-item>
      </el-col>
      <el-col :lg="5">
        <statistic-item :value="288173" title="Active our site">
          <ShoppingBag />
        </statistic-item>
      </el-col>
      <el-col :lg="5">
        <statistic-item :value="103728" title="Gross sale our site">
          <GrossSaleIcon />
        </statistic-item>
      </el-col>
    </el-row>
    <OurService />
  </el-container>
</template>

<style lang="scss" scoped>
.about-page {
  margin: 40px auto 0px auto;
  max-width: 1170px;
  .el-breadcrumb {
    .el-breadcrumb__item {
      :global(.el-breadcrumb__inner) {
        color: var(--primary-color-light);
        font-size: 0.875rem;
        font-style: normal;
        line-height: 1.3125rem;
      }
    }
  }

  .banner {
    justify-content: space-between;
    gap: 1rem;

    .banner-info {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin: auto 0;
      max-width: 32.125rem;

      .title {
        color: var(--primary-color-light);
        text-align: justify;
        font-family: "Inter", sans-serif;
        font-size: 3.375rem;
        font-style: normal;
        font-weight: 600;
        line-height: 4rem;
        letter-spacing: 0.2025rem;
        align-self: flex-start;
      }
    }
  }

  .statistic-group {
    margin: 3rem 0;
    gap: 30px;

    .el-col {
      min-width: 270px;
    }
  }
}
</style>
