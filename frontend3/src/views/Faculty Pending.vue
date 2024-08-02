<template>
    <main>
      <div class="top-container">
        <div class="pendingBooks">
          <span class="material-icons">history_edu</span>
          <span class="text">Faculty Pending Books</span>
        </div>
      </div>  
      <hr>
  
      <div class="main-content">
        <div class="pending-table">
          <PendingTable/>
        </div>
      </div>
    </main>
  </template>
  
  <script>
  import PendingTable from '../components/FacultyPendingTable.vue';
  
  export default {
    components: {
      PendingTable
    }
  };
  </script>
  
  <style lang="scss" scoped>
  .top-container {
    display: flex;
    margin: 0.5rem;
  
    .pendingBooks {
      flex: 1;
  
      .material-icons {
        color: var(--dark);
        font-size: 2.5rem;
      }
  
      .text {
        position: relative;
        color: var(--dark);
        font-size: 1.5rem;
        font-weight: 900;
        bottom: 0.5rem;
      }
    }
  } 
  
  .main-content {
    display: flex;
    flex-direction: column;
  
    .pending-table {
      border-radius: 10px;
      background-color: #f0f0f0;
      padding: 0.5rem;
      height: 100%;
    }
  }
  </style>
  