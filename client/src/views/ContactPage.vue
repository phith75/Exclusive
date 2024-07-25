<script lang="ts" setup>
import { MailIcon, PhoneIcon } from "@/assets";
import { reactive, ref } from "vue";
import type { ComponentSize, FormInstance, FormRules } from "element-plus";

const formSize = ref<ComponentSize>("default");
const ruleFormRef = ref<FormInstance>();
const ruleForm = reactive<any>({
  name: "",
  email: "",
  phone: "",
  message: "",
});
const loading = ref<boolean>(false);

const rules = reactive<FormRules<any>>({
  name: [
    { required: true, message: "Please input your name", trigger: "blur" },
  ],
  email: [
    {
      required: true,
      message: "Please input your email",
      trigger: "blur",
    },
  ],
  phone: [
    {
      required: true,
      message: "Please input your phone number",
      trigger: "blur",
    },
  ],
  message: [
    {
      required: true,
      message: "Please input your message",
      trigger: "blur",
    },
  ],
});
</script>

<template>
  <el-container class="contact-page">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ name: 'Homepage' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>Contact</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="contact-body">
      <el-aside width="21.25rem">
        <el-row>
          <el-col>
            <div class="title">
              <el-image :src="PhoneIcon" fit="cover" />
              <el-text>Call To Us</el-text>
            </div>
            <div class="description">
              <el-text tag="p">We are available 24/7, 7 days a week.</el-text>
              <el-text tag="p">Phone: +1 234 567 890</el-text>
            </div>
          </el-col>
          <el-divider />
          <el-col>
            <div class="title">
              <el-image :src="MailIcon" fit="cover" />
              <el-text>Write To US</el-text>
            </div>
            <div class="description">
              <el-text tag="p"
                >Fill out our form and we will contact you within 24
                hours.</el-text
              >
              <el-text tag="p">Emails: customer@exclusive.com</el-text>
            </div>
          </el-col>
        </el-row>
      </el-aside>
      <el-main>
        <el-form
          require-asterisk-position="right"
          ref="ruleFormRef"
          :model="ruleForm"
          :rules="rules"
          :size="formSize"
          class="contact-form"
          label-position="top"
          label-width="auto"
        >
          <el-row class="input-group">
            <el-form-item label="Name" prop="name">
              <el-input v-model="ruleForm.name" placeholder="Your name" />
            </el-form-item>
            <el-form-item label="Email" prop="email">
              <el-input v-model="ruleForm.email" placeholder="Your email" />
            </el-form-item>
            <el-form-item label="Phone" prop="phone">
              <el-input v-model="ruleForm.phone" placeholder="Your phone" />
            </el-form-item>
          </el-row>
          <el-form-item class="textarea" label="Message" prop="message">
            <el-input
              v-model="ruleForm.message"
              maxlength="500"
              placeholder="Your message"
              show-word-limit
              type="textarea"
            />
          </el-form-item>
          <el-form-item class="submit-button">
            <el-button :loading="loading">Send Massage</el-button>
          </el-form-item>
        </el-form>
      </el-main>
    </div>
  </el-container>
</template>

<style lang="scss">
.contact-page {
  margin: 40px auto 0px auto;
  max-width: 1170px;
  .contact-body {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1.88rem;
    margin: 2rem 0;

    .el-aside {
      border-radius: 0.25rem;
      background: #fff;
      box-shadow: 0px 1px 13px 0px rgba(0, 0, 0, 0.05);
      padding: 2.2rem 0;

      .el-row {
        .el-col {
          padding: 1.5rem;

          .title {
            display: flex;
            gap: 1rem;
            align-items: center;

            span {
              color: var(--primary-color-light);
              font-size: 1rem;
              font-style: normal;
              font-weight: 500;
              line-height: 1.5rem;
            }
          }

          .description {
            margin: 1rem 0;
            max-width: 15.63rem;

            p {
              margin: 0.8rem 0;
              color: var(--primary-color-light);
              font-size: 0.88rem;
              font-style: normal;
              font-weight: 400;
              line-height: 1.25rem;
            }
          }
        }

        .el-divider {
          &.el-divider--horizontal {
            width: 90%;
            margin: 0.1rem auto;
          }
        }
      }
    }

    .el-main {
      width: 100%;
      border-radius: 0.25rem;
      background: #fff;
      box-shadow: 0px 1px 13px 0px rgba(0, 0, 0, 0.05);

      .el-form {
        justify-content: center;

        .input-group {
          justify-content: center;

          .el-form-item {
            &:nth-child(2) {
              margin: 0 1rem;
            }

            .el-form-item__content {
              .el-input {
                width: 14.6875rem;
                height: 3.125rem;
                flex-shrink: 0;
                border-radius: 0.25rem;
                background: var(--Secondary, #f5f5f5);
                outline: none;
              }
            }
          }
        }

        .textarea {
          width: 46.0625rem;
          margin: 0 auto;

          .el-form-item__content {
            .el-textarea {
              .el-textarea__inner {
                background-color: #f5f5f5;
                width: 46.0625rem;
                height: 12.9375rem;
                flex-shrink: 0;
              }
            }
          }
        }

        .submit-button {
          padding: 0 3rem;
          margin-top: 1rem;

          .el-form-item__content {
            justify-content: flex-end;

            .el-button {
              width: 14.6875rem;
              height: 3.125rem;
              flex-shrink: 0;
              border-radius: 0.25rem;
              background: var(--primary-color);
              color: var(--second-color-light);
              font-size: 1rem;
              font-style: normal;
              font-weight: 500;
              line-height: 1.5rem;
            }
          }
        }
      }
    }
  }
}

@media screen and (max-width: 574px) {
  .contact-page {
    .contact-body {
      flex-direction: column;
      gap: 1rem;

      .el-aside {
        width: 100%;
        padding: 1rem 0;

        .el-row {
          .el-col {
            padding: 1rem;

            .title {
              span {
                font-size: 0.88rem;
              }
            }

            .description {
              p {
                font-size: 0.75rem;
              }
            }
          }

          .el-divider {
            &.el-divider--horizontal {
              width: 90%;
              margin: 0.1rem auto;
            }
          }
        }
      }

      .el-main {
        width: 100%;

        .el-form {
          width: 100%;

          .input-group {
            width: 100%;

            .el-form-item {
              width: 100%;

              &:nth-child(2) {
                margin: 0 0 18px 0;
              }

              .el-form-item__content {
                .el-input {
                  width: 100%;
                }
              }
            }
          }

          .textarea {
            width: 100%;

            .el-form-item__content {
              .el-textarea {
                .el-textarea__inner {
                  padding: 0.6rem;
                  width: 100%;
                }
              }
            }
          }

          .submit-button {
            padding: 0;

            .el-form-item__content {
              justify-content: center;

              .el-button {
                width: 100%;
              }
            }
          }
        }
      }
    }
  }
}

@media screen and (min-width: 575px) and (max-width: 768px) {
  .contact-page {
    .contact-body {
      flex-direction: column;
      gap: 1rem;

      .el-aside {
        width: 100%;
        padding: 1rem 0;

        .el-row {
          .el-col {
            padding: 1rem;

            .title {
              span {
                font-size: 0.88rem;
              }
            }

            .description {
              p {
                font-size: 0.75rem;
              }
            }
          }

          .el-divider {
            &.el-divider--horizontal {
              width: 90%;
              margin: 0.1rem auto;
            }
          }
        }
      }

      .el-main {
        width: 100%;

        .el-form {
          .input-group {
            .el-form-item {
              &:nth-child(2) {
                margin-bottom: 18px;
              }

              .el-form-item__content {
                .el-input {
                  width: 44rem;
                }
              }
            }
          }

          .textarea {
            width: 100%;
            margin-left: -5px;

            .el-form-item__content {
              .el-textarea {
                .el-textarea__inner {
                  padding: 0.6rem;
                  width: 44rem;
                }
              }
            }
          }

          .submit-button {
            .el-form-item__content {
              justify-content: center;

              .el-button {
                width: 44rem;
              }
            }
          }
        }
      }
    }
  }
}
</style>
