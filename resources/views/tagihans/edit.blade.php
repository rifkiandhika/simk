@extends('layouts.app')
@section('title', 'Edit Tagihan Pasien')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('tagihans.index') }}">Tagihan Pasien</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Edit</li>
@endsection

@section('page-actions')
    <div class="d-flex flex-row gap-1 day-sorting">
        <button class="btn btn-sm btn-primary">Today</button>
        <button class="btn btn-sm">7d</button>
        <button class="btn btn-sm">2w</button>
        <button class="btn btn-sm">1m</button>
        <button class="btn btn-sm">3m</button>
        <button class="btn btn-sm">6m</button>
        <button class="btn btn-sm">1y</button>
    </div>
@endsection

@section('content')
<section>
    <div class="container-fluid">
        <div class="d-sm-flex mb-3">
            <h1 class="h3 mb-0 text-gray-600">Edit Tagihan Pasien</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('tagihans.update', $tagihan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('tagihans.form')

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let itemIndex = {{ isset($tagihan) ? $tagihan->items->count() : 0 }};

function formatRupiah(angka) {
    var number_string = angka.toString().replace(/[^,\d]/g, ''),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return rupiah;
}

function unformatRupiah(rupiah) {
    return parseInt(rupiah.replace(/\./g, '')) || 0;
}

function addItem() {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    const html = clone.querySelector('.item-row').outerHTML;
    const newHtml = html.replaceAll('INDEX', itemIndex);
    
    document.getElementById('itemsContainer').insertAdjacentHTML('beforeend', newHtml);
    
    attachItemEvents();
    itemIndex++;
    calculateTotal();
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
        calculateTotal();
    } else {
        alert('Minimal harus ada 1 item');
    }
}

function attachItemEvents() {
    document.querySelectorAll('.item-qty, .item-harga').forEach(input => {
        input.removeEventListener('input', calculateSubtotal);
        input.addEventListener('input', calculateSubtotal);
    });
}

function calculateSubtotal(e) {
    const row = e.target.closest('.item-row');
    const qty = parseInt(row.querySelector('.item-qty').value) || 0;
    const hargaInput = row.querySelector('.item-harga');
    const harga = unformatRupiah(hargaInput.value);
    
    hargaInput.value = formatRupiah(harga);
    
    const subtotal = qty * harga;
    row.querySelector('.item-subtotal').value = formatRupiah(subtotal);
    
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    let count = 0;
    
    document.querySelectorAll('.item-subtotal').forEach(input => {
        total += unformatRupiah(input.value);
        count++;
    });
    
    document.getElementById('totalTagihan').textContent = 'Rp ' + formatRupiah(total);
    document.getElementById('itemCount').textContent = count + ' item';
}

document.addEventListener('DOMContentLoaded', function() {
    @if(!isset($tagihan) || $tagihan->items->count() == 0)
        addItem();
    @else
        attachItemEvents();
        calculateTotal();
    @endif
    
    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    }
});

// Form validation on submit
const form = document.querySelector('form');
if (form) {
    form.addEventListener('submit', function(e) {
        const itemCount = document.querySelectorAll('.item-row').length;
        if (itemCount === 0) {
            e.preventDefault();
            alert('Tambahkan minimal 1 item tagihan');
            return false;
        }
        
        // Convert all harga inputs to plain numbers
        document.querySelectorAll('.item-harga').forEach(input => {
            input.value = unformatRupiah(input.value);
        });
    });
}
</script>
@endpush
