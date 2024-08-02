<template>
  <div>
    <v-data-table
      :search="search"
      :headers="visibleHeaders"
      :items="sortedData"
      :sort-by="[{ key: 'login_date', order: 'desc' }]"
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
          <v-select
            v-model="selectedSchoolYear"
            :items="schoolYears"
            label="Filter School Year"
            class="mx-2"
            density="compact"
            outlined
          ></v-select>
          <v-select
            v-model="selectedMonthly"
            :items="monthlyOptions"
            label="Filter Monthly"
            class="mx-2"
            density="compact"
            outlined
          ></v-select>
        </v-toolbar>
      </template>

      <template v-slot:item="{ item }">
        <tr>
          <td>{{ studentNames[item.student_id] }}</td>
          <td>{{ formatDate(item.login_date) }}</td>
          <td>{{ formatTime(item.login_time) }}</td>
          <td>{{ formatTime(item.logout_time) }}</td>
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
      search: '',
      headers: [
        { title: 'Student Name', key: 'studentNames' },
        { title: 'Date', key: 'login_date' },
        { title: 'Time In', key: 'login_time' },
        { title: 'Time Out', key: 'logout_time' }
      ],
      data: [],
      schoolYears: ['All'], 
      monthlyOptions: [
        'All',
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
      ],
      selectedSchoolYear: 'All',  
      selectedMonthly: 'All', 
      studentNames: {},    
    };
  },
  computed: {
    visibleHeaders() {
      return this.headers;
    },
    filteredData() {
      return this.data
        .map(item => {
          const date = new Date(item.login_date);
          const year = date.getFullYear();
          const month = date.getMonth();
          const day = date.getDate();

          let schoolYear;
          if (month > 6 || (month === 6 && day >= 29)) {
            schoolYear = `${year}-${year + 1}`;
          } else {
            schoolYear = `${year - 1}-${year}`;
          }

          return {
            ...item,
            schoolYear,
            month: date.toLocaleString('default', { month: 'long' })
          };
        })
        .filter(item => {
          return (
            (this.selectedSchoolYear === 'All' || item.schoolYear === this.selectedSchoolYear) &&
            (this.selectedMonthly === 'All' || item.month === this.selectedMonthly) &&
            (this.search === '' ||
              Object.values(item).some(value =>
                value.toString().toLowerCase().includes(this.search.toLowerCase())
              ))
          );
        });
    },
    sortedData() {
      return this.filteredData.sort((b, a) => {
        const dateA = new Date(`${a.login_date}T${a.login_time}`);
        const dateB = new Date(`${b.login_date}T${b.login_time}`);
        return dateA - dateB;
      });
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      try {
        const logsResponse = await axios.get('/logs');
        this.data = logsResponse.data;

        console.log('Logs Data:', this.data);

        const schoolYears = [...new Set(
          this.data.map(item => {
            const date = new Date(item.login_date);
            const year = date.getFullYear();
            const month = date.getMonth();
            const day = date.getDate();
            return month > 6 || (month === 6 && day >= 29) ? `${year}-${year + 1}` : `${year - 1}-${year}`;
          })
        )].sort((a, b) => {
          const [startYearA] = a.split('-').map(Number);
          const [startYearB] = b.split('-').map(Number);
          return startYearB - startYearA;
        });

        this.schoolYears = ['All', ...schoolYears];

        const studentIds = this.data.map(item => item.student_id);

        console.log('Student IDs:', studentIds);
        const studentResponse = await axios.get('/student', { params: { ids: studentIds } });
        const students = studentResponse.data;

        console.log('Students Data:', students);

        this.studentNames = students.reduce((acc, student) => {
          acc[student.student_id] = `${student.first_name} ${student.last_name}`;
          return acc;
        }, {});

        console.log('Student Names:', this.studentNames);

      } catch (error) {
        console.error('Error fetching data:', error);
      }
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
      formatTime(timeString) {
        if (typeof timeString !== 'string') {
          console.warn('Invalid time format:', timeString);
          return 'Invalid Time';
        }

        const [hours, minutes] = timeString.split(':').map(Number);
        
        // Check if hours and minutes are valid
        if (isNaN(hours) || isNaN(minutes) || hours < 0 || hours > 23 || minutes < 0 || minutes > 59) {
          console.warn('Invalid time value:', timeString);
          return 'Invalid Time';
        }

        const isPM = hours >= 12;
        const formattedHours = ((hours % 12) || 12).toString().padStart(2, '0');
        const formattedMinutes = minutes.toString().padStart(2, '0');
        const period = isPM ? 'PM' : 'AM';
        
        return `${formattedHours}:${formattedMinutes} ${period}`;
      }
  }
};
</script>

<style lang="scss" scoped>
.v-data-table {
  height: 100%;
}
</style>
