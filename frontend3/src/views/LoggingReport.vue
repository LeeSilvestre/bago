<template>
    <main>
      <div class="top-container">
        <div class="LoggingReport">
          <span class="material-icons">report</span>
          <span class="text">Logging Report</span>
        </div>
      </div>  
      <hr>
  
      <div class="main-content">
        <div class="loggingreport-table">
          <LoggingReportTable/>
        </div>
      </div>
    </main>
  </template>
  
  <script>
  import LoggingReportTable from '../components/LoggingReportTable.vue';
  
  export default {
    components: {
      LoggingReportTable
    }
  };
  </script>
  
  <style lang="scss" scoped>
  .top-container {
    display: flex;
    margin: 0.5rem;
  
    .LoggingReport {
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
  
    .loggingreport-table {
      border-radius: 10px;
      background-color: #f0f0f0;
      padding: 0.5rem;
      height: 100%;
    }
  }
  </style>