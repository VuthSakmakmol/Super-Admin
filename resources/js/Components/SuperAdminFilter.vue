<template>
    <div>
      <!-- Filters Section -->
      <div class="mb-3">
        <input type="text" v-model="filters.name" placeholder="Search by Name" class="form-control mb-2" />
        <input type="text" v-model="filters.id" placeholder="Search by ID" class="form-control mb-2" />
        <select v-model="filters.role" class="form-select mb-2">
          <option value="">Filter by Role</option>
          <option v-for="role in roles" :value="role" :key="role">{{ role }}</option>
        </select>
        <select v-model="filters.month" class="form-select mb-2">
          <option value="">Filter by Month</option>
          <option v-for="month in months" :value="month.value" :key="month.value">
            {{ month.name }}
          </option>
        </select>
        <select v-model="filters.year" class="form-select mb-2">
          <option value="">Filter by Year</option>
          <option v-for="year in years" :value="year" :key="year">{{ year }}</option>
        </select>
        <button @click="resetFilters" class="btn btn-secondary">See All</button>
      </div>
  
      <!-- User Table -->
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in filteredUsers" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>{{ user.created_at }}</td>
            <td>
              <button class="btn btn-warning btn-sm">Edit</button>
              <button class="btn btn-danger btn-sm">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        users: [], // All users
        roles: ['Admin', 'User', 'Super Admin'], // Replace with your actual roles
        months: [
          { value: 1, name: 'January' },
          { value: 2, name: 'February' },
          { value: 3, name: 'March' },
          { value: 4, name: 'April' },
          { value: 5, name: 'May' },
          { value: 6, name: 'June' },
          { value: 7, name: 'July' },
          { value: 8, name: 'August' },
          { value: 9, name: 'September' },
          { value: 10, name: 'October' },
          { value: 11, name: 'November' },
          { value: 12, name: 'December' },
        ],
        years: Array.from({ length: 30 }, (_, i) => new Date().getFullYear() - i),
        filters: {
          name: '',
          id: '',
          role: '',
          month: '',
          year: '',
        },
      };
    },
    computed: {
      filteredUsers() {
        // Apply filtering based on user input
        return this.users.filter((user) => {
          const matchesName = !this.filters.name || user.name.includes(this.filters.name);
          const matchesId = !this.filters.id || user.id == this.filters.id;
          const matchesRole = !this.filters.role || user.role == this.filters.role;
          const matchesMonth =
            !this.filters.month || new Date(user.created_at).getMonth() + 1 == this.filters.month;
          const matchesYear =
            !this.filters.year || new Date(user.created_at).getFullYear() == this.filters.year;
          return matchesName && matchesId && matchesRole && matchesMonth && matchesYear;
        });
      },
    },
    methods: {
      resetFilters() {
        this.filters = {
          name: '',
          id: '',
          role: '',
          month: '',
          year: '',
        };
      },
      fetchUsers() {
        fetch('/super-admin/filter-users')
          .then((response) => response.json())
          .then((data) => {
            this.users = data;
          });
      },
    },
    mounted() {
      this.fetchUsers(); // Load users on component mount
    },
  };
  </script>
  
  <style scoped>
  .table {
    margin-top: 20px;
  }
  </style>
  