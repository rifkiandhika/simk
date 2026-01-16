@extends('layouts.app')
@section('title', 'Edit Dokter')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dokters.index') }}">Dokter</a>
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
            <h1 class="h3 mb-0 text-gray-600">Edit Dokter</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('dokters.update', $dokter->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('dokters.form')

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
