<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BookResource;
use App\Repositories\BookRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class BookController extends BaseController
{

    protected $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $book = $this->bookRepository->getAllBooks();
        return $this->sendResponse(BookResource::collection($book), 'All books data successfully.', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Gate::denies('manage-books')) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $input = $request->all();
        $validator = Validator::make($input, [
            'title'     => 'required|string',
            'author'    => 'required|string',
            'year'      => 'required|integer',
            'isbn'      => 'required|string|unique:books',
            'available' => 'required|boolean'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 400);       
        }
        $book = $this->bookRepository->createBook($input);
        return $this->sendResponse(new BookResource($book), 'Book created successfully.', 201);
    
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = $this->bookRepository->getBookById($id);
        if(!$book) {
            return $this->sendError('Book not found.', [], 404);
        }
        return $this->sendResponse(new BookResource($book), 'Book retrieved successfully.', 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Gate::denies('manage-books')) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $input      = $request->all();
        $validator  = Validator::make($input, [
            'title'     => 'required|string',
            'author'    => 'required|string',
            'year'      => 'required|integer',
            'isbn'      => 'required|string|unique:books',
            'available' => 'required|boolean'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 400);       
        }

        $book = $this->bookRepository->updateBook($input, $id);

        if (!$book) {
            return $this->sendError('Book not found.', [], 404);
        }
        return $this->sendResponse(new BookResource($book), 'Book updated successfully.', 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Gate::denies('manage-books')) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $deleted = $this->bookRepository->deleteBook($id);
        if (!$deleted) {
            return $this->sendError('Book not found.', [], 404);
        }
        return $this->sendResponse('Book delete successfully.', [], 204);
    }

    public function borrow($id)
    {
        if (Gate::denies('borrow-return-books')) {
            return $this->sendError('Unauthorized.', [], 403);
        }

        $book = $this->bookRepository->borrowBook(auth()->id(), $id);

        if (!$book) {
            return $this->sendError('Book not found or already borrowed.', [], 404);
        }
        return $this->sendResponse($book, 'Book borrowed successfully.', 200);
    }

    public function returnBook($id)
    {
        if (Gate::denies('borrow-return-books')) {
            return $this->sendError('Unauthorized.', [], 401);
        }

        $book = $this->bookRepository->returnBook(auth()->id(), $id);

        if (!$book) {
            return $this->sendError($book, 'Book not found or not borrowed by this user.', 404);
        }
        return $this->sendResponse('Book returned successfully.', [], 200);
        
    }
}
