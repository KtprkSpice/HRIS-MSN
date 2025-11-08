@extends('layout.dashboard')
@section('header', 'Tambah Tugas')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="row g-3" action="{{ route('task.update', $task->id) }}" method="post">
        @csrf
        <div class="col-md-6">
            <label for="name" class="form-label">Nama Tugas</label>
            <input type="text" class="form-control @error('name')
                is-invalid
            @enderror"
                id="name" name="name" required value="{{ old('name', $task->name) }}">
            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="start_time" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control @error('start_time')
                is-invalid
            @enderror"
                id="start_time" name="start_time" required value="{{ old('start_time', $task->start_time) }}">
            @error('start_time')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-md-6">
            <label for="end_time" class="form-label">Tanggal Selesai</label>
            <input type="date" class="form-control @error('end_time')
                is-invalid
            @enderror"
                id="end_time" name="end_time" required value="{{ old('end_time', $task->end_time) }}">
            @error('end_time')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <label for="description" class="form-label">Deskripsi</label>
            <input type="text" class="form-control @error('description')
                is-invalid
            @enderror"
                id="description" placeholder="" name="description" required value="{{ old('description', $task->description) }}">
            @error('description')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
@endsection
