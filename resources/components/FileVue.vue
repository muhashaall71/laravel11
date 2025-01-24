<template>
    <div id="app">
      <nav>
        <ul>
          <li><a href="#" @click.prevent="currentView = 'Dashboard'">Dashboard</a></li>
          <li><a href="#" @click.prevent="currentView = 'Management'">Management</a></li>
        </ul>
      </nav>
      <main>
        <component :is="currentView"></component>
      </main>
    </div>
  </template>
  
  <script>
  import Dashboard from './Dashboard.vue';
  import Management from './Management.vue';
  
  export default {
    data() {
      return {
        currentView: 'Dashboard', // Default view
      };
    },
    components: {
      Dashboard,
      Management,
    },
  };
  </script>
  
  <style scoped>
  /* Tambahkan gaya untuk menu */
  nav ul {
    list-style: none;
    padding: 0;
    display: flex;
    gap: 20px;
  }
  
  nav ul li {
    display: inline;
  }
  
  nav ul li a {
    text-decoration: none;
    color: blue;
    font-weight: bold;
  }
  
  main {
    margin-top: 20px;
  }
  </style>
  