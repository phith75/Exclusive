<script lang="ts" setup>
import { ref } from "vue";
import { useTransition } from "@vueuse/core";

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: Number,
    required: true,
    default: 0,
  },
});
const source = ref<number>(0);

const outputValue = useTransition(source, {
  duration: 1500,
});
source.value = props.value;
</script>

<template>
  <div class="statistic">
    <div class="icon">
      <div class="icon-2">
        <slot />
      </div>
    </div>
    <el-statistic :title="title" :value="outputValue" />
  </div>
</template>

<style lang="scss">
.statistic {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
  border: 1px solid rgba(0, 0, 0, 0.3);
  border-radius: 0.25rem;
  padding: 1.875rem 2.125rem;
  cursor: pointer;

  &:hover {
    background-color: var(--primary-color);
    border: 1px solid var(--primary-color);

    .icon {
      background-color: rgba(250, 250, 250, 0.78);

      .icon-2 {
        background-color: rgba(250, 250, 250, 1);
        color: var(--primary-color);
      }
    }

    .el-statistic {
      .el-statistic__content {
        color: var(--second-color-light);
      }

      .el-statistic__head {
        color: var(--second-color-light);
      }
    }
  }

  .icon {
    width: 5rem;
    height: 5rem;
    background-color: #c1c0c1;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;

    .icon-2 {
      width: 4rem;
      height: 4rem;
      background-color: rgba(255, 69, 0, 0.78);
      border-radius: 50%;
      margin: auto 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }
  }

  .el-statistic {
    display: flex;
    flex-direction: column-reverse;
    gap: 0.75rem;
    align-items: center;

    .el-statistic__content {
      color: var(--primary-color-light);
      font-family: "Inter", sans-serif;
      font-size: 2rem;
      font-style: normal;
      font-weight: 700;
      line-height: 1.875rem;
      letter-spacing: 0.08rem;
    }

    .el-statistic__head {
      color: var(--primary-color-light);
      text-align: center;
      font-family: "Poppins", sans-serif;
      font-size: 1rem;
      font-style: normal;
      font-weight: 400;
      line-height: 1.5rem;
    }
  }
}
</style>
