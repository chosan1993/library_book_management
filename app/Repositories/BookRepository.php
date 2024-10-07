<?php

namespace App\Repositories;

use App\Models\Book;

class BookRepository implements BookRepositoryInterface
{

    public function getAllBooks()
    {
        return Book::all();
    }

    public function createBook(array $data)
    {
        return Book::create($data);
    }

    public function getBookById($id)
    {
        return Book::find($id);
    }

    public function updateBook(array $data, $id)
    {
        $book = $this->getBookById($id);
        if ($book) {
            $book->update($data);
            return $book;
        }
        return null;
    }

    public function deleteBook($id)
    {
        $book = $this->getBookById($id);
        if ($book) {
            return $book->delete();
        }
        return false;
    }

    public function borrowBook($userId, $bookId)
    {
        $book = $this->getBookById($bookId);
        if ($book) {
            $book->borrowed_by = $userId;
            $book->save();
            return $book;
        }
        return null;
    }


    public function returnBook($userId, $bookId)
    {
        $book = $this->getBookById($bookId);
        if ($book && $book->borrowed_by === $userId) {
            $book->borrowed_by = null;
            $book->save();
            return $book;
        }
        return null;
    }
}
