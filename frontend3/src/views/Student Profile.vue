<template>
  <main>
      <div class="top-container">
        <h1 class="bg-title">Book Inventory</h1>
        <div class="studentProfile">
              <span class="material-icons">inventory</span>
              <span class="text">Book Inventory</span>
        </div>
        <div class="buttons">
            <v-btn class="mb-2" color="primary" dark @click="manageCategories">Manage Categories</v-btn>
        </div>
        <span></span>
        <div class="buttons" style="margin-right: 10px;">
            <v-btn class="mb-2" color="#B71C1C" dark style="color: white;" @click="manageArchives">Archives</v-btn>
        </div>

      </div>  
      <hr>

      <div class="main-content">

          <div class="student-table">
              <StudentTable/>
          </div>
      </div>

      
</main>

</template>

<script>
// import AddStudent from '../components/AddStudent.vue';
import StudentTable from '../components/StudentTable.vue';

export default {
components: {
  // AddStudent,
  StudentTable
},
methods:{
    manageCategories() {
      this.$router.push('/categories');
    },
    manageArchives() {
      this.$router.push('/bookarchives');
    },
},
};
</script>

<style lang="scss" scoped>
.buttons {
  display: flex;
  align-items: center;
  margin-right: 20px;
}
.top-container{
            display: flex;
            margin: 0.5rem;
  
            .bg-title{
                z-index: -1;
                position: absolute;
                opacity: 10%;
                margin-top: -40px;
                font-size: 100px;
                font-family: Impact, fantasy;
            }
            .studentProfile{
                flex: 1;
        
                .material-icons{
                    position: relative;
                    color: var(--dark);
                    font-size: 2.5rem;
                }
                .text{
                    position: relative;
                    color: var(--dark);
                    font-size: 2.5rem;
                    font-weight: 900;
                    bottom: 0.5rem;
                    text-shadow: 0 1px 1px;
                    margin-left: 1rem;
                }
            }
        
        }

.main-content {
  display: flex;
  flex-direction: column;
  margin-right: 10px;

  .student-table{
      border-radius: 10px;
      background-color: #f0f0f0;
      padding: 0.5rem;
      height: 100%;
  }
}

</style>