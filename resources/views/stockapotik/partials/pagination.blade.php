@if($stocks->hasPages())
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        <p class="text-muted mb-0 small" id="paginationInfo">
            Menampilkan {{ $stocks->firstItem() }} - {{ $stocks->lastItem() }} dari {{ $stocks->total() }} data
        </p>
    </div>
    <div id="paginationLinks">
        {{ $stocks->appends(request()->query())->links() }}
    </div>
</div>
@endif