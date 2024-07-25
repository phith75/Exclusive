<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>List Lend Tickets</h1>
      </div>
    </div>

    <div class="action-filter">
      <div class="filters">
        <div class="filter-left">
          <div class="group-select">
            <label for="">Select Status</label>
            <select v-model="selectedStatus">
              <option value="">All Statuses</option>
              <option
                v-for="status in statusOption"
                :key="status.value"
                :value="status.value"
              >
                {{ status.label }}
              </option>
            </select>
          </div>
          <div class="group-select">
            <label for="">Select Details Status</label>
            <select v-model="selectedTicketDetailStatus">
              <option value="">All Statuses</option>
              <option
                v-for="status in ticketDetailStatusOption"
                :key="status.value"
                :value="status.value"
              >
                {{ status.label }}
              </option>
            </select>
          </div>

          <div class="date-section">
            <div class="date-picker">
              <div class="group-select">
                <label for="">Select Start Date</label>
                <div class="date-item">
                  <input
                    class="date-filter date-filter-start"
                    type="date"
                    :class="{ error: dateStartError }"
                    v-model="startDate"
                    :max="maxStartDate"
                    :min="minDateStart"
                  />
                </div>
              </div>
              <div class="group-select">
                <label for="">Select End Date</label>
                <div class="date-item">
                  <input
                    class="date-filter date-filter-end"
                    type="date"
                    :class="{ error: dateEndError }"
                    v-model="endDate"
                    :max="maxDate"
                    :min="minDateEnd"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="filter-right">
          <SearchComponent v-model="searchQuery" content="tickets" />
        </div>
        <div class="button-filter">
          <button class="btn-filter" @click="filterTickets">Filter</button>
          <button class="btn-clear" @click="clearFilters">Clear</button>
        </div>
      </div>
    </div>
    <div class="table-component">
      <TableComponent
        :headers="ticketHeaders"
        :items="tickets"
        :statusOptions="statusOption"
        @changeStatus="changeStatus"
        :showCheckbox="false"
        :editRouteName="'ticket.detail'"
        :showActions="true"
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
import { ref, computed, onMounted } from "vue";
import ticketService from "@/services/ticketService";
import TableComponent from "@/components/TableComponent.vue";
import PaginationComponent from "@/components/PaginationComponent.vue";
import SearchComponent from "@/components/SearchComponent.vue";
import { toast } from "vue3-toastify";
import {
  STATUS_TICKET_OPTIONS,
  STATUS_TICKET_DETAIL_OPTIONS,
  convertEnumToOptions,
} from "@/enum/Constants";
import type { TicketData } from "@/types/index";

// State variables
const searchQuery = ref("");
const tickets = ref<TicketData[]>([]);
const statusOption = convertEnumToOptions(STATUS_TICKET_OPTIONS);
const selectedStatus = ref("");
const ticketDetailStatusOption = convertEnumToOptions(
  STATUS_TICKET_DETAIL_OPTIONS
);
const selectedTicketDetailStatus = ref("");
const startDate = ref("");
const endDate = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const dateStartError = ref<string | null>(null);
const dateEndError = ref<string | null>(null);
const isLoading = ref(false);

// Table headers
const ticketHeaders = [
  { key: "lend_ticket_code", label: "Lend Ticket Code" },
  { key: "name", label: "User Name" },
  { key: "phone", label: "Phone" },
  { key: "email", label: "Email" },
  { key: "address", label: "Address" },
  { key: "status", label: "Status ticket" },
  { key: "borrowed_date", label: "Borrowed Date" },
  { key: "note", label: "Note" },
];

const today = new Date().toISOString().split("T")[0];
const maxDate = computed(() => today);
const minDateStart = computed(() => "");
const minDateEnd = computed(() => (startDate.value ? startDate.value : ""));
const maxStartDate = computed(() => (endDate.value ? endDate.value : today));

// Fetch tickets function
const fetchTickets = async (
  page = 1,
  keyword = "",
  status = "",
  ticket_detail_status = "",
  start_date = "",
  end_date = ""
) => {
  isLoading.value = true;
  try {
    const response = await ticketService.getTickets(
      page,
      keyword,
      status,
      ticket_detail_status,
      start_date,
      end_date
    );

    if (response.success === 1) {
      tickets.value = response.data;
      currentPage.value = response.pagination?.current_page ?? 1;
      totalPages.value = response.pagination?.last_page ?? 1;
    } else {
      throw new Error(response.message || "Failed to fetch tickets");
    }
  } catch (err: any) {
    toast.error(`Error: ${err.data.errors}`);
  } finally {
    isLoading.value = false;
  }
};

// Change status function
const changeStatus = async (id: number, newStatus: string) => {
  try {
    const response = await ticketService.changeStatusTicket(id.toString(), {
      status: newStatus,
    });
    if (response.success === 1) {
      toast.success("Ticket status updated successfully");
    } else {
      throw new Error(response.message || "Failed to update ticket status");
    }
  } catch (error: any) {
    if (error && error.status === 422 && error.data.errors.error_message) {
      toast.error("Error: " + error.data.errors.error_message);
    }
  }
  fetchTickets(
    currentPage.value,
    searchQuery.value,
    selectedStatus.value,
    selectedTicketDetailStatus.value,
    startDate.value,
    endDate.value
  );
};

onMounted(() => {
  fetchTickets();
});

// Pagination and filter functions
const goToPage = (page: number) => {
  fetchTickets(
    page,
    searchQuery.value,
    selectedStatus.value,
    selectedTicketDetailStatus.value,
    startDate.value,
    endDate.value
  );
};

const prevPage = () => {
  if (currentPage.value > 1) {
    fetchTickets(
      currentPage.value - 1,
      searchQuery.value,
      selectedStatus.value,
      selectedTicketDetailStatus.value,
      startDate.value,
      endDate.value
    );
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    fetchTickets(
      currentPage.value + 1,
      searchQuery.value,
      selectedStatus.value,
      selectedTicketDetailStatus.value,
      startDate.value,
      endDate.value
    );
  }
};

// Filter tickets function
const filterTickets = () => {
  fetchTickets(
    1,
    searchQuery.value,
    selectedStatus.value,
    selectedTicketDetailStatus.value,
    startDate.value,
    endDate.value
  );
};

// Clear filters function
const clearFilters = () => {
  searchQuery.value = "";
  selectedStatus.value = "";
  selectedTicketDetailStatus.value = "";
  startDate.value = "";
  endDate.value = "";

  fetchTickets(1, "", "", "", "", "");
};
</script>

<style scoped>
.action-filter {
  display: flex;
  justify-content: end;
  margin-bottom: 10px;
}
.date-section {
  display: flex;
  flex-direction: column;
}
.date-picker {
  display: flex;
  align-items: center;
  gap: 10px;
}
.date-filter {
  cursor: pointer;
  width: 150px;
  height: 38px;
  background-color: #ffffff;
  padding: 10px 15px;
  border-radius: 8px;
  border: 1px solid #d5d5d5;
}
</style>
