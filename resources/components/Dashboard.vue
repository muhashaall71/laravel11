<template>
  <div class="p-8 space-y-8">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-center">Dashboard</h1>

    <!-- Filter Tanggal untuk Pie Chart -->
    <div class="space-y-4">
      <h2 class="text-lg font-semibold">Filter Tanggal untuk Pie Chart</h2>
      <div class="flex items-center space-x-4">
        <div>
          <label for="pieStartDate" class="block text-sm font-medium text-gray-700">Start Date</label>
          <input
            type="date"
            v-model="filters.pieStartDate"
            id="pieStartDate"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="pieEndDate" class="block text-sm font-medium text-gray-700">End Date</label>
          <input
            type="date"
            v-model="filters.pieEndDate"
            id="pieEndDate"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <button @click="fetchPieChartData" class="bg-blue-500 text-white mt-5 font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition">Apply Filter</button>
      </div>
    </div>

    <!-- Pie Chart Container -->
    <div id="pie-chart-container" class="border rounded-md shadow p-4"></div>

    <!-- Filter Tanggal untuk Column Chart -->
    <div class="space-y-4">
      <h2 class="text-lg font-semibold">Filter Tanggal untuk Column Chart</h2>
      <div class="flex items-center space-x-4">
        <div>
          <label for="aggStartDate" class="block text-sm font-medium text-gray-700">Start Date</label>
          <input
            type="date"
            v-model="filters.aggStartDate"
            id="aggStartDate"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <div>
          <label for="aggEndDate" class="block text-sm font-medium text-gray-700">End Date</label>
          <input
            type="date"
            v-model="filters.aggEndDate"
            id="aggEndDate"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <button
          @click="fetchAggregationData"
          class="bg-blue-500 text-white mt-5 font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition"
        >
          Apply Filter
        </button>
      </div>
    </div>

    <!-- Column Chart Container -->
    <div id="column-chart-container" class="border rounded-md shadow p-4"></div>
  </div>
</template>

<script>
import Highcharts from "highcharts";

export default {
  data() {
    return {
      filters: {
        // Filter tanggal untuk Pie Chart
        pieStartDate: "",
        pieEndDate: "",
        // Filter tanggal untuk Column Chart
        aggStartDate: "",
        aggEndDate: "",
      },
      professionData: [], // Data untuk Pie Chart
      dateAggregationData: [], // Data untuk Column Chart
    };
  },
  methods: {
    // Fungsi untuk mengambil data dari API untuk Pie Chart
    async fetchPieChartData() {
      try {
        const response = await fetch(
          `http://127.0.0.1:8000/api/professions?start_date=${this.filters.pieStartDate || ""}&end_date=${this.filters.pieEndDate || ""}`
        );
        const result = await response.json();

        if (result.status === "success") {
          this.professionData = result.data;
          this.renderPieChart(); // Render Pie Chart
        } else {
          alert(result.message || "Failed to fetch profession data.");
        }
      } catch (error) {
        console.error("Error fetching data:", error);
        alert("An error occurred while fetching profession data.");
      }
    },

    // Fungsi untuk mengambil data dari API untuk Column Chart (Aggregation)
    async fetchAggregationData() {
      try {
        const response = await fetch(
          `http://127.0.0.1:8000/api/aggregation?start_date=${this.filters.aggStartDate || ""}&end_date=${this.filters.aggEndDate || ""}`
        );
        const result = await response.json();

        if (result.status === "success") {
          this.dateAggregationData = result.data;
          this.renderColumnChart(); // Render Column Chart
        } else {
          alert(result.message || "Failed to fetch aggregation data.");
        }
      } catch (error) {
        console.error("Error fetching data:", error);
        alert("An error occurred while fetching aggregation data.");
      }
    },

    // Fungsi untuk merender Pie Chart
    renderPieChart() {
      const chartData = this.professionData.map((item) => ({
        name: item.profession,
        y: item.count,
      }));

      Highcharts.chart("pie-chart-container", {
        chart: {
          type: "pie",
        },
        title: {
          text: "Distribution of Professions",
        },
        series: [
          {
            name: "Count",
            colorByPoint: true,
            data: chartData,
          },
        ],
      });
    },

    // Fungsi untuk merender Column Chart
    renderColumnChart() {
      const categories = this.dateAggregationData.map((item) => item.date);
      const data = this.dateAggregationData.map((item) => item.count);

      Highcharts.chart("column-chart-container", {
        chart: {
          type: "column",
        },
        title: {
          text: "Data Aggregation by Date",
        },
        xAxis: {
          categories: categories,
          title: {
            text: "Date",
          },
        },
        yAxis: {
          min: 0,
          title: {
            text: "Count",
          },
        },
        series: [
          {
            name: "Count",
            data: data,
          },
        ],
      });
    },
  },
  mounted() {
    // Default: Ambil data untuk kedua chart dengan filter kosong (akan menggunakan default API kemarin-hari ini)
    this.fetchPieChartData();
    this.fetchAggregationData();
  },
};
</script>

<style scoped>
/* Tidak perlu tambahan CSS karena Tailwind sudah mencakup styling */
</style>
