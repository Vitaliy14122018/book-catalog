<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	public function index(Request $request)
	{
		// Получаем параметр сортировки (по умолчанию сортируем по названию по возрастанию)
		$sortOrder = $request->get('sort', 'asc'); // значение по умолчанию 'asc'

		// Получаем параметры поиска
		$searchTitle = $request->get('search_title');
		$searchAuthor = $request->get('search_author');

		// Получаем список всех авторов
		$authors = Author::all();

		// Начинаем запрос для получения книг
		$query = Book::with('authors')
			->orderBy('title', $sortOrder); // сортировка по названию книги

		// Если есть поисковый запрос по названию, фильтруем книги
		if ($searchTitle) {
			$query->where('title', 'like', '%' . $searchTitle . '%');
		}

		// Если есть поисковый запрос по автору, фильтруем книги
		if ($searchAuthor) {
			$query->whereHas('authors', function ($q) use ($searchAuthor) {
				$q->where('surname', 'like', '%' . $searchAuthor . '%');
			});
		}

		// Получаем книги с пагинацией
		$books = $query->paginate(2);

		// Обрабатываем дату перед отправкой в представление
		$books->transform(function ($book) {
			$book->formatted_published_at = $book->published_at ? $book->published_at->format('d.m.Y') : 'Не указано';
			return $book;
		});

		// Передаем книги и авторов в представление
		return view('books.index', compact('books', 'authors'));
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
			'authors' => 'required|array',
			'authors.*' => 'exists:authors,id',
			'published_at' => 'nullable|date',
		]);

		// Создаем новую книгу
		$book = new Book();
		$book->title = $validatedData['title'];
		$book->description = $validatedData['description'] ?? null;
		$book->published_at = $validatedData['published_at'] ?? null;

		// Загружаем изображение, если оно есть
		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$name = uniqid() . '.' . $file->getClientOriginalExtension();
			$path = $file->storeAs('books', $name, 'public');
			$book->image = $path;
		}

		// Сохраняем книгу
		$book->save();

		// Синхронизируем авторов с книгой
		$book->authors()->sync($validatedData['authors']);

		// Получаем параметры фильтров и сортировки
		$searchTitle = $request->input('search_title');
		$searchAuthor = $request->input('search_author');
		$sort = $request->input('sort', 'asc');
		
		// Создаем запрос для получения списка книг с фильтрами и сортировкой
		$query = Book::query();
		
		// Применяем фильтры и сортировку
		if ($searchTitle) {
			$query->where('title', 'like', '%' . $searchTitle . '%');
		}

		if ($searchAuthor) {
			$query->whereHas('authors', function ($q) use ($searchAuthor) {
				$q->where('surname', 'like', '%' . $searchAuthor . '%');
			});
		}

		// Сортируем по названию
		if ($sort === 'desc') {
			$query->orderBy('title', 'desc');
		} else {
			$query->orderBy('title', 'asc');
		}

		// Пагинируем результаты
		$books = $query->paginate(2);
		
		// Меняем базовый путь для пагинации
		$books->withPath('/books');

		// Возвращаем список книг и пагинацию
		return response()->json([
			'status' => 'success',
			'message' => 'Книга успешно сохранена!',
			'paginationHtml' => view('books.partials.pagination', [
				'books' => $books
			])->render(),
			'booksHtml' => view('books.partials.table', [
				'books' => $books
			])->render(),
		]);
	}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show(Book $book)
	{
		// Получаем авторов книги
		$authors = $book->authors;

		return view('books.partials.show', compact('book', 'authors'));
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function edit(Book $book)
	{
		// Получаем авторов книги
		$authors = $book->authors;

		// Формируем ответ с данными книги
		return response()->json([
			'status' => 'success',
			'book' => $book,
			'authors' => $authors,
		]);
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, Book $book)
	{
		//  var_dump($request->all());
   // die;  // Останавливаем выполнение кода после вывода данных
		$validatedData = $request->validate([
			'title' => 'required|string|max:255',
			'description' => 'nullable|string',
			'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
			'authors' => 'required|array',
			'authors.*' => 'exists:authors,id',
			'published_at' => 'nullable|date',
		]);

		$book->title = $validatedData['title'];
		$book->description = $validatedData['description'] ?? null;
		$book->published_at = $validatedData['published_at'] ?? null;

		if ($request->hasFile('image')) {
			// Если нужно, удалить старую картинку:
			// Storage::disk('public')->delete($book->image);

			$file = $request->file('image');
			$name = uniqid() . '.' . $file->getClientOriginalExtension();
			$path = $file->storeAs('books', $name, 'public');
			$book->image = $path;
		}

		$book->save();

		$book->authors()->sync($validatedData['authors']);
		
		// Загружаем авторов вместе с книгой
		$book->load('authors');
		
		// Получаем параметры фильтров и сортировки
		$searchTitle = $request->input('search_title');
		$searchAuthor = $request->input('search_author');
		$sort = $request->input('sort', 'asc');
		
		 // Создаем запрос для получения списка книг с фильтрами и сортировкой
		$query = Book::query();
		
		// Применяем сортировку и фильтры
		if ($searchTitle) {
			$query->where('title', 'like', '%' . $searchTitle . '%');
		}

		if ($searchAuthor) {
			$query->whereHas('authors', function ($q) use ($searchAuthor) {
				$q->where('surname', 'like', '%' . $searchAuthor . '%');
			});
		}

		// Сортируем по названию
		if ($sort === 'desc') {
			$query->orderBy('title', 'desc');
		} else {
			$query->orderBy('title', 'asc');
		}
		
		// Пагинируем результаты
		$books = $query->paginate(2);
		
		// Меняем базовый путь для пагинации
		$books->withPath('/books');

		return response()->json([
			'status' => 'success',
			'message' => 'Книга успешно обновлена!',
			'paginationHtml' => view('books.partials.pagination', [
				'books' => $books
			])->render(),
			'book' => $book,
		]);
	}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function destroy(Book $book, Request $request)
	{
		// Проверяем, есть ли у книги изображение
		if ($book->image && Storage::exists('public/' . $book->image)) {
			// Удаляем изображение
			Storage::delete('public/' . $book->image);
		}
		// Удаляем книгу
		$book->delete();

		// Получаем параметры фильтров и сортировки
		$searchTitle = $request->input('search_title');
		$searchAuthor = $request->input('search_author');
		$sort = $request->input('sort', 'asc');
		
		// Создаем запрос для получения списка книг с фильтрами и сортировкой
		$query = Book::query();

		// Применяем фильтры и сортировку
		if ($searchTitle) {
			$query->where('title', 'like', '%' . $searchTitle . '%');
		}

		if ($searchAuthor) {
			$query->whereHas('authors', function ($q) use ($searchAuthor) {
				$q->where('surname', 'like', '%' . $searchAuthor . '%');
			});
		}

		// Сортируем по названию
		if ($sort === 'desc') {
			$query->orderBy('title', 'desc');
		} else {
			$query->orderBy('title', 'asc');
		}

		// Пагинируем результаты
		$books = $query->paginate(2);
		
		// Меняем базовый путь для пагинации
		$books->withPath('/books');

		// Возвращаем ответ
		return response()->json([
			'status' => 'success',
			'message' => 'Книга успешно удалена!',
			'paginationHtml' => view('books.partials.pagination', [
				'books' => $books
			])->render(),
			'booksHtml' => view('books.partials.table', [
				'books' => $books
			])->render(),
		]);
	}
}
