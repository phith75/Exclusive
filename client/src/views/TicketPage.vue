<template>
  <div class="lend-ticket-container">
    <el-breadcrumb separator="/">
      <el-breadcrumb-item :to="{ path: '/' }">Home</el-breadcrumb-item>
      <el-breadcrumb-item>Lend Tickets</el-breadcrumb-item>
    </el-breadcrumb>
    <div class="lend-ticket-content">
      <ProfileNav />
      <div class="lend-ticket-page">
        <div class="filter-ticket">
          <el-form>
            <el-form-item class="flex-range">
              <el-date-picker
                v-model="dateRange"
                type="daterange"
                range-separator="To"
                start-placeholder="Start date"
                end-placeholder="End date"
                :disabled-date="disabledDate"
                format="YYYY-MM-DD"
              />
            </el-form-item>
            <el-form-item>
              <el-select
                class="select-status"
                v-model="selectedStatus"
                placeholder="Select status"
              >
                <el-option
                  v-for="(label, value) in ticketStatusOptions"
                  :key="value"
                  :label="label.label"
                  :value="Number(label.value)"
                />
              </el-select>
            </el-form-item>
            <el-form-item>
              <el-input v-model="searchQuery" placeholder="Search" />
            </el-form-item>
            <el-form-item class="flex-range">
              <el-button
                type="primary"
                @click="submitFilter"
                :loading="isLoading"
              >
                Filter
              </el-button>
              <el-button @click="clearFilter" :loading="isLoading">
                Clear Filter
              </el-button>
            </el-form-item>
          </el-form>
        </div>
        <div class="table-container">
          <el-table
            v-if="paginatedLendTickets.length > 0"
            :data="paginatedLendTickets"
            class="lend-tickets-table"
            height="400"
          >
            <el-table-column
              prop="lend_ticket_code"
              label="Ticket Code"
              min-width="200"
            />
            <el-table-column
              prop="borrowed_date"
              label="Borrowed Date"
              min-width="200"
              align="center"
            />
            <el-table-column
              prop="status"
              label="Status"
              min-width="120"
              align="center"
            >
              <template #default="{ row }">
                <el-tag :type="getTicketStatusTagType(row.status)">
                  {{ getTicketStatusText(row.status) }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column
              label="Actions"
              fixed="right"
              min-width="100"
              align="right"
            >
              <template #default="{ row }">
                <el-button type="primary" @click="showTicketDetails(row)">
                  <el-icon><View /></el-icon>
                </el-button>
              </template>
            </el-table-column>
          </el-table>
          <el-empty v-else description="No lend tickets found." />
        </div>
        <div class="pagination-container">
          <el-pagination
            hide-on-single-page
            v-model:current-page="currentPage"
            v-model:page-size="pageSize"
            :total="totalLendTickets"
            layout="prev, pager, next"
            @current-change="handleCurrentChange"
          />
        </div>
      </div>
    </div>
  </div>
  <el-dialog
    title="Ticket Details"
    :model-value="drawerVisible"
    :width="resizeByWidth"
    append-to-body
    @close="drawerVisible = false"
    class="custom-drawer"
  >
    <div v-if="selectedTicket" class="ticket-info">
      <h2>  Code: {{ selectedTicket.lend_ticket_code }}</h2>
      <p><strong>Name:</strong> {{ selectedTicket.name }}</p>
      <p><strong>Phone:</strong> {{ selectedTicket.phone }}</p>
      <p><strong>Email:</strong> {{ selectedTicket.email }}</p>
      <p><strong>Address:</strong> {{ selectedTicket.address }}</p>
      <p class="note"><strong>Note:</strong> {{ selectedTicket.note }}</p>
    </div>
    <div class="table-container">
      <el-table
        v-if="selectedTicket && selectedTicket.ticket_detail.length > 0"
        :data="selectedTicket.ticket_detail"
        class="ticket-detail-table"
      >
        <el-table-column prop="book_name" label="Book Name" min-width="200">
          <template #default="{ row }">
            <span class="book-name">{{ row.book_name }}</span>
          </template>
        </el-table-column>
        <el-table-column
          prop="quantity"
          label="Quantity"
          align="center"
          min-width="100"
        />
        <el-table-column
          prop="expected_refund_time"
          label="Expected Refund Time"
          min-width="200"
          align="center"
        />
        <el-table-column
          prop="returned_date"
          label="Returned Date"
          align="center"
          min-width="200"
        >
          <template #default="{ row }">
            <span v-if="row.returned_date">{{ row.returned_date }}</span>
            <span v-else>Not returned yet</span>
          </template>
        </el-table-column>
        <el-table-column
          label="Status"
          min-width="150"
          fixed="right"
          align="center"
        >
          <template #default="{ row }">
            <div
              v-if="
                parseInt(row.status) === STATUS_TICKET_DETAIL_OPTIONS.Returned
              "
            >
              <el-tag :type="getDetailStatusTagType(row.status)">
                {{ getDetailStatusText(row.status) }}
              </el-tag>
            </div>
            <div v-else>
              <el-select
                v-model="row.status"
                placeholder="Select status"
                @change="updateDetailStatus(row)"
                :disabled="isDetailStatusDisabled(row.status)"
              >
                <el-option
                  v-for="option in detailStatusOptions"
                  :key="option.value"
                  :label="option.label"
                  :value="option.value"
                  :disabled="
                    isDetailStatusOptionDisabled(row.status, option.value)
                  "
                >
                  <div class="flex items-center">
                    <el-tag
                      :color="getDetailStatusTagColor(option.value)"
                      style="margin-right: 8px"
                      size="small"
                    />
                    <span
                      :style="{
                        color: getDetailStatusTagColor(option.value),
                      }"
                    >
                      {{ option.label }}
                    </span>
                  </div>
                </el-option>
              </el-select>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <el-empty v-else description="No ticket details found." />
    </div>
  </el-dialog>
</template>

<script lang="ts" setup>
import { ref, onMounted, computed } from "vue";
import ticketService from "@/services/ticketService";
import {
  STATUS_TICKET_OPTIONS,
  STATUS_TICKET_DETAIL_OPTIONS,
  convertEnumToOptions,
} from "@/enum/Constants";
import type { TicketData, TicketDetail, UpdateTicketData } from "@/types/index";
import { toast } from "vue3-toastify";
import moment from "moment";
import ProfileNav from "@/components/NavMenuComponent.vue";

const lendTickets = ref<TicketData[]>([]);
const selectedTicket = ref<TicketData | null>(null);
const drawerVisible = ref(false);
const ticketStatusOptions = convertEnumToOptions(STATUS_TICKET_OPTIONS);
const detailStatusOptions = convertEnumToOptions(STATUS_TICKET_DETAIL_OPTIONS);

const currentPage = ref(1);
const pageSize = ref(10);
const totalLendTickets = computed(() => lendTickets.value.length);

const paginatedLendTickets = computed(() => {
  const startIndex = (currentPage.value - 1) * pageSize.value;
  const endIndex = startIndex + pageSize.value;
  return lendTickets.value.slice(startIndex, endIndex);
});

const selectedStatus = ref("");
const searchQuery = ref("");
const dateRange = ref<[string, string]>(["", ""]);
const isLoading = ref(false);

const disabledDate = (time: Date) => {
  return moment(time) > moment();
};

const currentFilter = ref({
  status: "",
  start_date: "",
  end_date: "",
  keyword: "",
});
const resizeByWidth = computed(() => {
  if (window.innerWidth < 768) {
    return "90%";
  }
  return "70%";
});

const submitFilter = async () => {
  const [start_date, end_date] = dateRange.value.map((date) =>
    date ? moment(date).format("YYYY-MM-DD") : ""
  );

  const newFilter = {
    status: selectedStatus.value,
    start_date,
    end_date,
    keyword: searchQuery.value,
  };

  if (
    newFilter.keyword === currentFilter.value.keyword &&
    newFilter.status === currentFilter.value.status &&
    newFilter.start_date === currentFilter.value.start_date &&
    newFilter.end_date === currentFilter.value.end_date
  ) {
    return;
  }

  currentFilter.value = newFilter;
  try {
    isLoading.value = true;
    await fetchLendTickets(
      newFilter.keyword,
      newFilter.status,
      newFilter.start_date,
      newFilter.end_date
    );
  } catch {
  } finally {
    isLoading.value = false;
  }
};

const clearFilter = () => {
  selectedStatus.value = "";
  searchQuery.value = "";
  dateRange.value = ["", ""];
  fetchLendTickets();
};

const fetchLendTickets = async (
  keywords: string = "",
  status: string = "",
  start_date: string = "",
  end_date: string = ""
) => {
  const response = await ticketService.getTickets(
    1,
    keywords,
    status,
    start_date,
    end_date
  );
  if (response.success) {
    lendTickets.value = response.data.map((ticket) => {
      ticket.ticket_detail = ticket.ticket_detail.map((detail) => {
        detail.status = detail.status.toString();
        return detail;
      });
      ticket.status = ticket.status.toString();
      return ticket;
    });
  } else {
    lendTickets.value = [];
  }
};

const showTicketDetails = (ticket: TicketData) => {
  selectedTicket.value = ticket;
  drawerVisible.value = true;
};

const handleCurrentChange = (page: number) => {
  currentPage.value = page;
};

const updateDetailStatus = async (ticketDetail: TicketDetail) => {
  try {
    const data: UpdateTicketData = { status: ticketDetail.status };
    const response = await ticketService.changeStatusDetails(
      ticketDetail.id,
      data
    );
    if (response.success) {
      toast.success("Status changed successfully");
      await fetchLendTickets();
      const updatedTicket = lendTickets.value.find(
        (ticket) => ticket.id === selectedTicket.value?.id
      );
      if (updatedTicket) {
        selectedTicket.value = updatedTicket;
      }
    } else {
      throw new Error(response.message || "Failed to update detail status");
    }
  } catch (error: any) {
    if (error && error.response && error.response.data.errors.error_message) {
      toast.error("Error: " + error.response.data.errors.error_message);
    } else {
      console.error("Error changing status:", error);
    }
  }
};

const getTicketStatusText = (status: string) => {
  const option = ticketStatusOptions.find((option) => option.value === status);
  return option ? option.label : "Unknown";
};

const getDetailStatusText = (status: string) => {
  const option = detailStatusOptions.find((option) => option.value === status);
  return option ? option.label : "Unknown";
};

const getTicketStatusTagType = (status: string) => {
  switch (parseInt(status)) {
    case STATUS_TICKET_OPTIONS.Approved:
      return "success";
    case STATUS_TICKET_OPTIONS.Returned:
      return "info";
    default:
      return "default";
  }
};

const getDetailStatusTagType = (status: string) => {
  switch (parseInt(status)) {
    case STATUS_TICKET_DETAIL_OPTIONS.Borrowed:
      return "warning";
    case STATUS_TICKET_DETAIL_OPTIONS.Returned:
      return "success";
    case STATUS_TICKET_DETAIL_OPTIONS.Overdue:
    case STATUS_TICKET_DETAIL_OPTIONS.Lost:
      return "danger";
    default:
      return "default";
  }
};

const getDetailStatusTagColor = (status: string) => {
  switch (parseInt(status)) {
    case STATUS_TICKET_DETAIL_OPTIONS.Borrowed:
      return "#FF6600";
    case STATUS_TICKET_DETAIL_OPTIONS.Returned:
      return "#1EC79D";
    case STATUS_TICKET_DETAIL_OPTIONS.Overdue:
      return "#E63415";
    case STATUS_TICKET_DETAIL_OPTIONS.Lost:
      return "#6222C9";
    default:
      return "#CCCCCC";
  }
};

const isDetailStatusOptionDisabled = (
  currentStatus: string,
  optionValue: string
) => {
  const statusNumber = parseInt(currentStatus);
  const optionNumber = parseInt(optionValue);

  if (optionNumber === STATUS_TICKET_DETAIL_OPTIONS.Overdue) {
    return true;
  }

  if (
    statusNumber === STATUS_TICKET_DETAIL_OPTIONS.Overdue ||
    statusNumber === STATUS_TICKET_DETAIL_OPTIONS.Lost
  ) {
    return optionNumber !== STATUS_TICKET_DETAIL_OPTIONS.Returned;
  }

  return false;
};

const isDetailStatusDisabled = (status: string) => {
  return parseInt(status) === STATUS_TICKET_DETAIL_OPTIONS.Returned;
};

onMounted(() => {
  fetchLendTickets();
});
</script>

<style lang="scss" scoped>
.note {
  white-space: pre-wrap;
}
.filter-ticket {
  .el-form {
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    .flex-range {
      flex: 1 1 300px;
      min-width: 150px;
    }
  }
}
.filter-ticket {
  .el-form {
    display: flex;
    gap: 5px;
    .select-status {
      :deep(.el-select__wrapper) {
        max-width: 200px;
        min-width: 150px;
      }
    }
    .el-input,
    .select-status {
      width: 100%;
      background-color: #f3f3f3;
    }
  }
}
:deep(.el-dialog) {
  width: 100%;
}
.ticket-info {
  margin-bottom: 20px;
  p {
    margin: 5px 0;
  }
}
.lend-ticket-container {
  width: 100%;
  max-width: 1170px;
  margin: 40px auto 140px auto;
  padding: 0 15px;
  .pagination-container {
    margin: 10px 10px 0px 0px;
    display: flex;
    justify-content: end;
  }
  .lend-ticket-content {
    margin-top: 2rem;
    display: flex;

    .lend-ticket-page {
      width: 100%;
      background: #fff;
      padding: 20px;
      border: 1px solid #ebeef5;
      border-radius: 4px;

      .table-container {
        .el-table {
          width: 100%;

          .el-table__header-wrapper thead th {
            white-space: nowrap;
            background-color: #f5f7fa;
            color: #303133;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #ebeef5;
          }

          .book-name {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 3;
            overflow: hidden;
          }
        }
      }

      .el-empty {
        margin-top: 20px;
        width: 100%;
        height: 356px;
        border-bottom: 1px solid #ebeef5;
        border-top: 1px solid #ebeef5;
      }

      .custom-drawer {
        padding: 40px 0px;
        --el-dialog-padding-primary: 40px;

        .table-container {
          max-height: 400px;
          :deep(.el-table) {
            min-width: 600px;
          }
        }
      }
    }
  }
}

.el-table {
  .el-table__header-wrapper thead th {
    white-space: nowrap;
    background-color: #f5f7fa;
    color: #303133;
    font-weight: 600;
    font-size: 14px;
    padding: 12px 0;
    border-bottom: 1px solid #ebeef5;
  }

  .book-name {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 3;
    overflow: hidden;
  }
}

@media (max-width: 1024px) {
  .lend-ticket-content {
    flex-direction: column;
    justify-content: space-between;

    .lend-ticket-page {
      .table-container {
        .el-table {
          .el-table__header-wrapper thead th {
            white-space: nowrap;
            background-color: #f5f7fa;
            color: #303133;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #ebeef5;
          }
        }
      }
    }
  }
}

@media (max-width: 768px) {
  .select-status {
    :deep(.el-select__wrapper) {
      width: 150px;
    }
  }
  .ticket-detail-table {
    .el-table {
      min-width: 1000px;
    }
  }
  .lend-ticket-content {
    flex-direction: column;
    :deep(.el-table__inner-wrapper) {
      width: 670px;
    }

    .lend-ticket-page {
      width: 100%;
      padding: 10px;

      .custom-drawer {
        .ticket-info {
          p {
            font-size: 14px;
          }
        }

        :deep(.table-container) {
          .el-table {
            min-width: 400px;
          }
        }
      }
    }
  }
}
@media (max-width: 480px) {
  .lend-ticket-container {
    padding: 0 10px;

    .lend-ticket-content {
      :deep(.el-table__inner-wrapper) {
        width: 370px;
      }
      .lend-ticket-page {
        padding: 5px;

        .custom-drawer {
          .ticket-info {
            p {
              font-size: 12px;
            }
          }

          .table-container {
            .el-table {
              min-width: unset !important;
            }
          }
        }
      }
    }
  }
}
:deep(.el-dialog) {
  padding: 20px;
  margin: 0 auto;
  .el-dialog__body {
    padding: 20px;
  }
  @media (max-width: 768px) {
    width: 90%;
    padding: 15px;
  }
  @media (max-width: 480px) {
    width: 100%;
    padding: 10px;
  }
}
</style>
