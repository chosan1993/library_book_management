<?php

namespace App\Repositories;

use App\Models\Book;

interface BookRepositoryInterface
{
    public function getAllBooks();
    public function createBook(array $data);
    public function getBookById($id);
    public function updateBook(array $data, $id);
    public function deleteBook($id);
    public function borrowBook($userId, $bookId);
    public function returnBook($userId, $bookId);
}
