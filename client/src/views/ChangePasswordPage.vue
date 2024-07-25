<template>
  <div class="profile-container">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>Change Password</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="profile-content">
      <ProfileNav />
      <div class="password-edit">
        <h2>Change Password</h2>
        <el-form
          require-asterisk-position="right"
          :model="form"
          :rules="rules"
          ref="formRef"
          label-position="top"
        >
          <el-row :gutter="20">
            <el-col :span="24">
              <el-form-item label="Current Password" prop="currentPassword">
                <el-input v-model="form.currentPassword" type="password" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="New Password" prop="newPassword">
                <el-input v-model="form.newPassword" type="password" />
              </el-form-item>
            </el-col>
            <el-col :span="24">
              <el-form-item label="Confirm New Password" prop="confirmPassword">
                <el-input v-model="form.confirmPassword" type="password" />
              </el-form-item>
            </el-col>
          </el-row>
          <div class="form-actions">
            <el-button @click="cancelEdit">Cancel</el-button>
            <el-button type="primary" @click="changePassword"
              >Save Changes</el-button
            >
          </div>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";
import ProfileNav from "@/components/NavMenuComponent.vue";
import authService from "@/services/authService";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();
const router = useRouter();

const formRef = ref(null);

const form = reactive({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

const rules = reactive({
  currentPassword: [
    {
      required: true,
      message: "Please input current password",
      trigger: "blur",
    },
  ],
  newPassword: [
    { required: true, message: "Please input new password", trigger: "blur" },
  ],
  confirmPassword: [
    {
      required: true,
      message: "Please confirm your new password",
      trigger: "blur",
    },
    {
      validator: (_rule, value, callback) => {
        if (value !== form.newPassword) {
          callback(new Error("Passwords do not match"));
        } else {
          callback();
        }
      },
      trigger: "blur",
    },
  ],
});

const cancelEdit = () => {
  router.push("/home");
};

const changePassword = async () => {
  formRef.value.validate(async (valid) => {
    if (valid) {
      try {
        const response = await authService.changePassword(
          form.currentPassword,
          form.newPassword,
          form.confirmPassword
        );
        if (response.success == 1) {
          authStore.logout();
          toast.success("Password updated successfully!");
        } else {
          toast.error(response.message || "Failed to update password.");
        }
      } catch (error: any) {
        if (error && error.status === 422 && error.data.errors.error_message) {
          toast.error(error.data.errors.error_message);
        }
      }
    }
  });
};
</script>

<style scoped lang="scss">
.profile-container {
  width: 100%;
  max-width: 1170px;
  margin: 40px auto 140px auto;
  padding: 0 15px;

  .profile-content {
    margin-top: 2rem;
    display: flex;
    .password-edit {
      width: 100%;
      background: #fff;
      padding: 20px;
      border: 1px solid #ebeef5;
      border-radius: 4px;

      h2 {
        margin-bottom: 20px;
        font-size: 24px;
        color: var(--primary-color);
      }

      .el-form-item {
        margin-bottom: 20px;

        .el-input {
          width: 100%;
          height: 40px;
          background-color: #f3f3f3;
        }
      }

      .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;

        .el-button {
          height: 40px;
          font-size: 16px;
        }

        .el-button--primary {
          background-color: var(--primary-color);
          border-color: var(--primary-color);
          color: white;
        }
      }
    }
  }
}

@media (max-width: 768px) {
  .profile-content {
    flex-direction: column;
    .password-edit {
      width: 100%;
    }
  }
  .form-actions {
    display: flex;
    justify-content: center;
    flex-direction: column-reverse;
    gap: 5px;

    .el-button {
      margin: 0px;
      height: 20px;
      font-size: 16px;
    }
  }

  .el-row {
    flex-direction: column;
  }

  .el-col {
    width: 100% !important;
  }
}
</style>
