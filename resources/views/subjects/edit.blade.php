@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('project.subject'). ' ' .$subject->id }}</div>
                    <div class="card-body">
                        <form method="post" class="item-form" action="{{ route('subjects.update', $subject) }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <h5 class="card-title">
                                <input class="text" name="name" id="name" type="text" value="{{ $subject->name ?? ''}}">
                            </h5>
                            <p class="card-text">
                                <textarea name="description" id="description" cols="80" rows="10" class="text">{{ $subject->description ?? ''}}</textarea>
                            </p>
                            <button type="submit" class="btn btn-success mt-4" name="update" value="Update">{{ __('project.update') }}</button>
                        </form>
                        <p class="card-text"><i>Created: {{ $subject->created_at }}
                                @if($subject->updated_at->ne($subject->created_at))
                                    <br>Modified: {{ $subject->updated_at }}
                                @endif
                            </i></p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('subjects.index') }}" class="btn btn-info">{{  __('project.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
