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
          <td :class="{'red-text': item.borrow_status === 1}">{{ availableStatus[item.borrow_status] }}</td>
          <td>
            <button v-if="item.borrow_status !== 2" type="button" class="btn btn-success white-text" @click="returnConfirmation(item.borrow_id)" :disabled="loading">
              <span v-if="loading">Returning...</span>
              <span v-else>Return</span>
            </button>
          </td>
        </tr>
        <v-dialog v-model="dialogConfirmation2" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">Confirmation</v-card-title>
              <v-card-text>
                Are you sure you want to update borrow status to returned?
                <div>
                  <strong>Student Name:</strong> {{ item.student_name }}
                </div>
                <div>
                  <strong>Student LRN:</strong> {{ item.student_lrn }}
                </div>
                <div>
                  <strong>Book Title:</strong> {{ item.book_title }}
                </div>
                <div>
                  <strong>Book Code:</strong> {{ item.access_no }}
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
      selectedBorrowId: null,
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
          book_title: '',
          student_id: '',
          access_no: '',
      },
      selected: [],
    };
  },
  mounted() {
    this.fetchData();
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
      return this.data.filter(item => item.borrow_status == 1);
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

              const bookIds = borrowStatusData.map(item => item.book_title);
              axios.get('/books', { params: { ids: bookIds } })
                .then((bookResponse) => {
                  const books = bookResponse.data;

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
      this.dialogConfirmation = true;
    },
    closeConfirmation() {
      this.dialogConfirmation = false;
    },
    returnConfirmation(borrow_id){
      this.dialogConfirmation2 = true;
      this.selectedBorrowId = borrow_id;
    },
    closeConfirmation2() {
      this.dialogConfirmation2 = false;
    },
    back() {
      axios.put('/borrowstatus/' + this.selectedBorrowId)
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
.red-text {
color: red;
}
.white-text {
  color: white;
}
  .v-data-table {
     height: 100%;
  }
</style>
  