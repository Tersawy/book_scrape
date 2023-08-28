<?php

namespace App\Http\Controllers;

use App\Helpers\Kotobati;

use App\Models\KotobatiBookScrape;

use Illuminate\Database\QueryException;

use Exception;

class HomeController extends Controller
{
    public function renderHomePage()
    {
        $kotobati = new Kotobati();

        try {
            $kotobati->getRandomBooks(20);

            $kotobati->loadBooksDetails();
        } catch (Exception $e) {
            return response()->view("home", ['books' => [], 'error' => $e->getMessage()]);
        }

        $books = $kotobati->books;

        $existingBookIds = KotobatiBookScrape::whereIn('book_id', array_column($books, 'book_id'))->pluck('book_id')->toArray();

        $recordsToInsert = [];

        foreach ($books as $book) {
            if (!in_array($book['book_id'], $existingBookIds)) {
                $recordsToInsert[] = $book;
            }
        }

        if (!empty($recordsToInsert)) {
            KotobatiBookScrape::insert($recordsToInsert);
        }

        return response()->view("home", ['books' => $books]);
    }

    public function getBooks()
    {
        $kotobati = new Kotobati();

        try {
            $kotobati->getRandomBooks();

            $kotobati->loadBooksDetails();
        } catch (Exception $e) {
            return response()->json(['message' => 'failed to load books'], 500);
        }

        $books = $kotobati->books;

        $existingBookIds = KotobatiBookScrape::whereIn('book_id', array_column($books, 'book_id'))->pluck('book_id')->toArray();

        $recordsToInsert = [];

        foreach ($books as $book) {
            if (!in_array($book['book_id'], $existingBookIds)) {
                $recordsToInsert[] = $book;
            }
        }

        if (!empty($recordsToInsert)) {
            try {
                KotobatiBookScrape::insert($recordsToInsert);
            } catch (QueryException $e) {
                return response()->json(['books' => $books, 'error' => 'failed to save books']);
            }
        }

        return response()->json(['books' => $books]);
    }
}
