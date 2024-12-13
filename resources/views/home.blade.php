@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@if(auth()->user()->hasRole('user'))
                <!-- User Actions -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h4>Hello Konpapa</h4>
                    </div>
                    
                </div>
                @endif
@endsection
