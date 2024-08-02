<template>
  <div>
    <v-data-table
      :search="search"
      :headers="headers"
      :items="displayedData"
      :sort-by="[{ key: 'return_date', order: 'desc' }]"
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
          <td>{{ formatDate(item.return_date) }}</td>
          <td :class="{'red-text': item.borrow_status === 3 | item.borrow_status == 4, 'green-text': item.borrow_status === 2 }">{{ availableStatus[item.borrow_status] }}</td>
        </tr>
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
      headers: [
          { title: 'Borrow ID', key: 'borrow_id' },
          { title: 'Student Name', key: 'student_name' },
          { title: 'LRN', key: 'student_lrn' },
          { title: 'Book Title', key: 'book_title' },
          { title: 'Book Code', key: 'access_no' },
          { title: 'Borrow Date', key: 'borrowed_date' },
          { title: 'Return Date', key: 'return_date' },
          { title: 'Return Status', key: 'borrow_status' },
      ],
      dialog: false,
      dialogDelete: false,
      };
  },
  computed: {
    displayedData() {
      if (this.search) {
        return this.data.filter((item) =>
          Object.values(item).some((value) =>
            typeof value === 'string' && value.toLowerCase().includes(this.search.toLowerCase())
          )
        );
      }
      return this.data.filter(item => item.borrow_status == 2 || item.borrow_status == 3 || item.borrow_status == 4);
    },
    formTitle() {
      return this.editedItem.id ? 'Edit Book' : 'New Book';
    },
  },
  created(){
    this.fetchData();
  },
  methods: {
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
                  this.data.sort((a, b) => b.id - a.id);
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
    formatDate(dateString) {
      const options = {
        month: 'long',  
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
      };
      const date = new Date(dateString);
      return date.toLocaleString('en-US', options);
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
    save() {
      if (this.editedItem.id) {
        const index = this.data.findIndex((item) => item.id === this.editedItem.id);
        this.data.splice(index, 1, this.editedItem);
      } else {
        this.data.push({ ...this.editedItem });
      }
      this.close();
    },
    close() {
      this.dialog = false;
      this.editedItem = {
        id: '',
        student_lrn: '',
        student_name: '',
        book_title: '',
        borrow_date: '',
        return_date: '',
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
      const index = this.data.findIndex((item) => item.id === this.editedItem.id);
      this.data.splice(index, 1);
      this.closeDelete();
    },
    closeDelete() {
      this.dialogDelete = false;
      this.editedItem = {
        id: '',
        student_lrn: '',
        student_name: '',
        book_title: '',
        borrow_date: '',
        return_date: '',
      };
    },
  },
};
</script>
<style lang="scss" scoped>
.red-text {
color: red;
}
.green-text {
color: green;
}
.v-data-table {
   height: 100%;
}
</style>