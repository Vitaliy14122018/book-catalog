<!-- Модальное окно для редактирования книги -->
<div class="modal fade" id="bookEditModal" tabindex="-1" aria-labelledby="bookEditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookEditModalLabel">Редактировать книгу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="bookEditForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editBookId" name="id">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Название книги</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="editDescription" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editAuthors" class="form-label">Авторы</label>
                        <select class="form-control" id="editAuthors" name="authors[]" multiple required>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}">{{ $author->surname }} {{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editPublishedAt" class="form-label">Дата публикации</label>
                        <input type="date" class="form-control" id="editPublishedAt" name="published_at">
                    </div>
                    <div class="mb-3">
                        <label for="editImage" class="form-label">Изображение книги</label>
                        <input type="file" class="form-control" id="editImage" name="image">
                        <img id="editImagePreview" src="" alt="Preview" class="mt-2" width="100">
                    </div>
                    <button type="submit" class="btn btn-primary">Обновить</button>
                </form>
            </div>
        </div>
    </div>
</div>