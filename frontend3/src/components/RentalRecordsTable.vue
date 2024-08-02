<template>
    <div>
      <v-data-table
        :search="search"
        :headers="headers"
        :items="filteredData"
        :sort-by="[{ key: 'borrow_id', order: 'desc' }]"
      >
        
        <template v-slot:top>
          <v-toolbar flat>
            <v-toolbar-title class="text-h6 font-weight-black" style="color: #2F3F64"></v-toolbar-title>
            <v-text-field
              v-model="search"
              class="w-auto mr-4"
              density="compact"
              label="Search"
              prepend-inner-icon="mdi-magnify"
              variant="solo-filled"
              flat
              hide-details
              single-line
            ></v-text-field>
  
            <!-- Filter by Grade -->
            <v-select
              v-model="selectedGrade"
              :items="grades"
              item-text="grade"
              item-value="grade"
              label="Filter by Grade"
              dense
              outlined
              clearable
            ></v-select>
  
            <!-- Filter by Section -->
            <v-select
              v-model="selectedSection"
              :items="sections"
              item-text="section"
              item-value="section"
              label="Filter by Section"
              dense
              outlined
              clearable
            ></v-select>
  
            <v-spacer></v-spacer>
  
            <!-- Add Record Button -->
            <v-dialog v-model="dialog" max-width="1000px">
              <template v-slot:activator="{ props }">
                <v-btn class="mb-2 rounded-l" color="primary" dark v-bind="props" prepend-icon="mdi-plus">Add Record</v-btn>
              </template>
  
              <v-card>
                <v-card-title class="text-h5">{{ formTitle }}</v-card-title>
                <v-card-text>
                  <v-form @submit.prevent="save">
                    <v-text-field v-model="editedItem.book_id" label="Book ID" :error-messages="getErrorMessage('book_id')"></v-text-field>
                    <v-text-field v-model="editedItem.student_id" label="Student ID" :error-messages="getErrorMessage('student_id')"></v-text-field>
                  </v-form>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" @click="close">Cancel</v-btn>
                  <v-btn color="blue darken-1" @click="showConfirmation">Add Record</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
  
            <!-- Confirmation Dialog -->
            <v-dialog v-model="dialogConfirmation" max-width="500px">
              <v-card>
                <v-card-title class="text-h5">Confirmation</v-card-title>
                <v-card-text>
                  Are you sure you want to add this record?
                  <div>
                    <strong>Book ID:</strong> {{ editedItem.book_id }}
                  </div>
                  <div>
                    <strong>Student ID:</strong> {{ editedItem.student_id }}
                  </div>
                </v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" @click="save">Yes</v-btn>
                  <v-btn color="blue darken-1" @click="closeConfirmation">No</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
  
        <!-- Table Rows -->
        <template v-slot:item="{ item }">
          <tr>
            <td>{{ item.student_grade }}</td>
            <td>{{ item.student_section }}</td>
            <td>{{ item.rental_record }}</td>
            <td>{{ formatDate2(item.borrowed_date) }}</td>
            <td>{{ formatDate(item.return_duedate) }}</td>
            <td :class="{ 'green-text': item.borrow_status === 0 }">{{ availableStatus[item.borrow_status] }}</td>
            <td>
              <button v-if="item.borrow_status !== 2" type="button" class="btn btn-success white-text" @click="returnConfirmation(item)" :disabled="loading">
                <span v-if="loading">Returning...</span>
                <span v-else>Return</span>
              </button>
            </td>
          </tr>
          <!-- Confirmation Dialog 2 -->
          <v-dialog v-model="dialogConfirmation2" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">Confirmation</v-card-title>
              <v-card-text v-if="selectedItem">
                Update Student's Borrow Status
                <div>
                  <strong>Student Grade:</strong> {{ selectedItem.student_grade }}
                </div>
                <div>
                  <strong>Student Section:</strong> {{ selectedItem.student_section }}
                </div>
                <div>
                  <strong>Rental Record:</strong> {{ selectedItem.rental_record }}
                </div>
              </v-card-text>
              <v-card-actions> 
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" @click="back">Return</v-btn>
                <v-btn color="blue darken-1" @click="backback">Return With Damage</v-btn>
                <v-btn color="blue darken-1" @click="closeConfirmation2">No</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </template>
      </v-data-table>
    </div>
  </template>
<script>
import axios from 'axios';
export default {
  data() {
    return {
      availableStatus: ["Pending", "Overdue", "Returned", "Returned With Damage"],
      data: [],
      search: '',
      selectedItem: null,
      headers: [
        { title: 'Student ID', key: 'student_id' },
        { title: 'Grade', key: 'student_grade' },
        { title: 'Section', key: 'student_section' },
        { title: 'Rental Record', key: 'rental_record' },
        { title: 'Release Date', key: 'release_date' },
        { title: 'Status', key: 'borrow_status' },
        { title: 'Action', sortable: false },
      ],
      loading: false,
      dialog: false,
      dialogDelete: false,
      dialogConfirmation: false,
      dialogConfirmation2: false,
      editedItem: {
        book_id: '',
        student_id: '',
        student_grade: '',
        student_section: '',
        rental_record: '',
        status: '',
      },
      selectedGrade: null,
      selectedSection: null,
      fieldErrors: {
        book_id: false,
        student_id: false,
      },
    };
  },
  computed: {
    displayedData() {
      if (this.search) {
        return this.filteredData.filter((item) =>
          Object.values(item).some((value) =>
            value.toLowerCase().includes(this.search.toLowerCase())
          )
        );
      }
      return this.filteredData;
    },
    filteredData() {
      let filtered = this.data;
      if (this.selectedGrade) {
        filtered = filtered.filter(item => item.student_grade === this.selectedGrade);
      }
      if (this.selectedSection) {
        filtered = filtered.filter(item => item.student_section === this.selectedSection);
      }
      return filtered;
    },
    grades() {
      // Assuming grades are fetched from API or static list
      // Replace with actual data source
      return [...new Set(this.data.map(item => item.student_grade))];
    },
    sections() {
      // Assuming sections are fetched from API or static list
      // Replace with actual data source
      return [...new Set(this.data.map(item => item.student_section))];
    },
    formTitle() {
      return this.editedItem.borrow_id ? 'Add Record' : 'New Borrow Record';
    },
  },
  created() {
    this.fetchData();
  },
  methods: {
    formatDate2(dateString) {
      const options = {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      };
      const date = new Date(dateString);
      return date.toLocaleString('en-US', options);
    },
    formatDate(dateString) {
      const options = {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
      };
      const date = new Date(dateString);
      return date.toLocaleString('en-US', options);
    },
    fetchData() {
      axios.get('/borrowstatus')
        .then((response) => {
          const borrowStatusData = response.data;

          const studentIds = borrowStatusData.map(item => item.student_id);
          axios.get('/student', { params: { ids: studentIds } })
            .then((studentResponse) => {
              const students = studentResponse.data;

              const bookIds = borrowStatusData.map(item => item.book_id);
              axios.get('/books', { params: { ids: bookIds } })
                .then((bookResponse) => {
                  const books = bookResponse.data;

                  this.data = borrowStatusData.map(statusItem => {
                    const student = students.student.find(student => student.student_id === statusItem.student_id);
                    const book = books.find(book => book.book_id === statusItem.book_id);
                    return {
                      ...statusItem,
                      student_grade: student ? student.student_grade : '',
                      student_section: student ? student.student_section : '',
                      rental_record: book ? book.rental_record : '',
                    };
                  });
                  this.$forceUpdate();
                })
                .catch((bookError) => {
                  console.error('Error fetching book data:', bookError);
                });
            })
            .catch((studentError) => {
              console.error('Error fetching student data:', studentError);
            });
        })
        .catch((error) => {
          console.error('Error fetching borrow status data:', error);
        });
    },
    showConfirmation() {
      this.fieldErrors = {
        book_id: this.editedItem.book_id.trim() === '',
        student_id: this.editedItem.student_id.trim() === '',
      };
      if (this.isFormValid()) {
        this.dialogConfirmation = true;
      }
    },
    getErrorMessage(field) {
      if (this.fieldErrors[field]) {
        return 'This field is required';
      }
      return '';
    },
    isFormValid() {
      return (
        this.editedItem.book_id.trim() !== '' &&
        this.editedItem.student_id.trim() !== ''
      );
    },
    closeConfirmation() {
      this.dialogConfirmation = false;
    },
    returnConfirmation(item) {
      this.selectedItem = item;
      this.dialogConfirmation2 = true;
    },
    closeConfirmation2() {
      this.dialogConfirmation2 = false;
    },
    back() {
      axios.put('/borrowstatus/' + this.selectedItem.borrow_id)
        .then(() => {
          this.loading = true;
          this.loading = false;
          this.closeConfirmation2();
          this.fetchData();
        })
        .catch(error => {
          console.error('Error updating borrow status:', error);
          this.loading = false;
        });
    },
    backback() {
      axios.put('/borrowstatus/damaged/' + this.selectedItem.borrow_id)
        .then(() => {
          this.loading = true;
          this.loading = false;
          this.closeConfirmation2();
          this.fetchData();
        })
        .catch(error => {
          console.error('Error updating borrow status:', error);
          this.loading = false;
        });
    },
    save() {
      axios.post('/borrowstatus', this.editedItem)
        .then(() => {
          this.fetchData();
          this.closeConfirmation();
          this.close();
        })
        .catch(error => {
          console.error('Error adding record:', error);
        });
    },
    close() {
      this.dialog = false;
      this.editedItem = {
        borrow_id: '',
        student_grade: '',
        student_section: '',
        rental_record: '',
        status: '',
      };
    },
    editItem(item) {
      this.editedItem = { ...item };
      this.dialog = true;
    },
    deleteItem(item) {
      this.editedItem = { ...item };
      this.dialogDelete = true;
    },
    deleteItemConfirm() {
      const index = this.data.findIndex(
        (item) => item.borrow_id === this.editedItem.borrow_id
      );
      this.data.splice(index, 1);
      this.closeDelete();
    },
    closeDelete() {
      this.dialogDelete = false;
      this.editedItem = {
        borrow_id: '',
        student_grade: '',
        student_section: '',
        rental_record: '',
        status: '',
      };
    },
  },
};
</script>

<style lang="scss" scoped>
.green-text {
  color: green;
}
.white-text {
  color: white;
}
.v-data-table {
  height: 100%;
}
</style>
  