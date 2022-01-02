@extends('layouts.pdf_layout')
@section('content')
    <div>
        <table class="table">
            <thead>
            <tr>
                <th>Sl</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Joining Date</th>
                <th>Designation</th>
                <th>Trade</th>
                <th>Madrasah</th>
            </tr>
            </thead>
            <tbody>
            @forelse($teachers as $teacher)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $teacher->name }}</td>
                    <td>{{ $teacher?->user->email }}</td>
                    <td>{{ $teacher->mobile }}</td>
                    <td>{{ $teacher->joining_date }}</td>
                    <td>{{ $teacher->designation }}</td>
                    <td>{{ $teacher?->trade?->name }}</td>
                    <td>{{ $teacher?->madrasa?->name }}</td>
                </tr>
            @empty

            @endforelse
            </tbody>
        </table>
    </div>
@endsection
@section('header')
    <h4 class="m-0" style="margin: 0">{{config('app.name')}}</h4>
    <p>Teacher List</p>
@endsection
