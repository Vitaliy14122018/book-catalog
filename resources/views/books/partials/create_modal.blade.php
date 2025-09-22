<!-- Модальное окно для создания книги -->
<div class="modal fade" id="bookCreateModal" tabindex="-1" aria-labelledby="bookCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookCreateModalLabel">Добавить книгу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bookCreateForm">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Название книги</label>
                        <input type="text" class="form-control" id="title" name="title">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Описание</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="authors" class="form-label">Авторы</label>
                        <select class="form-control" id="authors" name="authors[]" multiple>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}">{{ $author->surname }} {{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="published_at" class="form-label">Дата публикации</label>
                        <input type="date" class="form-control" id="published_at" name="published_at">
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Изображение книги</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        </div>
    </div>
</div>
