<div id="pagination">
     {{ $authors->appends(request()->query())->links() }}
</div>
