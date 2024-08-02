<template>
  <v-data-table
    :search="search"
    :headers="headers"
    :items="displayedBooks"
    :sort-by="[{ key: 'book_id', order: 'asc' }]"
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
            <v-btn class="mb-2 rounded-l" color="primary" dark v-bind="props" prepend-icon="mdi-plus">Add Category</v-btn>
          </template>

          <!-- Content of the dialog for adding a book -->
          <v-card>
            <v-card-title class="text-h5">{{ formTitle }}</v-card-title>
            <v-card-text>
              <v-form @submit.prevent="save">
                <v-text-field v-model="editedItem.categ_name" label="Category Name" :error-messages="getErrorMessage('categ_name')"></v-text-field>
                <v-text-field v-model="editedItem.book_ddc" label="DDC" :error-messages="getErrorMessage('categ_name')"></v-text-field>
                <v-text-field v-model="editedItem.fine_if_lost" label="Fine if lost"></v-text-field>
                <v-text-field v-model="editedItem.fine_if_damaged" label="Fine if damaged"></v-text-field>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="close">Cancel</v-btn>
              <v-btn color="blue darken-1" @click="showConfirmation">Add</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogConfirmation" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Confirmation</v-card-title>
            <v-card-text>
              Are you sure you want to add this book category?
              <div>
                <strong>Category Name:</strong> {{ editedItem.categ_name }}
                <strong>DDC:</strong> {{ editedItem.book_ddc }}
                <strong>Fine if lost:</strong> {{ editedItem.fine_if_lost }}
                <strong>Fine if damaged:</strong> {{ editedItem.fine_if_damaged }}
              </div>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="save">Yes</v-btn>
              <v-btn color="blue darken-1" @click="closeConfirmation">No</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="editdialog" max-width="1000px">
          <v-card>
            <v-card-title class="text-h5">Edit Category</v-card-title>
            <v-card-text>
              <v-form @submit.prevent="saveEdit">
                <v-text-field v-model="editedItem.categ_name" label="Category Name"></v-text-field>
                <v-text-field v-model="editedItem.book_ddc" label="DDC"></v-text-field>
                <v-text-field v-model="editedItem.lost_fine" label="Fine if lost"></v-text-field>
                <v-text-field v-model="editedItem.damaged_fine" label="Fine if damaged"></v-text-field>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="closeEdit">Cancel</v-btn>
              <v-btn color="blue darken-1" @click="editConfirmation">Save</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogConfirmation2" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Confirmation</v-card-title>
            <v-card-text>
              Are you sure you want to update this book category?
              <div>
                <strong>Category Name:</strong> {{ editedItem.categ_name }}
                <strong>DDC:</strong> {{ editedItem.book_ddc }}
                <strong>Fine if lost:</strong> {{ editedItem.lost_fine }}
                <strong>Fine if damaged:</strong> {{ editedItem.damaged_fine }}
              </div>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="saveEdit">Yes</v-btn>
              <v-btn color="blue darken-1" @click="closeConfirmation2">No</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogArchiveConfirmation" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Confirmation</v-card-title>
            <v-card-text>
              Are you sure you want to archive this category?
              <div>
                <strong>Category:</strong> {{ itemToArchive.categ_name }}
              </div>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" @click="archiveItemConfirmed">Yes</v-btn>
              <v-btn color="blue darken-1" @click="closeArchiveConfirmation">No</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>

    <template v-slot:item="{ item }">
      <tr>
        <td>{{ item.categ_name }}</td>
        <td>{{ item.book_ddc }}</td>
        <td>{{ item.lost_fine }}</td>
        <td>{{ item.damaged_fine }}</td>
        <td>
          <v-icon class="me-2" size="small" style="color: #2F3F64" @click="editItem(item)">mdi-pencil</v-icon>
          <v-icon size="small" style="color: #B71C1C" @click="archiveItem(item)">mdi-archive</v-icon>
        </td>
      </tr>
    </template>
  </v-data-table>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      books: [],
      search: '',
      dialog: false,
      editdialog: false,
      dialogDelete: false,
      dialogConfirmation: false,
      dialogConfirmation2: false,
      isAddingBook: false,
      isEditingBook: false,
      dialogArchiveConfirmation: false,
      itemToArchive: null,
      headers: [
        { title: 'NAME', key: 'categ_name' },
        { title: 'DDC', key: 'book_ddc' },
        { title: 'LOST FINE', key: 'lost_fine' },
        { title: 'DAMAGE FINE', key: 'damaged_fine' },
        { title: 'Actions', sortable: false },
      ],
      editedItem: {
        categ_name: '',
        book_ddc: '',
        lost_fine: '',
        damaged_fine: '',
      },
      fieldErrors: {
        categ_name: false,
      },
    };
  },
  mounted() {
    this.fetchBooks();
  },
  methods: {
    fetchBooks() {
      axios.get('/category')
        .then(response => {
          this.books = response.data;
        })
        .catch(error => {
          console.error('Error fetching categories:', error);
        });
    },
    editItem(item) {
      this.editedItem = { ...item };
      this.editdialog = true;
    },
    returnToInventory() {
      this.$router.push('/bookinventory');
    },
    archiveItem(item) {
      this.itemToArchive = item;
      this.dialogArchiveConfirmation = true;
    },
    archiveItemConfirmed() {
      axios.put('/category/archive/' + this.itemToArchive.categ_name)
        .then(response => {
          this.fetchBooks();
        })
        .catch(error => {
          console.error('Error archiving category:', error);
        })
        .finally(() => {
          this.closeArchiveConfirmation();
        });
    },
    closeArchiveConfirmation() {
      this.dialogArchiveConfirmation = false;
    },
    deleteItemConfirm() {
      // Confirm deletion of a book
    },
    close() {
      // Close the dialog for adding/editing a book
      this.dialog = false;
      this.clearEditedItem();
    },
    closeEdit(){
      this.editdialog = false;
    },
    closeDelete() {
      // Close the deletion confirmation dialog
      this.dialogDelete = false;
    },
    showConfirmation() {
      this.fieldErrors = {
        categ_name: this.editedItem.categ_name.trim() === '',
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
        this.editedItem.categ_name.trim() !== ''
      );
    },
    closeConfirmation() {
      this.dialogConfirmation = false;
    },
    save() {
      this.isAddingBook = true;
      axios.post('/category', this.editedItem)
        .then(response => {
          this.books.push(response.data);
          this.closeConfirmation();
          this.close();
          this.clearEditedItem();
          this.fetchBooks();
        })
        .catch(error => {
          console.error('Error adding category:', error);
        })
        .finally(() => {
          this.isAddingBook = false;
        });
    },
    editConfirmation(){
      this.dialogConfirmation2 = true;
    },
    closeConfirmation2(){
      this.dialogConfirmation2 = false;
    },
    saveEdit(){
      this.isEditingBook = true;
      axios.put('/category/' + this.editedItem.categ_name, this.editedItem)
        .then(response => {
          this.closeConfirmation2();
          this.closeEdit();
          this.clearEditedItem();
          this.fetchBooks();
        })
        .catch(error => {
          console.error('Error adding book:', error);
        })
        .finally(() => {
          this.isEditingBook = false;
        });
    },
    clearEditedItem(){
      this.editedItem = {
        categ_name: '',
        book_ddc: '',
        lost_fine: '',
        damaged_fine: '',
      };
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Add Category' : 'Add Book Category';
    },
    displayedBooks() {
      const searchTerm = this.search.toLowerCase();
      return this.books.filter(book =>
        Object.values(book).some(value =>
          typeof value === 'string' && value.toLowerCase().includes(searchTerm)
        )
      ).filter(book => book.is_archived == 0);
    },
  },
  watch: {
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
  },
};
</script>
