<template>
    <main>
      <div class="top-container">
        <div class="Records">
          <span class="material-icons">pending</span>
          <span class="text">Pending Receipt</span>
        </div>
      </div>  
      <hr>
  
      <div class="main-content">
        <div class="record-table">
          <RecordTable/>
        </div>
      </div>
    </main>
  </template>
  
  <script>
  import RecordTable from '../components/PendingReceiptTable.vue';
  
  export default {
    components: {
      RecordTable
    }
  };
  </script>
  
  <style lang="scss" scoped>
  .top-container {
    display: flex;
    margin: 0.5rem;
  
    .Records {
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
  
    .record-table {
      border-radius: 10px;
      background-color: #f0f0f0;
      padding: 0.5rem;
      height: 100%;
    }
  }
  </style>