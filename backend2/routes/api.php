<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCategoryController;
use App\Http\Controllers\BorrowedBookController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\LibraryStatusController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FacultyBorrowController;
use App\Http\Controllers\LoggingController;
use App\Http\Controllers\RentalController;

Route::prefix('api')->group(function () {
    // Borrowed Books
    Route::get('/borrowstatus/new', [BorrowedBookController::class, 'showNewestStatusId']); //check
    Route::get('/borrowstatus', [BorrowedBookController::class, 'allBorrows']); //check
    Route::post('/borrowstatus', [BorrowedBookController::class, 'createBorrowStatus']); //check
    Route::get('/borrowstatus/user/{id}', [BorrowedBookController::class, 'getAllBorrowByStudent']); //check
    Route::get('/borrowstatus/borrow/{id}', [BorrowedBookController::class, 'getAllBorrowByBorrow']); //check
    Route::put('/borrowstatus/{id}', [BorrowedBookController::class, 'updateBorrowStatus']); //check
    Route::put('/borrowstatus/damaged/{id}', [BorrowedBookController::class, 'damagedBorrowStatus']);
    Route::put('/borrowstatus/damagedpay/{id}', [BorrowedBookController::class, 'dPayBorrowStatus']);
    Route::put('/borrowstatus/lostpay/{id}', [BorrowedBookController::class, 'lPayBorrowStatus']);
    Route::put('/borrowstatus/lost/{id}', [BorrowedBookController::class, 'lostBorrowStatus']);

    // Students Profile
    Route::get('/student', [StudentProfileController::class, 'allStudents']); //check
    Route::get('/student/{id}', [StudentProfileController::class, 'showAStudent']); //check
    Route::post('/student', [StudentProfileController::class, 'createStudentP']);
    Route::put('/student/{id}', [StudentProfileController::class, 'updateStudentP']);
    Route::delete('/student/{id}', [StudentProfileController::class, 'deleteStudentP']);

    // Books
    Route::get('/books', [BookController::class, 'allBooks']); //check
    Route::get('/books/{id}', [BookController::class, 'showABook']); //check
    Route::post('/books', [BookController::class, 'createBook']); //check
    Route::delete('/books/{id}', [BookController::class, 'deleteBookP']); //check
    Route::put('/books/{id}', [BookController::class, 'updateBookP']); //check
    Route::put('/books/archive/{id}', [BookController::class, 'archiveBook']);
    Route::put('/books/unarchive/{id}', [BookController::class, 'unarchiveBook']);

    // Categories
    Route::get('/category', [BookCategoryController::class, 'allCategory']); //check
    Route::get('/category/{id}', [BookCategoryController::class, 'showACategory']); //check
    Route::post('/category', [BookCategoryController::class, 'createCategory']); //check
    Route::delete('/category/{id}', [BookCategoryController::class, 'deleteCategory']); //check
    Route::put('/category/{id}', [BookCategoryController::class, 'updateCategory']); //check
    Route::put('/category/archive/{id}', [BookCategoryController::class, 'archiveCategory']);
    Route::put('/category/unarchive/{id}', [BookCategoryController::class, 'unarchiveCategory']);

    // Faculty Borrows
    Route::get('/faculty/borrows', [FacultyBorrowController::class, 'allFacultyBorrows']);
    Route::post('/faculty/borrows', [FacultyBorrowController::class, 'createFacultyBorrow']);
    Route::get('/faculty/borrows/{id}', [FacultyBorrowController::class, 'getFacultyBorrow']);
    Route::put('/faculty/borrows/update/{id}', [FacultyBorrowController::class, 'updateFacultyBorrowStatus']);
    Route::put('/faculty/damaged/{id}', [FacultyBorrowController::class, 'damagedFacultyBorrowStatus']);
    Route::put('/faculty/lost/{id}', [FacultyBorrowController::class, 'lostFacultyBorrowStatus']);
    Route::put('/faculty/borrows/cancel/{id}', [FacultyBorrowController::class, 'cancelFacultyBorrowStatus']);

    // Faculty
    Route::get('/faculty', [FacultyController::class, 'allFaculty']);
    Route::get('/faculty/{id}', [FacultyController::class, 'showFaculty']);
    Route::post('/faculty', [FacultyController::class, 'createFaculty']);
    Route::put('/faculty/{id}', [FacultyController::class, 'updateFaculty']);
    Route::delete('/faculty/{id}', [FacultyController::class, 'deleteFaculty']);

    //Librarian
    Route::get('/librarian', [LibrarianController::class, 'allLibrarian']);

    //Library Status
    Route::get('/librarystatus', [LibraryStatusController::class, 'allLibStat']);
    Route::get('/librarystatus/{id}', [LibraryStatusController::class, 'showALibStat']);

    //Logging
    Route::get('/logs', [LoggingController::class, 'allLog']);
    Route::get('/logs/{id}', [LoggingController::class, 'showALog']);
    Route::post('/logs', [LoggingController::class, 'createALog']);

    //Rental
    Route::get('/rental', [RentalController::class, 'allRental']);
    Route::get('/rental/{id}', [RentalController::class, 'showARental']);
    Route::post('/rental', [RentalController::class, 'createALog']);
});
