@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <form action="{{ route('article.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="image" id=""><br>
                        <input type="text" name="title" id="title" value="title">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
