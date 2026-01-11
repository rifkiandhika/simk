@extends('layouts.app')
@section('title', 'Create Tagihan Pasien')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('tagihans.index') }}">Tagihan Pasien</a>
    </li>
    <li class="breadcrumb-item active text-primary" aria-current="page">Create</li>
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
            <h1 class="h3 mb-0 text-gray-600">Create Tagihan Pasien</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('tagihans.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('tagihans.form')

                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
