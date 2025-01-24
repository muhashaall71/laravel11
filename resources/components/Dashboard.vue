<template>
    <div>
      <h1>Dashboard</h1>
  
      <!-- Filter Tanggal untuk Pie Chart -->
      <div>
        <label for="pieStartDate">Start Date (Pie Chart):</label>
        <input type="date" v-model="filters.pieStartDate" id="pieStartDate" />
  
        <label for="pieEndDate">End Date (Pie Chart):</label>
        <input type="date" v-model="filters.pieEndDate" id="pieEndDate" />
  
        <button @click="fetchPieChartData">Apply Filter for Pie Chart</button>
      </div>
  
      <!-- Charts -->
      <div id="pie-chart-container"></div>

       <!-- Filter Tanggal untuk Column Chart -->
       <div>
        <label for="aggStartDate">Start Date (Column Chart):</label>
        <input type="date" v-model="filters.aggStartDate" id="aggStartDate" />
  
        <label for="aggEndDate">End Date (Column Chart):</label>
        <input type="date" v-model="filters.aggEndDate" id="aggEndDate" />
  
        <button @click="fetchAggregationData">Apply Filter for Column Chart</button>
      </div>
      
      <div id="column-chart-container"></div>
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
  h2 {
    text-align: center;
    margin-bottom: 20px;
  }
  label {
    font-weight: bold;
  }
  input[type="date"] {
    margin-left: 5px;
    margin-right: 10px;
  }
  </style>
  