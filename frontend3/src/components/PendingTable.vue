<template>
  <div>
    <v-data-table
      :search="search"
      :headers="headers"
      :items="displayedData"
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

          
          <v-dialog v-model="dialog" max-width="1000px">
            <template v-slot:activator="{ props }">
              <v-btn class="mb-2 rounded-l" color="primary" dark v-bind="props" prepend-icon="mdi-plus">Add Record</v-btn>
            </template>

            <v-card>
              <v-card-title class="text-h5">{{ formTitle }}</v-card-title>
              <v-card-text>
                <v-form @submit.prevent="save">
                  <!--<v-text-field v-model="editedItem.book_title" label="Book Title" :error-messages="getErrorMessage('book_title')"></v-text-field>-->
                  <v-select
                  v-model="editedItem.book_title"
                  :items="bookTitles"
                  item-text="book_title"
                  item-value="book_title"
                  label="Book Title"
                  :error-messages="getErrorMessage('book_title')"
                ></v-select>
                  <v-text-field v-model="editedItem.student_id" label="Student ID" :error-messages="getErrorMessage('student_id')"></v-text-field>
                  <v-text-field v-model="editedItem.return_duedate" type="date" label="Return Due Date" :error-messages="getErrorMessage('return_duedate')"></v-text-field>
                  <v-text-field v-model="editedItem.access_no" label="Book Code" :error-messages="getErrorMessage('access_no')"></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" @click="close">Cancel</v-btn>
                <v-btn color="blue darken-1" @click="showConfirmation">Add Record</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogConfirmation" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">Confirmation</v-card-title>
              <v-card-text>
                Are you sure you want to add this record?
                <div>
                  <strong>Book TItle:</strong> {{ editedItem.book_title }}
                </div>
                <div>
                  <strong>Student ID:</strong> {{ editedItem.student_id }}
                </div>
                <div>
                  <strong>Book Code:</strong> {{ editedItem.access_no }}
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

      
      <template v-slot:item="{ item }">
        <tr>
          <td>{{ item.borrow_id }}</td>
          <td>{{ item.student_name }}</td>
          <td>{{ item.student_lrn }}</td>
          <td>{{ item.book_title }}</td>
          <td>{{ item.access_no }}</td>
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
        <v-dialog v-model="dialogConfirmation2" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">Confirmation</v-card-title>
              <v-card-text v-if="selectedItem">
                Update Student's Borrow Status
                <div>
                  <strong>Student Name:</strong> {{ selectedItem.student_name }}
                </div>
                <div>
                  <strong>Student LRN:</strong> {{ selectedItem.student_lrn }}
                </div>
                <div>
                  <strong>Book Title:</strong> {{ selectedItem.book_title }}
                </div>
                <div>
                  <strong>Book Code:</strong> {{ selectedItem.access_no }}
                </div>
              </v-card-text>
              <v-card-actions> 
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" @click="back">Return</v-btn>
                <v-btn color="blue darken-1" @click="backback">Damaged</v-btn>
                <v-btn color="blue darken-1" @click="backbackback">Lost</v-btn>
                <v-btn color="blue darken-1" @click="closeConfirmation2">Cancel</v-btn>
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
      availableStatus: ["Pending", "Overdue", "Returned", "Returned with Damage", "Lost", "Returned with Damage", "Lost"],
      data: [],
      search: '',
      selectedItem: null,
      headers: [
          { title: 'Borrow ID', key: 'borrow_id' },
          { title: 'Student Name', key: 'student_name' },
          { title: 'LRN', key: 'student_lrn' },
          { title: 'Book Title', key: 'book_title' },
          { title: 'Book Code', key: 'access_no' },
          { title: 'Borrow Date', key: 'borrowed_date' },
          { title: 'Return Due Date', key: 'return_duedate' },
          { title: 'Status', key: 'borrow_status' },
          { title: 'Action', sortable: false },
      ],
      loading: false,
      dialog: false,
      dialogDelete: false,
      dialogConfirmation: false, 
      dialogConfirmation2: false, 
      editedItem: {
          student_id: '',
          access_no: '',
          student_name: '',
          student_lrn: '',
          book_title: '',
          access_no: '',
      },
      selected: [],
      fieldErrors: {
        book_title: false,
        student_id: false,
        access_no: false,
      },
      titles: [],
    };
  },
  mounted() {
    this.fetchData();
    this.fetchItems();
  },
  computed: {
    displayedData() {
      if (this.search) {
        return this.data.filter((item) =>
          Object.values(item).some((value) =>
            value.toLowerCase().includes(this.search.toLowerCase())
          )
        );
      }
      return this.data.filter(item => item.borrow_status == 0);
    },
    bookTitles() {
      return this.titles.map(item => item.book_title);
    },
    formTitle() {
      return this.editedItem.borrow_id ? 'Add Record' : 'New Borrow Record';
    },
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchItems() {
      axios.get('/books')
        .then(response => {
          this.titles = response.data;
          console.log(response.data);
        })
        .catch(error => {
          console.error('Error fetching book titles:', error);
        });
    },
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

              const bookIds = borrowStatusData.map(item => item.book_title);
              axios.get('/books', { params: { ids: bookIds } })
                .then((bookResponse) => {
                  const books = bookResponse.data;
                  console.log(bookResponse);

                  this.data = borrowStatusData.map(statusItem => {
                    const student = students.find(student => student.student_id === statusItem.student_id);
                    const book = books.find(book => book.book_title === statusItem.book_title);
                    let middleInitial = '';
                    if (student && student.middle_name) {
                      middleInitial = student.middle_name.charAt(0) + '.';
                    }
                    return {
                      ...statusItem,
                      student_name: student ? `${student.first_name} ${middleInitial} ${student.last_name}${student.extension ? ' ' + student.extension : ''}` : '',
                      student_lrn: student ? student.student_lrn : '',
                      book_title: book ? book.book_title : '',
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
        book_title: this.editedItem.book_title.trim() === '',
        student_id: this.editedItem.student_id.trim() === '',
        access_no: this.editedItem.access_no.trim() === '',
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
        this.editedItem.book_title.trim() !== '' &&
        this.editedItem.student_id.trim() !== '' &&
        this.editedItem.access_no.trim() !== '' 
      );
    },
    closeConfirmation() {
      this.dialogConfirmation = false;
    },
    returnConfirmation(item){
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
      axios.put('/borrowstatus/damagedpay/' + this.selectedItem.borrow_id)
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
    backbackback() {
      axios.put('/borrowstatus/lostpay/' + this.selectedItem.borrow_id)
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
        this.fetchData()
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
          student_name: '',
          student_lrn: '',
          book_title: '',
          borrow_date: '',
          return_duedate: '',
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
          student_name: '',
          student_lrn: '',
          book_title: '',
          borrow_date: '',
          return_duedate: '',
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
