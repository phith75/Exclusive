<template>
  <div class="profile-container">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>Edit Profile</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="profile-content">
      <ProfileNav />
      <div class="profile-edit">
        <h2>Edit Your Profile</h2>
        <el-form
          require-asterisk-position="right"
          :model="form"
          :rules="rules"
          ref="formRef"
          label-position="top"
        >
          <el-row :gutter="20">
            <el-col :span="12" :xs="24">
              <el-form-item label="User Code" prop="user_code">
                <el-input v-model="form.user_code" disabled />
              </el-form-item>
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Name" prop="name">
                <el-input v-model="form.name" />
              </el-form-item>
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Email" prop="email">
                <el-input v-model="form.email" disabled />
              </el-form-item>
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Phone" prop="phone">
                <el-input v-model="form.phone" />
              </el-form-item>
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Date of Birth" prop="date_of_birth">
                <el-date-picker
                  :disabled-date="disabledDate"
                  v-model="form.date_of_birth"
                  type="date"
                  placeholder="Select your birth date"
                  format="YYYY-MM-DD"
                />
              </el-form-item>
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Address" prop="address">
                <el-input v-model="form.address" />
              </el-form-item>
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Province" prop="province_id">
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
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="District" prop="district_id">
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
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Ward" prop="ward_id">
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
            </el-col>
            <el-col :span="12" :xs="24">
              <el-form-item label="Gender" prop="gender">
                <el-select
                  v-model="form.gender"
                  placeholder="Select your gender"
                >
                  <el-option
                    v-for="(label, value) in convertGender"
                    :key="value"
                    :label="label.label"
                    :value="Number(label.value)"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
          <div class="form-actions">
            <el-button @click="cancelEdit">Cancel</el-button>
            <el-button type="primary" :loading="isLoading" @click="saveChanges"
              >Save Changes</el-button
            >
          </div>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, reactive, watch } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/authStore";
import { toast } from "vue3-toastify";
import ProfileNav from "@/components/NavMenuComponent.vue";
import authService from "@/services/authService";
import addressService from "@/services/addressService";
import { Gender, convertEnumToOptions } from "@/enum/Constants";
import moment from "moment";

import type { ProvinceData, DistrictData, WardData } from "@/types";

const authStore = useAuthStore();
const router = useRouter();
const convertGender = convertEnumToOptions(Gender);
const formRef = ref(null);
const isLoading = ref(false);

const form = reactive({
  name: authStore.getUser?.name || "",
  email: authStore.getUser?.email || "",
  user_code: authStore.getUser?.user_code || "",
  phone: authStore.getUser?.phone || "",
  address: authStore.getUser?.address || "",
  province_id: authStore.getUser?.province_id || null,
  district_id: authStore.getUser?.district_id || null,
  ward_id: authStore.getUser?.ward_id || null,
  gender: authStore.getUser?.gender,
  date_of_birth: authStore.getUser?.date_of_birth || "",
  province_name: authStore.getUser?.province_name || "",
  district_name: authStore.getUser?.district_name || "",
  ward_name: authStore.getUser?.ward_name || "",
});

const rules = reactive({
  name: [
    { required: true, message: "Please input your name", trigger: "blur" },
    { type: "string", message: "Name must be a string", trigger: "blur" },
    {
      max: 255,
      message: "Name must not exceed 255 characters",
      trigger: "blur",
    },
  ],
  phone: [
    { required: true, message: "Please input your phone", trigger: "blur" },
    { type: "string", message: "Phone must be a string", trigger: "blur" },
    {
      min: 10,
      message: "Phone must be at least 10 characters",
      trigger: "blur",
    },
    {
      max: 16,
      message: "Phone must not exceed 16 characters",
      trigger: "blur",
    },
  ],
  gender: [
    {
      required: true,
      message: "Please select your gender",
      trigger: "change",
    },
  ],
  address: [
    { required: true, message: "Please input your address", trigger: "blur" },
    { type: "string", message: "Address must be a string", trigger: "blur" },
    {
      max: 255,
      message: "Address must not exceed 255 characters",
      trigger: "blur",
    },
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
  date_of_birth: [
    {
      required: true,
      type: "date",
      message: "Please input a valid date",
      trigger: ["blur", "change"],
    },
    {
      validator: (_rule, value, callback) => {
        const today = new Date();
        const minDate = new Date("1900-01-01");
        const selectedDate = new Date(value);

        if (selectedDate >= today) {
          callback(new Error("Date of birth must be before today"));
        } else if (selectedDate <= minDate) {
          callback(new Error("Date of birth must be after 1900-01-01"));
        } else {
          callback();
        }
      },
      trigger: ["blur", "change"],
    },
  ],
});

const provinces = ref<ProvinceData[]>([]);
const districts = ref<DistrictData[]>([]);
const wards = ref<WardData[]>([]);
const disabledDate = (time: Date) => {
  return moment(time) > moment();
};

const cancelEdit = () => {
  router.push("/home");
};
const currentValue = {
  name: form.name,
  phone: form.phone,
  gender: form.gender,
  date_of_birth: form.date_of_birth,
  address: form.address,
  province_id: form.province_id,
  district_id: form.district_id,
  ward_id: form.ward_id,
};
const saveChanges = () => {
  const newValue = {
    name: form.name,
    phone: form.phone,
    gender: form.gender,
    date_of_birth: form.date_of_birth,
    address: form.address,
    province_id: form.province_id,
    district_id: form.district_id,
    ward_id: form.ward_id,
  };
  formRef.value.validate(async (valid) => {
    if (valid) {
      if (JSON.stringify(currentValue) == JSON.stringify(newValue)) {
        return;
      }
      try {
        isLoading.value = true;
        await authService.updateProfile(
          newValue.name,
          newValue.phone,
          newValue.gender,
          newValue.address,
          newValue.province_id,
          newValue.district_id,
          newValue.ward_id,
          moment(form.date_of_birth).format("YYYY-MM-DD")
        );
        toast.success("Profile updated successfully!");
        currentValue= newValue;
        authStore.me();
      } catch (error: any) {
        if (error && error.status === 422 && error.data.data) {
          const errorFields = Object.keys(error.data.data).map((key) => ({
            name: key,
            errors: error.data.data[key],
          }));
          formRef.value.setFields(errorFields);
        }
        toast.error("Error updating profile");
      } finally {
        isLoading.value = false;
      }
    }
  });
};

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
  form.district_name = "";
  form.ward_name = "";
  districts.value = [];
  wards.value = [];
  fetchDistricts();
};

const handleDistrictSelect = (value: number) => {
  form.district_id = value;
  form.ward_id = null;
  form.ward_name = "";
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

    .profile-edit {
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

        .el-input,
        .el-date-picker {
          width: 100%;
          height: 40px;
          background-color: #f3f3f3;
        }
        :deep(.el-form-item__content) {
          height: 40px;
        }

        .el-select {
          width: 100%;
          height: 40px;
        }
        :deep(.el-select__wrapper) {
          height: 40px;
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
    .profile-edit {
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
