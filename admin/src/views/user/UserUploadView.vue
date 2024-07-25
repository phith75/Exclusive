<template>
  <section class="section-content">
    <div class="content">
      <div class="content-header">
        <h1>Upload Excel File</h1>
      </div>
      <div
        class="upload-group"
        :class="{ 'is-dragover': isDragOver, error: errorMessage }"
        @dragover.prevent="isDragOver = true"
        @dragleave.prevent="isDragOver = false"
        @drop.prevent="handleDrop"
        @click="triggerFileInput"
      >
        <label :for="id" class="upload-label">{{ label }}</label>
        <input
          type="file"
          :id="id"
          @change="handleFileChange"
          :class="{ error: errorMessage }"
          accept=".xlsx,.xls,.csv"
        />
        <div v-if="rowCount !== null" class="row-count">
          Number of staff: {{ rowCount }}
        </div>
        <div v-else class="upload-placeholder">
          Drag & drop an Excel file here, or click to select one
        </div>
      </div>
      <span v-if="errorMessage" class="upload-error-message">{{
        errorMessage
      }}</span>
      <button class="submit-button" @click="submitFile" v-if="file">
        Submit
      </button>
      <div v-if="validationErrors.length" class="validation-errors">
        <h2>Validation Errors</h2>
        <table>
          <thead>
            <tr>
              <th>Field</th>
              <th>Error Message</th>
              <th>Invalid Value</th>
              <th>Row</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(error, index) in validationErrors" :key="index">
              <td>{{ error[0] }}</td>
              <td>{{ error[1] }}</td>
              <td>{{ error[2] }}</td>
              <td>{{ error[3] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="content">
      <div class="content-header">
        <h1>List of Imported Staff</h1>
      </div>
      <div class="table-component">
        <TableComponent
          :headers="staffHeaders"
          :items="staff"
          :showCheckbox="false"
          :showActions="false"
        />
      </div>
    </div>
  </section>
</template>
<script setup lang="ts">
import { reactive, ref, onMounted } from "vue";
import ExcelJS from "exceljs";
import { toast } from "vue3-toastify";
import userService from "@/services/userService";
import TableComponent from "@/components/TableComponent.vue";
import type { FormErrors, ImportStaffData, StaffList } from "@/types";

const label = "Upload Excel File";
const id = "excel-file-upload";
const isDragOver = ref(false);
const file = ref<File | null>(null);
const errorMessage = ref<string | null>(null);
const rowCount = ref<number | null>(null);
const formData = reactive<ImportStaffData>({
  user_file: null,
});
const errors = reactive<FormErrors>({
  user_file: null,
});
const validationErrors = ref<Array<[string, string, string, string]>>([]);
const staff = ref<StaffList[]>([]);

const staffHeaders = [
  { key: "user_code", label: "User Code" },
  { key: "name", label: "Name" },
  { key: "email", label: "Email" },
];

async function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement | null;
  if (target && target.files && target.files.length > 0) {
    const selectedFile = target.files[0];
    if (validateFileType(selectedFile)) {
      file.value = selectedFile;
      errors.user_file = null;
      await updateRowCount(file.value);
    } else {
      errors.user_file = ["File must be of type: .xlsx, .xls, .csv"];
    }
  }
}

async function handleDrop(event: DragEvent) {
  isDragOver.value = false;
  if (event.dataTransfer?.files && event.dataTransfer.files.length > 0) {
    const droppedFile = event.dataTransfer.files[0];
    if (validateFileType(droppedFile)) {
      file.value = droppedFile;
      errorMessage.value = null;
      errors.user_file = null;
      await updateRowCount(file.value);
    } else {
      errors.user_file = ["File must be of type: .xlsx, .xls, .csv"];
    }
  }
}

function triggerFileInput() {
  const fileInput = document.getElementById(id);
  if (fileInput) {
    (fileInput as HTMLInputElement).click();
  }
}

async function updateRowCount(file: File) {
  const workbook = new ExcelJS.Workbook();
  await workbook.xlsx.load(await file.arrayBuffer());
  const worksheet = workbook.worksheets[0];
  let count = 0;

  worksheet.eachRow({ includeEmpty: true }, (_row, rowIndex) => {
    if (rowIndex > 1) {
      count++;
    }
  });
  rowCount.value = count;
}

function validateFileType(file: File) {
  const allowedExtensions = ["xlsx", "xls", "csv"];
  const fileExtension = file.name.split(".").pop()?.toLowerCase();
  return allowedExtensions.includes(fileExtension || "");
}

async function submitFile() {
  if (!file.value) return;

  try {
    formData.user_file = file.value;
    const response = await userService.importStaff(formData);
    if (response.success === 1) {
      toast.success("File uploaded successfully");
      fetchStaffData();
      validationErrors.value = [];
    } else {
      throw new Error(response.message || "Failed to upload file");
    }
  } catch (error: any) {
    if (error && error.response.status == 422 && error.response.data.data) {
      Object.assign(errors, error.response.data.data);
      validationErrors.value = extractValidationErrors();
    }
  }
}

function extractValidationErrors() {
  const validationErrors: Array<[string, string, string, string]> = [];
  for (const [key, value] of Object.entries(errors)) {
    if (value) {
      validationErrors.push([key, value[0], value[1], value[2]]);
    }
  }
  return validationErrors;
}

async function fetchStaffData() {
  try {
    const response = await userService.listStaff();
    if (response.success == 1) {
      staff.value = response.data;
    } else {
      throw new Error(response.message || "Failed to fetch staff data");
    }
  } catch (error: any) {
    toast.error("Error fetching staff data");
  }
}

onMounted(() => {
  fetchStaffData();
});
</script>
<style scoped>
.content {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: start;
  background-color: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  margin-top: 20px; /* Add margin-top for spacing between sections */
}

.content-header h1 {
  margin-bottom: 20px;
  font-size: 24px;
  color: #333;
}
.table-component{
  width: 100%;
}

.upload-group {
  margin-bottom: 20px;
  width: 100%;
  border: 2px dashed #d5d5d5;
  border-radius: 8px;
  padding: 40px 20px;
  text-align: center;
  transition:
    background-color 0.3s,
    border-color 0.3s;
  position: relative;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.upload-group.is-dragover {
  background-color: #f0f0f0;
}

.upload-group.error {
  border-color: red;
}

.upload-group input {
  display: none;
}

.upload-error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}

.upload-placeholder {
  color: #999;
  font-size: 16px;
  margin-top: 10px;
}

.upload-label {
  display: block;
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
}

.row-count {
  margin-top: 10px;
  font-size: 16px;
}

.submit-button {
  margin-top: 20px;
  padding: 10px 20px;
  font-size: 16px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover {
  background-color: #0056b3;
}

.validation-errors {
  margin-top: 20px;
  width: 100%;
  max-width: 800px;
}

.validation-errors h2 {
  color: red;
  margin-bottom: 10px;
}

.validation-errors table {
  width: 100%;
  border-collapse: collapse;
}

.validation-errors th,
.validation-errors td {
  border: 1px solid #ddd;
  padding: 8px;
}

.validation-errors th {
  background-color: #f2f2f2;
  text-align: left;
}

.validation-errors tr {
  background-color: #fdd;
}
.validation-errors td {
  color: red;
}

/* Styles for the staff table */
.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.content-header h1 {
  margin-bottom: 0;
}

.content-header a {
  text-decoration: none;
  color: #007bff;
  font-size: 16px;
  transition: color 0.3s;
}

.content-header a:hover {
  color: #0056b3;
}

/* Table styles if the TableComponent uses default table styles */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

th,
td {
  padding: 8px 12px;
  border: 1px solid #ddd;
  text-align: left;
}

th {
  background-color: #f2f2f2;
}

tr:nth-child(even) {
  background-color: #f9f9f9;
}

tr:hover {
  background-color: #f1f1f1;
}
</style>
