<div id="pagination">
    {{ $books->appends(request()->query())->links() }}
</div>
