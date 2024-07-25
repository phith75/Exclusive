<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Users</h1>
      </div>
    </div>
    <div class="action-filter">
      <div class="actions">
        <router-link :to="{ name: 'user.upload' }">Import Staffs</router-link>
      </div>
      <div class="filters">
        <div class="filter-left">
          <div class="group-select">
            <label for="">Select Role</label>
            <select v-model="selectedRole">
              <option value="">All Roles</option>
              <option
                v-for="role in roleOptions"
                :key="role.value"
                :value="role.value"
              >
                {{ role.label }}
              </option>
            </select>
          </div>
          <div class="group-select">
            <label for="">Select Status</label>
          <select v-model="selectedStatus">
            <option value="">All Statuses</option>
            <option
              v-for="status in statusOptions"
              :key="status.value"
              :value="status.value"
            >
              {{ status.label }}
            </option>
          </select>
          </div>
          <div class="group-select">
            <label for="">Select Gender</label>
          <select v-model="selectedGender">
            <option value="">All Genders</option>
            <option
              v-for="gender in genderOptions"
              :key="gender.value"
              :value="gender.value"
            >
              {{ gender.label }}
            </option>
          </select>
          </div>
        </div>
        <div class="filter-right">
          <SearchComponent v-model="searchQuery" content="users" />
        </div>
        <div class="button-filter">
          <button class="btn-filter" @click="filterUsers">Filter</button>
          <button class="btn-clear" @click="clearFilters">Clear</button>
        </div>
      </div>
    </div>
    <div class="table-component">
      <TableComponent
        :headers="userHeaders"
        :items="users"
        :statusOptions="statusOptions"
        :roleOptions="roleOptions"
        :genderOptions="genderOptions"
        @changeStatus="changeStatus"
        @changeRole="changeRole"
        :showCheckbox="false"
        :showActions="false"
      />
    </div>
    <PaginationComponent
      v-if="totalPages > 1"
      :currentPage="currentPage"
      :totalPages="totalPages"
      @goToPage="goToPage"
      @prevPage="prevPage"
      @nextPage="nextPage"
    />
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import userService from "@/services/userService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import type { UserData } from "@/types/index";
import { useAuthStore } from "@/stores/authStore";
import {
  STATUS_USER_OPTIONS,
  ROLE_OPTIONS,
  GENDER_OPTIONS,
  convertEnumToOptions,
} from "@/enum/Constants.ts";

// State variables
const users = ref<UserData[]>([]);
const currentPage = ref(1);
const totalPages = ref(1);
const error = ref<string | null>(null);
const isLoading = ref(false);

const searchQuery = ref("");

const selectedRole = ref("");
const selectedStatus = ref("");
const selectedGender = ref("");

// Options for filters
const statusOptions = convertEnumToOptions(STATUS_USER_OPTIONS);
const roleOptions = convertEnumToOptions(ROLE_OPTIONS);
const genderOptions = convertEnumToOptions(GENDER_OPTIONS);
const authStore = useAuthStore();

const user = computed(() => authStore.getUser);
const userDecode = computed(() => {
  if (user.value) {
    return JSON.parse(user.value);
  }
  return {};
});

const currentUserId = userDecode.value.id;

const userHeaders = [
  { key: "user_code", label: "User Code" },
  { key: "name", label: "Name" },
  { key: "email", label: "Email" },
  { key: "address", label: "Address" },
  { key: "phone", label: "Phone" },
  { key: "date_of_birth", label: "Date of Birth" },
  { key: "gender", label: "Gender" },
  { key: "num_borrowed_books", label: "Borrowed Books" },
  { key: "reviews_count", label: "Reviews" },
  { key: "role", label: "Role" },
  { key: "status", label: "Status" },
];

const fetchUsers = async (
  page = 1,
  keyword = "",
  status = "",
  role = "",
  gender = ""
) => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await userService.getUsers(
      page,
      keyword,
      status,
      role,
      gender
    );

    if (response.success === 1) {
      users.value = response.data.map((user: UserData) => ({
        ...user,
        reviews_count: user.reviews.length,
        address: user.address
          ? user.address +
            ", " +
            user.ward_name +
            ", " +
            user.district_name +
            ", " +
            user.province_name
          : "-",
      }));

      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch users");
    }
  } catch (err: any) {
    error.value = err.message;
    toast.error(`Error: ${err.message}`);
  } finally {
    isLoading.value = false;
  }
};

const changeStatus = async (id: number, newStatus: string) => {
  if (id == currentUserId) {
    toast.error("You cannot change your own status.");
  } else {
    try {
      const response = await userService.updateStatusUser(id, {
        status: newStatus,
      });
      if (response.success === 1) {
        toast.success("User status updated successfully");
      } else {
        throw new Error(response.message || "Failed to update user status");
      }
    } catch (err: any) {
      toast.error(`Error: ${err.message}`);
    }
  }
  fetchUsers(
    currentPage,
    searchQuery.value,
    selectedStatus.value,
    selectedRole.value,
    selectedGender.value
  );
};

const changeRole = async (id: number, newRole: string) => {
  if (id === currentUserId) {
    toast.error("You cannot change your own role.");
  } else {
    try {
      const response = await userService.updateRoleUser(id, {
        role: newRole,
      });
      if (response.success === 1) {
        toast.success("User role updated successfully");
      } else {
        throw new Error(response.message || "Failed to update user role");
      }
    } catch (err: any) {
      toast.error(`Error: ${err.message}`);
    }
  }
  fetchUsers(
    currentPage,
    searchQuery.value,
    selectedStatus.value,
    selectedRole.value,
    selectedGender.value
  );
};

// Fetch users on component mount
onMounted(() => {
  fetchUsers();
});

// Pagination functions
const goToPage = (page: number) => {
  fetchUsers(
    page,
    searchQuery.value,
    selectedStatus.value,
    selectedRole.value,
    selectedGender.value
  );
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchUsers(
      currentPage.value - 1,
      searchQuery.value,
      selectedStatus.value,
      selectedRole.value,
      selectedGender.value
    );
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchUsers(
      currentPage.value + 1,
      searchQuery.value,
      selectedStatus.value,
      selectedRole.value,
      selectedGender.value
    );
  }
};

const currentFilter = ref({
  status: "",
  role: "",
  gender: "",
  keyword: "",
});

const filterUsers = () => {
  const newFilter = {
    status: selectedStatus.value,
    role: selectedRole.value,
    gender: selectedGender.value,
    keyword: searchQuery.value,
  };
  if (JSON.stringify(newFilter) == JSON.stringify(currentFilter.value)) {
    if (
      newFilter.keyword == "" &&
      newFilter.status == "" &&
      newFilter.role == "" &&
      newFilter.gender == ""
    ) {
      return;
    }
    return;
  }

  fetchUsers(
    1,
    searchQuery.value,
    selectedStatus.value,
    selectedRole.value,
    selectedGender.value
  );

  currentFilter.value = newFilter;
};

const clearFilters = () => {
  if (
    searchQuery.value == "" &&
    selectedStatus.value == "" &&
    selectedRole.value == "" &&
    selectedGender.value == ""
  ) {
    return;
  }
  searchQuery.value = "";
  selectedRole.value = "";
  selectedStatus.value = "";
  selectedGender.value = "";
  fetchUsers(1, "", "", "", "");
};
</script>
<style scoped lang="scss">
.content-header {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.action-filter {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.actions {
  display: flex;
  gap: 10px;
}

.filters {
  display: flex;
  align-items: end;
  justify-content: end;
  gap: 10px;
  margin: 10px 0;
}

.filter-left {
  display: flex;
  gap: 10px;
  select {
    width: 150px;
    height: 38px;
    background-color: #fff;
    padding: 0 10px;
  }
}

.button-filter {
  display: flex;
  gap: 10px;
  button {
    width: 60px;
    height: 38px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s;
  }
  .btn-filter {
    background-color: #007bff;
    color: white;
  }
  .btn-clear {
    background-color: #dc3545;
    color: white;
  }
}
</style>
