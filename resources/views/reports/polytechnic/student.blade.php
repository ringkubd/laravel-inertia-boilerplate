@extends('layouts.pdf_layout')
@section('content')
    <div>
        <table class="table">
            <thead>
            <tr>
                <th>SL#</th>
                <th>Name</th>
                <th>Class</th>
                <th>Session</th>
                <th>Mobile</th>
                <th>Trade</th>
                <th>Madrasah</th>
                <th>Polytechnic</th>
            </tr>
            </thead>
            <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $loop->iteration  }}</td>
                    <td>{{ $student->name  }}</td>
                    <td>{{ $student?->classroom[0]?->name }}</td>
                    <td>{{ $student->diploma_session }}</td>
                    <td>{{ $student->mobile  }}</td>
                    <td>{{ $student->polytechnic_trade_id  }}</td>
                    <td>{{ $student?->madrasha?->name }}</td>
                    <td>{{ $student?->polytechnic?->name }}</td>
                </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
@section('header')
    <h4 style="margin: 0px; padding: 0px">{{ config('app.name')  }}</h4>
    <p>Student List</p>
@endsection
@section('style')
    <style>
        table{
            font-size: 10px!important;
        }
    </style>
@endsection
