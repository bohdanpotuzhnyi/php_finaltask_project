@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @foreach($subjects as $subject)
                    <div class="card">
                        <div class="card-header">{{ __('project.subject'). ' #' .$subject->id }}</div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $subject->name }}</h5>
                            <p class="card-text">{{  mb_strimwidth($subject->description, 0, 100, " ...")  }}</p>
                            <a href="{{ route('subjects.show', $subject) }}" class="btn btn-success">{{  __('project.show') }}</a>
                            <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-warning">{{  __('project.edit') }}</a>
                            <form action="{{ route('subjects.destroy', $subject) }}" method="post" style="display:inline-block;" class="delete-form">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger">{{  __('project.delete') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('subjects.create') }}" class="btn btn-dark">{{ __('project.create') }}</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

