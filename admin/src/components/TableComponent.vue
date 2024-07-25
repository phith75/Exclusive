<template>
  <template v-if="items.length === 0">
    <el-empty description="No records found" />
  </template>
  <template v-else>
    <div class="box-table">
      <table>
        <thead>
          <tr>
            <th v-if="showCheckbox">
              <input
                type="checkbox"
                class="checkbox-many"
                ref="selectAllCheckbox"
                @change="toggleSelectAll"
              />
            </th>
            <th>#</th>
            <th
              v-for="header in headers"
              :key="header.key"
              :class="{ 'center-class': centerClass.includes(header.key) }"
            >
              {{ header.label }}
            </th>
            <th v-if="showActions || actionType == 'restore'">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="item.id">
            <td v-if="showCheckbox">
              <input
                type="checkbox"
                :value="item.id"
                v-model="selectedItems"
                @change="updateSelectedItems"
              />
            </td>
            <td>{{ index + 1 }}</td>

            <td
              v-for="header in headers"
              :key="header.key"
              :class="{ 'center-class': centerClass.includes(header.key) }"
            >
              <span
                class="image"
                v-if="header.label === 'Thumbnail' || header.label === 'Image'"
              >
                <img
                  :src="getImageUrl(item[header.key])"
                  alt="image"
                  width="100"
                  height="100"
                />
              </span>
              <span v-else-if="header.label === 'Status'">
                <select
                  :id="item.id"
                  v-model="item[header.key]"
                  :class="['select-control', getStatusClass(item[header.key])]"
                  @change="emit('changeStatus', item.id, item[header.key])"
                  :disabled="isSelectDisabled(item[header.key])"
                >
                  <option
                    v-for="option in statusOptions"
                    :key="option.value"
                    :value="option.value"
                    :disabled="isOptionDisabled(item[header.key], option.value)"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </span>
              <span v-else-if="header.label === 'Role'">
                <select
                  :id="item.id"
                  v-model="item[header.key]"
                  :class="['select-control', getRoleClass(item[header.key])]"
                  @change="emit('changeRole', item.id, item[header.key])"
                >
                  <option
                    v-for="option in roleOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </option>
                </select>
              </span>
              <span
                v-else-if="header.label === 'Status ticket'"
                :class="[
                  'status-ticket',
                  {
                    'status-approved':
                      getStatusLabel(item[header.key]) == 'Approved',
                    'status-returned':
                      getStatusLabel(item[header.key]) == 'Returned',
                  },
                ]"
              >
                {{ getStatusLabel(item[header.key]) }}
              </span>
              <span
                v-else-if="header.label == 'Gender'"
                :class="['text-ellipsis', getGenderClass(item[header.key])]"
              >
                {{ getGenderLabel(item[header.key]) }}
              </span>
              <span
                v-else
                :class="['text-ellipsis', getGenderClass(item[header.key])]"
              >
                {{ item[header.key] }}
              </span>
            </td>
            <td v-if="showActions">
              <div class="show-btn" v-if="editRouteName === 'ticket.detail'">
                <router-link :to="getEditLink(item.id)">
                  <img src="@/assets/images/view-details.png" alt="view" />
                </router-link>
              </div>
              <div v-else class="action-btns">
                <router-link v-if="editRouter" :to="getEditLink(item.id)">
                  <img src="@/assets/images/pencil-write.png" alt="edit" />
                </router-link>
                <a @click="openPopup(item.name, item.id)">
                  <img src="@/assets/images/bin.png" alt="delete" />
                </a>
              </div>
            </td>
            <td v-else-if="actionType == 'restore'">
              <div class="action-btns">
                <a @click="restoreItem(item.id)">
                  <img src="@/assets/images/restore-bin.png" alt="restore" />
                </a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>

  <div class="popup-overlay" v-if="showPopup">
    <div class="popup-content">
      <span class="close" @click="closePopup">&times;</span>
      <p>You want to delete {{ itemName }}?</p>
      <div class="btnDelete">
        <button class="btnConfirm btn--primary" @click="closePopup">
          Cancel
        </button>
        <button class="btnConfirm btn--danger" @click="confirmDelete(itemId)">
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, ref, type PropType } from "vue";
import type { Header, Item, Option } from "@/types/index";

const props = defineProps({
  headers: {
    type: Array as PropType<Header[]>,
    required: true,
  },
  items: {
    type: Array as PropType<Item[]>,
    required: true,
  },
  statusOptions: {
    type: Array as PropType<Option[]>,
    default: () => [],
  },
  roleOptions: {
    type: Array as PropType<Option[]>,
    default: () => [],
  },
  genderOptions: {
    type: Array as PropType<Option[]>,
    default: () => [],
  },
  showCheckbox: {
    type: Boolean,
    default: false,
  },
  editRouter: {
    type: Boolean,
    default: true,
  },
  restoreRouter: {
    type: Boolean,
    default: true,
    required: false,
  },
  showActions: {
    type: Boolean,
    default: false,
  },
  editRouteName: {
    type: String,
    required: false,
  },
  actionType: {
    type: String,
    default: "delete",
  },
});
const centerClass = [
  "borrowed_count",
  "wishlists_count",
  "reviews_count",
  "quantity",
  "num_borrowed_books",
  "reviews_count",
  "rating",
];

const emit = defineEmits([
  "edit",
  "delete",
  "deleteMany",
  "restoreMany",
  "changeStatus",
  "changeRole",
  "selectedItems",
  "restore",
]);
const restoreItem = (id: number) => {
  emit("restore", id);
};

const selectedItems = ref<number[]>([]);
const showPopup = ref(false);
const itemName = ref("");
const itemId = ref(0);
const selectAllCheckbox = ref<HTMLInputElement | null>(null);

const toggleSelectAll = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.checked) {
    selectedItems.value = props.items.map((item) => item.id);
  } else {
    selectedItems.value = [];
  }
  emit("selectedItems", selectedItems.value);
};

const updateSelectedItems = () => {
  emit("selectedItems", selectedItems.value);
};

const getEditLink = (id: number) => {
  return {
    name: props.editRouteName,
    params: { id },
  };
};

const getImageUrl = (imagePath: string) => {
  if (imagePath.startsWith("https")) {
    return imagePath;
  } else {
    return `${import.meta.env.VITE_IMAGE_URL}${imagePath}`;
  }
};

const getStatusLabel = (statusValue: string | number) => {
  const statusOption = props.statusOptions.find(
    (option) => option.value == statusValue
  );
  return statusOption ? statusOption.label : statusValue;
};
const getGenderLabel = (genderValue: string | number) => {
  const genderOption = props.genderOptions.find(
    (option) => option.value == genderValue
  );

  return genderOption ? genderOption.label : genderValue;
};
const getStatusClass = (statusValue: string | number) => {
  const statusOption = props.statusOptions.find(
    (option) => option.value == statusValue
  );
  if (statusOption) {
    const label = statusOption.label.toLowerCase();
    if (label === "active") return "select-active";
    if (label === "inactive") return "select-inactive";
    if (label === "approved") return "select-approved";
    if (label === "returned") return "select-returned";
    if (label === "borrowed") return "select-borrowed";
    if (label === "overdue") return "select-overdue";
    if (label === "lost") return "select-lost";
    if (label === "new") return "select-new";
  }
  return "";
};

const getRoleClass = (roleValue: string | number) => {
  const roleOption = props.roleOptions.find(
    (option) => option.value == roleValue
  );
  if (roleOption) {
    const label = roleOption.label.toLowerCase();
    if (label === "admin") return "select-admin";
    if (label === "user") return "select-user";
  }
  return "";
};

const getGenderClass = (genderValue: string | number) => {
  const genderOption = props.genderOptions.find(
    (option) => option.value == genderValue
  );
  if (genderOption) {
    const label = genderOption.label.toLowerCase();
    if (label === "male") return "color-male";
    if (label === "female") return "color-female";
    if (label === "other") return "color-other";
  }
  return "";
};

const isSelectDisabled = (statusValue: string | number) => {
  return statusValue == "returned";
};

const openPopup = (name: string, id: number) => {
  itemName.value = name;
  itemId.value = id;
  showPopup.value = true;
};

const closePopup = () => {
  showPopup.value = false;
};

const confirmDelete = (id: number) => {
  emit("delete", id);
  closePopup();
};

const isOptionDisabled = (
  statusValue: string | number,
  optionValue: string | number
) => {
  if (props.statusOptions.length !== 4) {
    return false;
  }
  if (statusValue == 2) {
    return true;
  }
  if (statusValue == 3 || statusValue == 4) {
    return optionValue != 2;
  }
  return false;
};
</script>

<style scoped>
.box-table {
  width: 100%;
  border-radius: 8px;
  border: 1px solid #d5d5d5;
  max-height: 100%;
  overflow: auto;
}

table {
  height: 100%;
  width: 100%;
  border-collapse: collapse;
}
.center-class {
  text-align: center;
}

thead {
  position: sticky;
  top: 0;
  z-index: 2;
  background-color: #f4f4f4;
}

tbody {
  height: 100%;
  overflow: auto;
  background-color: #fff;
}

th,
td {
  border-bottom: 1px solid #d5d5d5;
  text-align: left;
  padding: 8px;
  vertical-align: middle;
}

th {
  background-color: #f4f4f4;
  font-size: 16px;
  white-space: nowrap;
  font-weight: 600;
}

td {
  font-size: 14px;
  font-weight: 400;
}

th:nth-child(1),
td:nth-child(1) {
  text-align: center;
  position: sticky;
  left: 0;
  background-color: #f4f4f4;
  z-index: 1;
  width: 50px;
  border-right: 1px solid #d5d5d5;
}

th:last-child,
td:last-child {
  position: sticky;
  right: 0;
  background-color: #f4f4f4;
  z-index: 1;
  text-align: center;
  width: 100px;
  border-left: 1px solid #d5d5d5;
}

.checkboxDelete {
  display: flex;
  align-items: center;
}

table td input {
  width: 80%;
  height: 100%;
  margin: auto;
}

.table-cell {
  max-height: 100px;
}

.image img {
  outline: #4880ff 2px solid;
  border-radius: 8px;
}
.text-ellipsis {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 3;
  white-space: pre-wrap;
  overflow: hidden;
}

.action-btns a {
  padding: 5px 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-right: 1px solid #d5d5d5;
}
.no-record {
  display: flex;
  align-items: center;
}

input[type="checkbox"] {
  cursor: pointer;
  display: block;
  width: 20px;
  height: 20px;
  border: 2px solid #555555;
  border-radius: 3px;
  background-color: white;
}

.action-btns a:last-child {
  border-right: none;
}

img {
  max-width: 50px;
  max-height: 50px;
  display: block;
}

.action-btns {
  display: flex;
  align-items: center;
  max-width: 100px;
  justify-content: center;
  border-radius: 5px;
  background-color: #fff;

  border: 1px solid #d5d5d5;
}

.action-btns img {
  cursor: pointer;
  width: 20px;
  height: 20px;
}

.btn--danger-many,
.btn--restore-many {
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.btn--restore-many {
  background-color: #4caf50;
}

.btn--danger-many {
  background-color: #f44336;
}

.checkbox-many {
  cursor: pointer;
  margin: auto;
}

.show-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  margin-top: 10px;
}

.select-control {
  height: 40px;
  width: 120px;
}

/* Custom styles for select based on option class */
.select-active {
  color: green;
  background-color: #e1f0e1;
}

.select-inactive {
  color: red;
  background-color: #f5ecec;
}

.select-admin {
  color: rgb(0, 126, 243);
  background-color: #e1eaff;
}

.select-user {
  color: rgb(209, 179, 9);
  background-color: #fff5cc;
}

.select-approved {
  color: green;
  background-color: #e1f0e1;
}

.select-returned {
  color: green;
  background-color: #e1f0e1;
}

.select-borrowed {
  color: white;
  background-color: #1796ff;
}

.select-overdue {
  color: white;
  background-color: #ff9800;
}

.select-lost {
  color: red;
  background-color: #f5ecec;
}

.select-new {
  color: white;
  background-color: #ff9800;
}

/* Disabled option styles */
.select-control option:disabled {
  background-color: #d5d5d5; /* Light gray */
  color: #eeeeee; /* Darker gray */
  cursor: not-allowed;
}

/* Disabled select styles */
.select-control:disabled {
  background-color: #e0e0e0;
  color: #a0a0a0;
  cursor: not-allowed;
}

.status-ticket.status-approved {
  color: green;
  background-color: #e1f0e1;
  padding: 4px;
  border-radius: 4px;
}

.status-ticket.status-returned {
  color: red;
  background-color: #f5ecec;
  padding: 4px;
  border-radius: 4px;
}
</style>
