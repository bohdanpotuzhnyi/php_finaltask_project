@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('project.subject'). ' ' .$subject->id }}</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $subject->name }}</h5>
                        <p class="card-text">{{ $subject->description }}</p>
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
