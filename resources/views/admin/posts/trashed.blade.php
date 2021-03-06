@extends('layouts.app')

@section('content')
    <div class="card text-white bg-dark my-3">
        <div class="card-body">
        <table class="table table-striped table-dark">
            <thead>
                <th width="30%">
                    Image
                </th>
                <th width="40%">
                    Title
                </th>
                <th>
                    Edit
                </th>
                <th>
                    Restore
                </th>
                <th>
                    Destroy
                </th>
            </thead>
            <tbody>
              @if($posts->count() > 0)
                @foreach($posts as $post)
                    <tr>
                        <td>
                        <img src="{{$post->featured}}" alt="{{$post->title}}" width="50px">
                        </td>
                        <td>
                        {{$post->title}}
                        </td>
                        <td>
                            <a href="{{route('post.edit', ['id' => $post->id])}}" class="btn btn-sm btn-info">Edit</a>
                        </td>
                        <td>
                            <a href="{{route('post.restore', ['id' => $post->id])}}" class="btn btn-sm btn-success">Restore</a>
                        </td>
                        <td>
                            <a href="{{route('post.kill', ['id' => $post->id])}}" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                @endforeach
                @else
                <tr>
                    <th colspan="5" class="text-center">No trashed posts</th>
                </tr>
              @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
