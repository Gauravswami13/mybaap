@extends('site.layout')
@section('title', 'Edit profile · MyBaap')
@section('content')
    <article class="page">
        <h1>Edit profile</h1>
        <form method="post" action="{{ url('/account') }}" enctype="multipart/form-data">
            @csrf
            <label class="field">Name
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>
            <label class="field">Photo
                <input type="file" name="photo" accept="image/*">
            </label>
            @error('name')<p class="flash">{{ $message }}</p>@enderror
            <button class="btn" type="submit">Save</button>
        </form>
    </article>
@endsection
