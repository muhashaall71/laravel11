<template>
  <div class="dashboard p-8 space-y-8 bg-gray-100 min-h-screen">
    <!-- Navbar -->
    <nav class="flex flex-col md:flex-row justify-between items-center bg-white shadow rounded-lg p-4">
      <h1 class="text-xl font-bold text-gray-700">Management Data</h1>
      <div class="filters flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 items-center mt-4 md:mt-0">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search by name or email"
          @input="fetchData"
          class="border border-gray-300 rounded-lg px-4 py-2 w-full md:w-64 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        />
        <select
          v-model="selectedProfession"
          @change="fetchData"
          class="border border-gray-300 rounded-lg px-4 py-2 w-full md:w-48 focus:ring-2 focus:ring-blue-500 focus:outline-none"
        >
          <option value="">All Professions</option>
          <option v-for="profession in professions" :key="profession" :value="profession">
            {{ profession }}
          </option>
        </select>
        <button
          @click="showCreateModal = true"
          class="bg-blue-500 text-white font-medium px-4 py-2 rounded-lg hover:bg-blue-600 transition"
        >
          Create Data
        </button>
      </div>
    </nav>

    <!-- Table -->
    <div class="bg-white shadow rounded-lg overflow-x-auto">
      <table class="table-auto w-full border-collapse">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600">Email</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600">Profession</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600">Updated At</th>
            <th class="px-4 py-2 text-left font-semibold text-gray-600">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="post in data"
            :key="post.id"
            class="border-t hover:bg-gray-50 transition"
          >
            <td class="px-4 py-2 text-gray-700">{{ post.name }}</td>
            <td class="px-4 py-2 text-gray-700">{{ post.email }}</td>
            <td class="px-4 py-2 text-gray-700">{{ post.profession }}</td>
            <td class="px-4 py-2 text-gray-700">{{ new Date(post.updated_at).toLocaleString() }}</td>
            <td class="px-4 py-2 space-x-2">
              <button
                @click="editData(post)"
                class="bg-yellow-400 text-white font-medium px-3 py-1 rounded-lg hover:bg-yellow-500 transition"
              >
                Edit
              </button>
              <button
                @click="deleteData(post.id)"
                class="bg-red-500 text-white font-medium px-3 py-1 rounded-lg hover:bg-red-600 transition"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination flex items-center justify-between bg-white shadow rounded-lg p-4">
      <button
        :disabled="pagination.current_page === 1"
        @click="changePage(pagination.current_page - 1)"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition disabled:bg-gray-300"
      >
        Previous
      </button>
      <span class="text-gray-700">
        Page {{ pagination.current_page }} of {{ pagination.last_page }}
      </span>
      <button
        :disabled="pagination.current_page === pagination.last_page"
        @click="changePage(pagination.current_page + 1)"
        class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition disabled:bg-gray-300"
      >
        Next
      </button>
    </div>

    <!-- Create Data Modal -->
    <div
      v-if="showCreateModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">Create New Data</h2>
        <form @submit.prevent="createData" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-medium mb-1">Name:</label>
            <input
              type="text"
              v-model="newData.name"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Email:</label>
            <input
              type="email"
              v-model="newData.email"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Profession:</label>
            <select
              v-model="newData.profession"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >
              <option value="" disabled>Select Profession</option>
              <option value="Developer">Developer</option>
              <option value="Teacher">Teacher</option>
              <option value="Doctor">Doctor</option>
            </select>
          </div>
          <div class="modal-actions flex justify-end space-x-4">
            <button
              type="submit"
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition"
            >
              Submit
            </button>
            <button
              type="button"
              @click="closeCreateModal"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Data Modal -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <div class="modal-content bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">Edit Data</h2>
        <form @submit.prevent="updateData" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-medium mb-1">Name:</label>
            <input
              type="text"
              v-model="editData.name"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Email:</label>
            <input
              type="email"
              v-model="editData.email"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-1">Profession:</label>
            <select
              v-model="editData.profession"
              required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"
            >
              <option value="" disabled>Select Profession</option>
              <option value="Developer">Developer</option>
              <option value="Teacher">Teacher</option>
              <option value="Doctor">Doctor</option>
            </select>
          </div>
          <div class="modal-actions flex justify-end space-x-4">
            <button
              type="submit"
              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition"
            >
              Update
            </button>
            <button
              type="button"
              @click="closeEditModal"
              class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

  
<script>
import axios from "axios";

export default {
  data() {
    return {
      data: [],
      professions: [],
      searchQuery: "",
      selectedProfession: "",
      pagination: {
        current_page: 1,
        last_page: 1,
      },
      showCreateModal: false,
      showEditModal: false,
      newData: {
        name: "",
        email: "",
        profession: "",
      },
      editData: {
        id: null,
        name: "",
        email: "",
        profession: "",
      },
    };
  },
  methods: {
    async fetchData(page = 1) {
      try {
        const response = await axios.get("http://127.0.0.1:8000/api/data/filter", {
          params: {
            search: this.searchQuery,
            profession: this.selectedProfession,
            per_page: 10,
            page: page,
          },
        });

        if (response.data.status === "success") {
          this.data = response.data.data;
          this.professions = response.data.professions;
          this.pagination = response.data.pagination;
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.pagination.current_page = page;
        this.fetchData(page);
      }
    },
    async createData() {
      try {
        const response = await axios.post("http://127.0.0.1:8000/api/data", this.newData);

        if (response.data.status === "success") {
          alert(response.data.message);
          this.closeCreateModal();
          this.fetchData(); // Refresh data
        }
      } catch (error) {
        console.error("Error creating data:", error);
        alert(error.response?.data?.message || "Failed to create data.");
      }
    },
    closeCreateModal() {
      this.showCreateModal = false;
      this.newData = { name: "", email: "", profession: "" }; // Reset form
    },
    async editData(post) {
        this.editData = { ...post }; // Salin data untuk diedit
        this.showEditModal = true; // Tampilkan modal
    },
    async updateData() {
      try {
        const response = await axios.put(`http://127.0.0.1:8000/api/data/${this.editData.id}`, this.editData);

        if (response.data.status === "success") {
          alert(response.data.message);
          this.closeEditModal();
          this.fetchData(); // Refresh data
        }
      } catch (error) {
        console.error("Error updating data:", error);
        alert(error.response?.data?.message || "Failed to update data.");
      }
    },
    closeEditModal() {
      this.showEditModal = false;
      this.editData = { id: null, name: "", email: "", profession: "" }; // Reset form
    },
    async deleteData(id) {
      if (confirm("Are you sure you want to delete this data?")) {
        try {
          const response = await axios.delete(`http://127.0.0.1:8000/api/data/${id}`);
  
          if (response.data.status === "success") {
            alert(response.data.message);
            this.fetchData(); // Refresh data after deletion
          }
        } catch (error) {
          console.error("Error deleting data:", error);
          alert(error.response?.data?.message || "Failed to delete data.");
        }
      }
    },
  },
  mounted() {
    this.fetchData();
  },
};
</script>


<style>
.dashboard {
  padding: 20px;
}

nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.filters input,
.filters select {
  margin: 0 10px;
  padding: 5px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

table th,
table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  width: 400px;
}

.modal-actions {
  display: flex;
  justify-content: space-between;
}
</style>
