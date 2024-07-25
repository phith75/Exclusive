<template>
  <section id="section-content">
    <div class="content">
      <div class="content-header">
        <h1>Lend Ticket: {{ lendTicketCode }}</h1>
      </div>
    </div>
    <div class="action-filter">
      <router-link :to="{ name: 'ticket.list' }">Back</router-link>
    </div>
    <div class="table-component">
      <TableComponent
        :headers="detailHeaders"
        :items="ticketDetails"
        :statusOptions="statusOption"
        @changeStatus="changeStatus"
        :showCheckbox="false"
        :showActions="false"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import ticketService from "@/services/ticketService";
import TableComponent from "@/components/TableComponent.vue";
import { toast } from "vue3-toastify";
import {
  STATUS_TICKET_DETAIL_OPTIONS,
  convertEnumToOptions,
} from "@/enum/Constants";
import { useRoute } from "vue-router";
import type { TicketDetail } from "@/types/index";
const route = useRoute();
const ticketDetailId = route.params.id as string;
const ticketDetails = ref<TicketDetail[]>([]);
const error = ref<string | null>(null);
const isLoading = ref(false);
const lendTicketCode = ref("");

// Table headers
const detailHeaders = [
  { key: "book_name", label: "Book Name" },
  { key: "quantity", label: "Quantity" },
  { key: "expected_refund_time", label: "Expected Refund Time" },
  { key: "returned_date", label: "Returned Date" },
  { key: "status", label: "Status" },
];

const statusOption = convertEnumToOptions(STATUS_TICKET_DETAIL_OPTIONS);

const fetchTicketDetails = async () => {
  isLoading.value = true;
  error.value = null;
  try {
    const response = await ticketService.getTicketById(ticketDetailId);
    if (response.success == 1) {
      ticketDetails.value = response.data.ticket_detail;
      lendTicketCode.value = response.data.lend_ticket_code;
    } else {
      throw new Error("response.data is not an array");
    }
  } catch (err: any) {
    error.value = err.data.errors;
    toast.error(`Error: ${err.data.errors}`);
  } finally {
    isLoading.value = false;
  }
};

const changeStatus = async (id: number, newStatus: string) => {
  try {
    const response = await ticketService.changeStatusDetails(id, {
      status: newStatus,
    });
    if (response.success == 1) {
      toast.success("Detail status updated successfully");
    } else {
      throw new Error(response.message || "Failed to update detail status");
    }
  } catch (error: any) {
    if (error && error.status === 422 && error.data.errors.error_message) {
      toast.error("Error: " + error.data.errors.error_message);
    }
  }
  fetchTicketDetails();
};

onMounted(() => {
  fetchTicketDetails();
});
</script>

<style scoped></style>
