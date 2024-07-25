<template>
  <div class="section-content">
    <div class="content">
      <h1>Dashboard</h1>
    </div>
    <div class="card-container">
      <div class="card-item" v-for="(value, key) in dashboardData" :key="key">
        <div class="card-item-detail">
          <div class="card-item-title">
            <p>{{ formatKey(key.toString()) }}</p>
            <div class="card-item-revenue">
              <h5>{{ value }}</h5>
            </div>
          </div>

          <div class="card-item-icon">
            <img :src="cardIcons[key]" alt="icon" />
          </div>
        </div>
      </div>
    </div>

    <div class="group-chart">
      <div class="chart-wrapper chart-item">
        <h2 class="chart-title">Top Customers Borrowing Books</h2>
        <BarChart :options="barChartOptions" :chartData="topCustomersChartData">
          <template #default="{ labels, datasets }">
            <div v-for="(data, index) in datasets[0].data" :key="index">
              <p class="chart-item-label">{{ labels[index] }}</p>
              <p>{{ data }}</p>
            </div>
          </template>
        </BarChart>
      </div>
      <div class="chart-wrapper chart-item">
        <h2 class="chart-title">Top Most Borrowed Books</h2>
        <BarChart :options="barChartOptions" :chartData="topBooksChartData">
          <template #default="{ labels, datasets }">
            <div v-for="(data, index) in datasets[0].data" :key="index">
              <p class="chart-item-label">{{ labels[index] }}</p>
              <p>{{ data }}</p>
            </div>
          </template>
        </BarChart>
      </div>
    </div>
    <div class="chart-wrapper">
      <h2 class="chart-title">Books Borrowed Over Time</h2>
      <BarChart :chartData="lineChartData" :options="lineChartOptions">
        <template #default="{ labels, datasets }">
          <div v-for="(data, index) in datasets[0].data" :key="index">
            <p class="chart-item-label">{{ labels[index] }}</p>
            <p>{{ data }}</p>
          </div>
        </template>
      </BarChart>
    </div>
    <div class="group-chart">
      <div
        v-if="pieChartData.datasets[0].data.length > 0"
        class="chart-wrapper chart-item"
      >
        <h2 class="chart-title">Books Borrowed by Category</h2>
        <PieChart :chartData="pieChartData" :options="pieChartOptions" />
      </div>
      <div v-else class="chart-wrapper chart-item" style="align-items: center">
        <h2 class="chart-title">Books Borrowed by Category</h2>
        <p style="text-align: center">Data is empty</p>
      </div>
      <div class="chart-wrapper chart-item">
        <h2 class="chart-title">Top Favorite Books</h2>
        <LineChart
          :chartData="lineChartTopFavoriteBooks"
          :options="lineChartOptions"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue";
import dashboardService from "@/services/dashboardService";
import { BarChart, LineChart, PieChart } from "vue-chart-3";
import { Chart, registerables, type ChartData as ChartJSData } from "chart.js";
import userIcon from "@/assets/images/user.png";
import bookIcon from "@/assets/images/order.png";
import lendTicketIcon from "@/assets/images/pending.png";
import availableBookIcon from "@/assets/images/sales.png";

import ChartDataLabels from "chartjs-plugin-datalabels";
import type { DashboardData } from "@/types/index";

Chart.register(...registerables, ChartDataLabels);

const dashboardData = ref<DashboardData>({
  total_users: 0,
  total_books: 0,
  total_lend_tickets: 0,
  total_books_available: 0,
});

const lineChartData = ref<ChartJSData<"line">>({
  labels: [],
  datasets: [
    {
      label: "Quantity book Borrowed Over Time",
      data: [],
      borderColor: "#0079AF",
      backgroundColor: ["#4BC0C0"],
      fill: false,
      datalabels: {
        anchor: "end",
        align: "top",
        formatter: (value: number) => value,
      },
    },
  ],
});

const pieChartData = ref<ChartJSData<"doughnut">>({
  labels: [],
  datasets: [
    {
      data: [],
      backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0", "#9966FF"],
      datalabels: {
        formatter: (value: number) => value,
      },
    },
  ],
});

const barChartOptions = {
  indexAxis: "y",
  scales: {
    align: "start",
  },
};

const topCustomersChartData = ref<ChartJSData<"bar">>({
  labels: [],
  datasets: [
    {
      label: "Quantity borrowed book",
      data: [],
      backgroundColor: ["#FF6384"],
      datalabels: {
        anchor: "end",
        align: "top",
        formatter: (value: number) => value,
      },
    },
  ],
});

const topBooksChartData = ref<ChartJSData<"bar">>({
  labels: [],
  datasets: [
    {
      label: "Quanity borrowed of each book",
      data: [],
      backgroundColor: ["#77CEFF"],
      datalabels: {
        anchor: "end",
        align: "top",
        formatter: (value: number) => value,
      },
    },
  ],
});

const lineChartTopFavoriteBooks = ref<ChartJSData<"line">>({
  labels: [],
  datasets: [
    {
      label: "Quantity favorite each book",
      data: [],
      borderColor: "#0079AF",
      fill: false,
      datalabels: {
        anchor: "end",
        align: "top",
        formatter: (value: number) => value,
      },
    },
  ],
});

const filter = ref("day");
const cardIcons: Record<string, string> = {
  total_users: userIcon,
  total_books: bookIcon,
  total_lend_tickets: lendTicketIcon,
  total_books_available: availableBookIcon,
};

const formatKey = (key: string): string => {
  return key.replace(/_/g, " ").replace(/\b\w/g, (char) => char.toUpperCase());
};

const fetchData = async () => {
  try {
    const dashboardResponse = await dashboardService.getStatus();
    dashboardData.value = dashboardResponse.data;
    await changeDateFilter();
  } catch (error) {
    console.error("Error loading dashboard data:", error);
  }
};

const changeDateFilter = async (start: string = "", end: string = "") => {
  try {
    const [
      monthlyBorrowedBooksResponse,
      booksByCategoryResponse,
      topCustomersBorrowingBooksResponse,
      topMostBorrowedBooksResponse,
      top30FavoriteBooksResponse,
    ] = await Promise.all([
      dashboardService.getMonthlyBorrowedBooks(),
      dashboardService.getBooksByCategory(filter.value, start, end),
      dashboardService.getTopCustomersBorrowingBooks(filter.value, start, end),
      dashboardService.getTopMostBorrowedBooks(filter.value, start, end),
      dashboardService.getTop30FavoriteBooks(filter.value, start, end),
    ]);

    lineChartData.value.datasets[0].data =
      monthlyBorrowedBooksResponse.data.map((item) => item.borrow_count);
    lineChartData.value.labels = monthlyBorrowedBooksResponse.data.map((item) =>
      truncateLabel(item.month_name, 15)
    );
    const booksByCategoryData = booksByCategoryResponse.data;
    const topBooksByCategoryData = booksByCategoryData
      .filter((item) => item.book_count > 10)
      .map((item) => item.book_count);
    const othersBookCount = booksByCategoryData
      .filter((item) => item.book_count <= 10)
      .reduce((total, item) => total + item.book_count, 0);

    const filteredCategories = booksByCategoryData.filter(
      (item) => item.book_count >10
    );
    const truncatedLabels = filteredCategories.map((item) =>
      truncateLabel(item.name, 15)
    );
    const othersLabel = "Others";

    pieChartData.value.datasets[0].data = [
      ...topBooksByCategoryData,
      othersBookCount,
    ];
    pieChartData.value.labels = [...truncatedLabels, othersLabel];

    topCustomersChartData.value.datasets[0].data =
      topCustomersBorrowingBooksResponse.data.map(
        (item) => item.num_borrowed_books
      );
    topCustomersChartData.value.labels =
      topCustomersBorrowingBooksResponse.data.map((item) =>
        truncateLabel(item.name, 15)
      );

    topBooksChartData.value.datasets[0].data =
      topMostBorrowedBooksResponse.data.map((item) => item.lend_tickets_count);
    topBooksChartData.value.labels = topMostBorrowedBooksResponse.data.map(
      (item) => truncateLabel(item.name, 15)
    );

    lineChartTopFavoriteBooks.value.datasets[0].data =
      top30FavoriteBooksResponse.data.map((item) => item.wishlists_count);
    lineChartTopFavoriteBooks.value.labels =
      top30FavoriteBooksResponse.data.map((item) =>
        truncateLabel(item.name, 15)
      );
  } catch (error) {
    console.error("Error loading chart data:", error);
  }
};

const truncateLabel = (label: string, maxLength: number) => {
  if (label.length > maxLength) {
    return label.slice(0, maxLength - 3) + "...";
  } else {
    return label;
  }
};

onMounted(() => {
  fetchData();
});

const lineChartOptions = ref<ChartJSData<"bar">>({
  labels: [],
  datasets: [
    {
      label: "Top Most Borrowed Books",
      data: [],
      backgroundColor: ["#77CEFF", "#0079AF", "#123E6B", "#97B0C4", "#A5C8ED"],
      datalabels: {
        anchor: "end",
        align: "top",
        formatter: (value: number) => value,
      },
    },
  ],
});
const pieChartOptions = ref({
  plugins: {
    legend: {
      display: true,
      position: "right",
    },
  },
});
</script>

<style scoped>
.section-content {
  padding: 20px;
}
.content {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}
.filter-buttons {
  display: flex;
  margin: 20px 0;
}
.filter-button.clear {
  background-color: #d32c2c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 18px;
  padding: 6px 12px;
  transition: background-color 0.3s ease;
}
.filter-button.clear:hover {
  background-color: #7a0808;
}
.filter-button {
  background-color: #0079af;
  color: white;
  margin-right: 15px;
  margin-left: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 18px;
  padding: 6px 12px;
  transition: background-color 0.3s ease;
}

.filter-button:hover {
  background-color: #005f85;
}

.filter-button.active {
  background-color: #004c6b;
}

.card-container {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}
.card-item {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 20px;
  width: 23%;
  box-sizing: border-box;
}
.card-item-detail {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.card-item-title p {
  margin: 0;
  font-size: 20px;
  color: #333;
}
.card-item-revenue h5 {
  margin: 0;
  font-size: 24px;
  color: #333;
}
.card-item-icon img {
  width: 40px;
  height: 40px;
}
.chart-wrapper {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin: 20px 0;
}
.group-chart {
  display: flex;
  justify-content: space-between;
  gap: 10px;
}
.chart-item {
  width: 100%;
}
.chart-title {
  font-size: 24px;
  font-weight: bold;
  text-align: center;
  margin-bottom: 10px;
}

.chart-item-label {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
