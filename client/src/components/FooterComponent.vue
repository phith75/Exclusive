<script lang="ts" setup>
import {
  AppStore,
  Facebook,
  GooglePlay,
  IconSend,
  Instagram,
  Linkedin,
  LogoLight,
  QrCode,
  Twitter,
} from "@/assets";
import { ref } from "vue";

const email = ref<string>("");
</script>

<template>
  <el-footer>
    <el-container>
      <el-row class="main-footer">
        <el-col :lg="5" :sm="12" :xs="24" class="subscribe">
          <el-image :src="LogoLight" class="logo" />
          <span>Subscribe</span>
          <p>Get 10% off your first order</p>
          <form class="form">
            <el-input
              v-model="email"
              placeholder="Enter your email"
              size="default"
              class="sent-email"
            />
            <el-image :src="IconSend" class="icon-sent" />
          </form>
        </el-col>
        <el-col :lg="6" :sm="12" :xs="24" class="support">
          <span>Support</span>
          <ul>
            <li>111 Bijoy sarani, Dhaka, DH 1515, Bangladesh.</li>
            <li>exclusive@gmail.com</li>
            <li>+88015-88888-9999</li>
          </ul>
        </el-col>
        <el-col :lg="4" :sm="12" :xs="24" class="account">
          <span>Account</span>
          <ul>
            <li>My Account</li>
            <li>Login / Register</li>
            <li>Cart</li>
            <li>Wishlist</li>
            <li>Shop</li>
          </ul>
        </el-col>
        <el-col :lg="4" :sm="12" :xs="24" class="quick-link">
          <span>Quick Link</span>
          <ul>
            <li>Privacy Policy</li>
            <li>Terms Of Use</li>
            <li>FAQ</li>
            <li>Contact</li>
          </ul>
        </el-col>
        <el-col :lg="5" :sm="12" :xs="24" class="download-app">
          <span>Download App</span>
          <ul>
            <li>
              <span class="title">Save $3 with App New User Only</span>
            </li>
            <li class="link-install">
              <div>
                <el-image :src="QrCode" class="qr-code" />
              </div>
              <div class="link-store">
                <el-image :src="GooglePlay" />
                <el-image :src="AppStore" />
              </div>
            </li>
            <li class="social">
              <el-image :src="Facebook" />
              <el-image :src="Twitter" />
              <el-image :src="Instagram" />
              <el-image :src="Linkedin" />
            </li>
          </ul>
        </el-col>
      </el-row>
    </el-container>
    <el-row class="copy-right-row">
      <el-col :span="24" class="copy-right">
        <span class="title"
          >© Copyright Kiaisoft 2024. All right reserved</span
        >
      </el-col>
    </el-row>
  </el-footer>
</template>

<style lang="scss" scoped>
.el-footer {
  height: 100%;
  background: var(--primary-color);
  color: var(--second-color-light);
  padding: 0;

  .el-row {
    padding: 1rem 0;
    margin: 0;
    justify-content: space-between;

    &.main-footer {
      margin: 5rem 0 3.75rem 0;
    }
    .main-footer {
      margin: 1rem 0;
    }
    .el-col {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;

      ul {
        display: flex;
        flex-direction: column;
        gap: 1rem;

        li {
          cursor: pointer;
        }
      }

      span {
        color: var(--second-color-light);
        font-size: 1.25rem;
        font-weight: 500;
        line-height: 1.75rem;
      }

      &.copy-right {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 3rem;
        background: var(--primary-color-dark);
        color: var(--second-color-light);

        .title {
          font-size: 1rem;
          font-style: normal;
          font-weight: 400;
          line-height: 1.5rem;
          opacity: 0.4;
        }
      }

      &.subscribe {
        display: flex;
        gap: 0.625rem;

        .el-image {
          width: 7.375rem;
          height: 1.5rem;
        }

        .form {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.625rem;
          border-radius: 0.25rem;
          padding: 0.4rem;
          border: 1px solid var(--second-color-light);
          max-width: 14rem;

          .el-image {
            cursor: pointer;
          }

          .el-input {
            width: 50rem;
            &.sent-email {
              background-color: transparent !important;
              border: none;
              box-shadow: none;
              :deep(.el-input__wrapper) {
                background-color: transparent !important;
                border: none;
                box-shadow: none;

                &:focus,
                &:hover,
                &.is-focus {
                  outline: none;
                  border: none;
                  box-shadow: none;
                }

                :deep(.el-input__inner) {
                  &::placeholder {
                    color: #ffffff;
                    opacity: 0.4;
                  }
                }
              }
            }
          }
        }
      }
    }

    &.copy-right-row {
      border-top: 1px solid var(--second-color-light);
      opacity: 0.5;
    }
  }

  .download-app {
    max-width: fit-content;
    .social {
      display: flex;
      justify-content: flex-start;
      .el-image {
        cursor: pointer;
        margin-right: 2rem;
      }
    }

    span.title {
      color: var(--second-color-light);
      font-size: 0.75rem;
      font-weight: 500;
      line-height: 1.125rem;
    }

    .link-install {
      display: flex;
      gap: 0.3rem;
      align-items: center;

      .qr-code {
        margin: auto;
        width: 5rem;
        height: 5rem;
      }

      .link-store {
        display: flex;
        flex-direction: column;
      }
    }
  }
}

@media (max-width: 576px) {
  .el-footer {
    .el-container {
      .main-footer {
        margin: 1rem 1rem;
        gap: 2rem;
      }
    }
  }
}

@media (min-width: 768px) {
  .el-footer {
    .el-container {
      margin: 0 auto;
      max-width: 1170px;
      .main-footer {
        width: 100%;
        margin: 1rem 0;
      }
    }
  }
}

@media (min-width: 1200px) {
  .el-footer {
    .el-container {
      .main-footer {
        margin: 2rem 0;
      }
    }
  }
}
</style>
