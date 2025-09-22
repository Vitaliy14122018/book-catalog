<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Получаем параметры сортировки и фильтрации из запроса
        $searchSurname = $request->get('search_surname');
        $searchName = $request->get('search_name');
        $sortOrder = $request->get('sort', 'asc'); // Значение по умолчанию 'asc'

        // Создаем базовый запрос
        $query = Author::orderBy('surname', $sortOrder);

        // Если фильтр по фамилии передан, добавляем условие
        if ($searchSurname) {
            $query->where('surname', 'like', '%' . $searchSurname . '%');
        }

        // Если фильтр по имени передан, добавляем условие
        if ($searchName) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        // Получаем авторов с пагинацией
        $authors = $query->paginate(2);

        // Передаем данные в представление
        return view('authors.index', compact('authors'));
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
        // Валидация данных
        $validated = $request->validate([
            'surname' => 'required|string|min:3',
            'name' => 'required|string',
            'patronymic' => 'nullable|string',
        ]);

        // Создаем нового автора
        $author = Author::create($validated);

        // Получаем параметры фильтров и сортировки
        $searchSurname = $request->input('search_surname');
        $searchName = $request->input('search_name');
        $sortOrder = $request->input('sort', 'asc');

        // Создаем запрос для получения списка авторов с фильтрацией и сортировкой
        $query = Author::orderBy('surname', $sortOrder);

        // Применяем фильтр по фамилии
        if ($searchSurname) {
            $query->where('surname', 'like', '%' . $searchSurname . '%');
        }

        // Применяем фильтр по имени
        if ($searchName) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        // Получаем авторов с пагинацией
        $authors = $query->paginate(2);
		
		// Меняем базовый путь для пагинации
		$authors->withPath('/authors');

        // Возвращаем обновленный список авторов и пагинацию
        return response()->json([
            'status' => 'success',
            'message' => 'Автор успешно добавлен!',
            'authorsHtml' => view('authors.partials.table', compact('authors'))->render(), // HTML таблицы авторов
            'paginationHtml' => view('authors.partials.pagination', compact('authors'))->render(), // HTML пагинации
        ]);
    }
	
	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function show(Author $author)
	{
		
		return view('authors.partials.show', compact('author', 'books'));
	}
	
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function edit(Author $author)
	{
		// Получаем данные о авторе
		return response()->json([
			'status' => 'success',
			'author' => $author,  // Передаем данные автора
		]);
	}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Author $author)
    {
        // Валидация данных
        $validated = $request->validate([
            'surname' => 'required|string|min:3|max:255',
            'name' => 'required|string|max:255',
            'patronymic' => 'nullable|string|max:255',
        ]);

        // Обновление данных автора
        $author->update($validated);

        // Получаем параметры фильтров и сортировки
        $searchSurname = $request->input('search_surname');
        $searchName = $request->input('search_name');
        $sortOrder = $request->input('sort', 'asc');

        // Создаем запрос для получения списка авторов с фильтрацией и сортировкой
        $query = Author::orderBy('surname', $sortOrder);

        // Применяем фильтры по фамилии и имени
        if ($searchSurname) {
            $query->where('surname', 'like', '%' . $searchSurname . '%');
        }

        if ($searchName) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        // Получаем авторов с пагинацией
        $authors = $query->paginate(2);
		
		// Меняем базовый путь для пагинации
		$authors->withPath('/authors');

        // Возвращаем обновленный список авторов и пагинацию
        return response()->json([
            'status' => 'success',
            'message' => 'Автор успешно обновлен',
            'authorsHtml' => view('authors.partials.table', compact('authors'))->render(),
            'paginationHtml' => view('authors.partials.pagination', compact('authors'))->render(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Author $author
     * @return \Illuminate\Http\Response
     */
    public function destroy(Author $author, Request $request)
    {
        // Удаляем автора
        $author->delete();

        // Получаем параметры фильтров и сортировки
        $searchSurname = $request->input('search_surname');
        $searchName = $request->input('search_name');
        $sortOrder = $request->input('sort', 'asc');

        // Создаем запрос для получения списка авторов с фильтрацией и сортировкой
        $query = Author::orderBy('surname', $sortOrder);

        // Применяем фильтры
        if ($searchSurname) {
            $query->where('surname', 'like', '%' . $searchSurname . '%');
        }

        if ($searchName) {
            $query->where('name', 'like', '%' . $searchName . '%');
        }

        // Получаем авторов с пагинацией
        $authors = $query->paginate(2);
		
		// Меняем базовый путь для пагинации
		$authors->withPath('/authors');

        // Возвращаем обновленный список авторов и пагинацию
        return response()->json([
            'status' => 'success',
            'message' => 'Автор удален',
            'authorsHtml' => view('authors.partials.table', compact('authors'))->render(),
            'paginationHtml' => view('authors.partials.pagination', compact('authors'))->render(),
        ]);
    }
}
