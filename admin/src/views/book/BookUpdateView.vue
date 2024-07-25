<template>
  <section class="section-content">
    <div class="content">
      <h1>Add Book</h1>
    </div>
    <div class="form-content">
      <form @submit.prevent="updateBook">
        <div class="form-layout">
          <div class="form-column">
            <InputComponent
              label="Name"
              type="text"
              id="name"
              v-model="formData.name"
              placeholder="Enter book name"
              :errorMessage="errors.name ? errors.name[0] : ''"
            />
          </div>

          <div class="form-column">
            <InputComponent
              label="Quantity"
              type="number"
              id="quantity"
              v-model="formData.quantity"
              placeholder="Enter quantity"
              :errorMessage="errors.quantity ? errors.quantity[0] : ''"
            />
          </div>
        </div>
        <div class="form-layout">
          <div class="form-column">
            <SelectComponent
              label="Category"
              id="category_id"
              v-model="formData.category_id"
              :options="categoryOptions"
              :errorMessage="errors.category_id ? errors.category_id[0] : ''"
            />
          </div>

          <div class="form-column">
            <SelectComponent
              label="Author"
              id="author_id"
              v-model="formData.author_id"
              :options="authorOptions"
              :errorMessage="errors.author_id ? errors.author_id[0] : ''"
            />
          </div>

          <div class="form-column">
            <SelectComponent
              label="Publisher"
              id="publisher_id"
              v-model="formData.publisher_id"
              :options="publisherOptions"
              :errorMessage="errors.publisher_id ? errors.publisher_id[0] : ''"
            />
          </div>
        </div>
        <div class="form-layout-full">
          <div class="form-column-full">
            <ImageUploadComponent
              label="Thumbnail"
              id="thumbnail"
              v-model="formData.thumbnail"
              placeholder="Upload book thumbnail"
              :errorMessage="errors.thumbnail ? errors.thumbnail[0] : ''"
            />
          </div>

          <div class="form-column-full">
            <ImageUploadMultipleComponent
              label="Images"
              id="images"
              v-model="formData.images"
              placeholder="Upload images"
              :errorMessage="errors.images ? errors.images[0] : ''"
              multiple
            />
          </div>
        </div>
        <div class="form-layout-full">
          <div class="form-column-full form-textarea">
            <label class="required" for="short_description"
              >Short Description</label
            >
            <textarea
              id="short_description"
              v-model="formData.short_description"
              placeholder="Enter short description"
              :class="{ error: errors.short_description }"
            ></textarea>
            <span
              v-if="errors.short_description"
              class="error-message-textarea"
              >{{ errors.short_description[0] }}</span
            >
          </div>

          <div class="form-column-full" :class="{ error: errors.description }">
            <label class="required" for="description">Description</label>
            <QuillEditor
              :options="editorOptions"
              class="editor ql-container ql-snow"
              contentType="html"
              v-model:content="formData.description"
            />
            <span v-if="errors.description" class="error-message-textarea">{{
              errors.description[0]
            }}</span>
          </div>
        </div>

        <button type="submit" id="add-book">Submit</button>
      </form>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, reactive, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import InputComponent from "@/components/InputComponent.vue";
import SelectComponent from "@/components/SelectComponent.vue";
import ImageUploadComponent from "@/components/ImageUploadComponent.vue";
import ImageUploadMultipleComponent from "@/components/ImageUploadMultipleComponent.vue";
import bookService from "@/services/bookService";
import { toast } from "vue3-toastify";
import type { Option, UpdateBookData } from "@/types/index";
import type { FormErrors } from "@/types/index";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

const route = useRoute();
const router = useRouter();
const bookId = route.params.id as string;

const formData = reactive<UpdateBookData>({
  name: "",
  thumbnail: null,
  category_id: "",
  author_id: "",
  publisher_id: "",
  description: "",
  short_description: "",
  quantity: 1,
  images: [],
});

const errors = reactive<FormErrors>({
  name: null,
  thumbnail: null,
  category_id: null,
  author_id: null,
  publisher_id: null,
  description: null,
  short_description: null,
  quantity: null,
  images: null,
});

const categoryOptions = ref<Option[]>([]);
const authorOptions = ref<Option[]>([]);
const publisherOptions = ref<Option[]>([]);

const editorOptions = {
  theme: "snow",
  modules: {
    toolbar: [
      ["bold", "italic", "underline", "strike"],
      ["blockquote", "code-block"],
      [{ header: 1 }, { header: 2 }],
      [{ list: "ordered" }, { list: "bullet" }],
      [{ script: "sub" }, { script: "super" }],
      [{ indent: "-1" }, { indent: "+1" }],
      [{ direction: "rtl" }],
      [{ size: ["small", false, "large", "huge"] }],
      [{ header: [1, 2, 3, 4, 5, 6, false] }],
      [{ color: [] }, { background: [] }],
      [{ font: [] }],
      [{ align: [] }],
      ["clean"],
    ],
  },
};

onMounted(async () => {
  const [authors, categories, publishers] = await Promise.all([
    bookService.getAuthors(),
    bookService.getCategories(),
    bookService.getPublishers(),
  ]);
  await getBookDetails(),
    (authorOptions.value = authors.data.map((author: any) => ({
      value: author.id,
      label: author.name,
    })));

  categoryOptions.value = categories.data.map((category: any) => ({
    value: category.id,
    label: category.name,
  }));

  publisherOptions.value = publishers.data.map((publisher: any) => ({
    value: publisher.id,
    label: publisher.name,
  }));
});

async function getBookDetails() {
  const response = await bookService.getBookById(bookId);
  const bookData = response.data;

  formData.name = bookData.name;
  formData.quantity = bookData.quantity;
  formData.category_id = bookData.category_id;
  formData.author_id = bookData.author_id;
  formData.publisher_id = bookData.publisher_id;
  formData.description = bookData.description;
  formData.short_description = bookData.short_description;
  formData.thumbnail = bookData.thumbnail;
  formData.images = bookData.images;
}

function validateForm() {
  let isValid = true;

  if (!formData.name.trim()) {
    errors.name = ["Name is required"];
    isValid = false;
  } else {
    errors.name = null;
  }

  if (formData.quantity <= 0) {
    errors.quantity = ["Quantity must be greater than zero"];
    isValid = false;
  } else {
    errors.quantity = null;
  }

  if (formData.category_id === -1) {
    errors.category_id = ["Category is required"];
    isValid = false;
  } else {
    errors.category_id = null;
  }

  if (formData.author_id === -1) {
    errors.author_id = ["Author is required"];
    isValid = false;
  } else {
    errors.author_id = null;
  }

  if (formData.publisher_id === -1) {
    errors.publisher_id = ["Publisher is required"];
    isValid = false;
  } else {
    errors.publisher_id = null;
  }

  if (!formData.short_description.trim()) {
    errors.short_description = ["Short description is required"];
    isValid = false;
  } else if (formData.short_description.length > 255) {
    errors.short_description = [
      "Short description must be less than 255 characters",
    ];
    isValid = false;
  } else {
    errors.short_description = null;
  }

  // Validate Short Description
  if (!formData.short_description.trim()) {
    errors.short_description = ["Short description is required"];
    isValid = false;
  } else if (formData.short_description.length > 255) {
    errors.short_description = [
      "Short description must be less than 255 characters",
    ];
    isValid = false;
  } else {
    errors.short_description = null;
  }
  if (removeHtmlTags(formData.description) == "") {
    errors.description = ["Description is required"];
    isValid = false;
  } else {
    errors.description = null;
  }

  if (
    !formData.thumbnail ||
    (formData.thumbnail && formData.thumbnail.size === 0)
  ) {
    errors.thumbnail = ["Thumbnail is required"];
    isValid = false;
  } else {
    errors.thumbnail = null;
  }

  // Validate Images
  if (!formData.images.length) {
    errors.images = ["Images are required"];
    isValid = false;
  } else {
    errors.images = null;
  }

  return isValid;
}
function removeHtmlTags(str: string) {
  return str.replace(/<\/?[^>]+>/gi, "");
}
async function updateBook() {
  if (!validateForm()) {
    return;
  }
  try {
    const oldImagesArr: string[] = [];
    const newImagesArr: File[] = [];

    Object.keys(errors).forEach((key) => (errors[key] = null));

    formData.images?.forEach((img) => {
      if (img instanceof File) {
        newImagesArr.push(img);
      } else {
        oldImagesArr.push(img.image as string);
      }
    });
    formData.old_images = oldImagesArr;

    formData.images = newImagesArr;

    if (!(formData.thumbnail instanceof File)) {
      formData.thumbnail = null;
    }

    const response = await bookService.updateBook(bookId, formData);
    if (response.success) {
      router.push({ name: "book.list" });
      setTimeout(() => {
        toast.success("Book updated successfully");
      }, 200);
    }
  } catch (error: any) {
    if (error && error.response.status === 422 && error.response.data.data) {
      Object.assign(errors, error.response.data.data);
    }
  }
}
</script>

<style scoped>
.section-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
}

.form-content {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.form-layout {
  display: flex;
  width: 100%;
  gap: 20px;
  justify-content: space-between;
}
.editor.error {
  border: 1px solid red !important;
}

.form-column {
  flex: 1 1 calc(50% - 20px);
  min-width: 300px;
  margin-top: 10px;
}

.form-layout-full {
  display: flex;
  flex-direction: column;
  margin-top: 10px;
  gap: 20px;
  width: 100%;
}

.form-column-full {
  width: 100%;
}

button {
  margin-top: 20px;
}

#book-image-upload.upload-group {
  margin-bottom: 20px;
  width: 100%;
}

#book-image-upload.upload-group input {
  width: 100%;
  height: 50px;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
  padding: 0 20px;
  font-size: 16px;
}

#book-image-upload .upload-error-message {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}

#book-image-upload.upload-group input.error {
  border: 1px solid red;
}

#book-image-upload .upload-image-preview {
  margin-top: 10px;
}

#book-image-upload .upload-image-preview img {
  max-width: 100%;
  max-height: 200px;
}

.form-column-full textarea {
  width: 100%;
  height: 100px;
  border: 1px solid #d5d5d5;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 16px;
  resize: vertical;
}

.form-column-full textarea.error {
  border: 1px solid red;
}

.error-message-textarea {
  color: red;
  font-size: 12px;
  margin-top: 5px;
}

.quill-editor {
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 16px;
}

:deep(.ql-toolbar) {
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  margin-top: 5px;
}

:deep(.ql-container) {
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
  margin-bottom: 5px;
}

:deep(.ql-editor) {
  font-family: "Arial", sans-serif;
  font-size: 16px;
  line-height: 1.6;
  height: 100%;
  min-height: 300px;
}

.ql-toolbar {
  background-color: #f0f0f0;
  border-radius: 4px;
}

:deep(.ql-toolbar button) {
  margin: 5px 0px !important;
}

.ql-toolbar button:hover {
  background-color: #e0e0e0;
}

.ql-toolbar button.ql-active {
  background-color: #d0d0d0;
}
:deep(.ql-tooltip, .ql-hidden) {
  display: none !important;
}

.required:after {
  content: "*";
  color: red;
  margin-left: 4px;
}
</style>
