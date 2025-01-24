<template>
    <div class="dashboard">
      <nav>
        <h1>Management Data</h1>
        <div class="filters">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search by name or email"
            @input="fetchData"
          />
          <select v-model="selectedProfession" @change="fetchData">
            <option value="">All Professions</option>
            <option v-for="profession in professions" :key="profession" :value="profession">
              {{ profession }}
            </option>
          </select>
          <button @click="showCreateModal = true">Create Data</button>
        </div>
      </nav>
  
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Profession</th>
            <th>Updated At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in data" :key="post.id">
            <td>{{ post.name }}</td>
            <td>{{ post.email }}</td>
            <td>{{ post.profession }}</td>
            <td>{{ new Date(post.updated_at).toLocaleString() }}</td>
            <td>
                <button @click="editData(post)">Edit</button>
                <button @click="deleteData(post.id)" style="margin-left: 2cm;">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
  
      <div class="pagination">
        <button :disabled="pagination.current_page === 1" @click="changePage(pagination.current_page - 1)">
          Previous
        </button>
        <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button
          :disabled="pagination.current_page === pagination.last_page"
          @click="changePage(pagination.current_page + 1)"
        >
          Next
        </button>
      </div>
  
      <!-- Create Data Modal -->
      <div v-if="showCreateModal" class="modal">
        <div class="modal-content">
          <h2>Create New Data</h2>
          <form @submit.prevent="createData">
            <div>
              <label>Name:</label>
              <input type="text" v-model="newData.name" required />
            </div>
            <div>
              <label>Email:</label>
              <input type="email" v-model="newData.email" required />
            </div>
            <div>
              <label>Profession:</label>
              <select v-model="newData.profession" required>
                <option value="" disabled>Select Profession</option>
                <option value="Developer">Developer</option>
                <option value="Teacher">Teacher</option>
                <option value="Doctor">Doctor</option>
              </select>
            </div>
            <div class="modal-actions">
              <button type="submit">Submit</button>
              <button type="button" @click="closeCreateModal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
  
      <!-- Edit Data Modal -->
        <div v-if="showEditModal" class="modal">
        <div class="modal-content">
            <h2>Edit Data</h2>
            <form @submit.prevent="updateData">
            <div>
                <label>Name:</label>
                <input type="text" v-model="editData.name" required />
            </div>
            <div>
                <label>Email:</label>
                <input type="email" v-model="editData.email" required />
            </div>
            <div>
                <label>Profession:</label>
                <select v-model="editData.profession" required>
                <option value="" disabled>Select Profession</option>
                <option value="Developer">Developer</option>
                <option value="Teacher">Teacher</option>
                <option value="Doctor">Doctor</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="submit">Update</button>
                <button type="button" @click="closeEditModal">Cancel</button>
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
