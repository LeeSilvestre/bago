<template>
  <main>
    <div class="top-container">
      <div class="RentalRecords">
        <span class="material-icons">book</span>
        <span class="text">Rental Records</span>
      </div>
    </div>
    <hr>
    <div class="main-content">
      <div class="rentalrecords-table">
        <RentalRecordsTable />
      </div>
    </div>
  </main>
</template>

<script>
import RentalRecordsTable from '../components/RentalRecordsTable.vue';

export default {
  components: {
    RentalRecordsTable
  }
};
</script>

<style lang="scss" scoped>
.top-container {
  display: flex;
  margin: 0.5rem;

  .RentalRecords {
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

  .rentalrecords-table {
    border-radius: 10px;
    background-color: #f0f0f0;
    padding: 0.5rem;
    height: 100%;
  }
}
</style>
