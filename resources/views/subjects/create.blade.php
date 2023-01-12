@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('project.create') }}</div>
                    <div class="card-body">
                        <form method="post" class="item-form" action="{{ route('subjects.store') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <h5 class="card-title">
                                <input class="text" name="name" id="name" type="text" value="{{ $subject->name ?? ''}}">
                            </h5>
                            <p class="card-text">
                                <textarea name="description" id="description" cols="80" rows="10" class="text">{{ $subject->description ?? ''}}</textarea>
                            </p>
                            <button type="submit" class="btn btn-success mt-4" name="save" value="Save">{{ __('project.save') }}</button>
                        </form>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('subjects.index') }}" class="btn btn-info">{{  __('project.back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
