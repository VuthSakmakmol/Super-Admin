@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container mt-4">
    <h1>Activity Log</h1>
    <p>Review all recent activities in the system.</p>

    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Timestamp</th>
                    <th>Performed By</th>
                    <th>Action</th>
                    <th>Target</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                <tr>
                    <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $activity->user->name ?? 'System' }}</td>
                    <td>{{ $activity->action }}</td>
                    <td>
                        {{ $activity->target_type }}
                        @if($activity->target_id)
                            (ID: {{ $activity->target_id }})
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
