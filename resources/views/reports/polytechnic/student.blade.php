@extends('layouts.pdf_layout')

@section('header')
<div class="header-section">
    <h3 style="padding: 0; color: #000;">{{ config('app.name') }}</h3>
    <h4 style="margin: 5px 0; color: #000;">Polytechnic Student List</h4>
    <hr style="border: 1px solid #000; margin: 10px 0;">
</div>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="filter-info" style="margin: 5px 0; font-size: 13px; color: #000;">
        @if(isset($filterInfo))
            <p><strong>Filters:</strong> {{ $filterInfo }}</p>
        @endif
        <p><strong>Total Students:</strong> {{ $students->count() }} | <strong>Report Generated:</strong> {{ date('d M Y H:i') }}</p>
    </div>

    @php
        $continuingCount = 0;
        $dropoutCount = 0;
        $completedCount = 0;

        foreach($students as $student) {
            if($student->results->contains('status', 'Dropout')) {
                $dropoutCount++;
            } elseif($student->polytechnic_completed == 1 || ($student->results->max('semester') == 8 && $student->results->where('semester', 8)->contains('status', 'Passed'))) {
                $completedCount++;
            } else {
                $continuingCount++;
            }
        }
    @endphp

    <!-- Status-wise Summary Section -->
    <div style="margin-bottom: 20px; border: 1px solid #000; padding: 10px; background-color: #f9f9f9;">
        <h4 style="margin: 0 0 10px 0; color: #000; text-decoration: underline;">Student Status Summary</h4>
        <table style="width: 60%; border-collapse: collapse; margin: 0 auto;">
            <tr style="background-color: #e0e0e0;">
                <th style="padding: 6px; border: 1px solid #000; text-align: center;">Status</th>
                <th style="padding: 6px; border: 1px solid #000; text-align: center;">Count</th>
                <th style="padding: 6px; border: 1px solid #000; text-align: center;">Percentage</th>
            </tr>
            <tr>
                <td style="padding: 6px; border: 1px solid #000;">Continuing</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">{{ $continuingCount }}</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">
                    {{ $students->count() > 0 ? round(($continuingCount / $students->count()) * 100, 1) : 0 }}%
                </td>
            </tr>
            <tr>
                <td style="padding: 6px; border: 1px solid #000;">Dropped out</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">{{ $dropoutCount }}</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">
                    {{ $students->count() > 0 ? round(($dropoutCount / $students->count()) * 100, 1) : 0 }}%
                </td>
            </tr>
            <tr>
                <td style="padding: 6px; border: 1px solid #000;">Completed</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">{{ $completedCount }}</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">
                    {{ $students->count() > 0 ? round(($completedCount / $students->count()) * 100, 1) : 0 }}%
                </td>
            </tr>
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td style="padding: 6px; border: 1px solid #000;">Total</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">{{ $students->count() }}</td>
                <td style="padding: 6px; border: 1px solid #000; text-align: center;">100%</td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; border-collapse: collapse; page-break-inside: auto;">
        <thead style="display: table-header-group;">
            <tr style="background-color: #e0e0e0; color: #000;">
                <th style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">SL#</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Name</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Father's Name</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Diploma Roll #</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Session</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Mobile</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Trade Course</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: left; font-weight: bold;">Institute</th>
                <th style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: bold;">Semester</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
            <tr style="{{ $loop->even ? 'background-color: #f5f5f5;' : 'background-color: #fff;' }}">
                <td style="padding: 8px; border: 1px solid #000; text-align: center">{{ $loop->iteration }}</td>
                <td style="padding: 8px; border: 1px solid #000; font-weight: 500">{{ $student->name }}</td>
                <td style="padding: 8px; border: 1px solid #000;">{{ $student->father_name }}</td>
                <td style="padding: 8px; border: 1px solid #000;">{{ $student->polytechnic_roll }}</td>
                <td style="padding: 8px; border: 1px solid #000;">{{ $student->polytechnic_session ?? 'N/A' }}</td>
                <td style="padding: 8px; border: 1px solid #000;">{{ $student->mobile ?: 'N/A' }}</td>
                <td style="padding: 8px; border: 1px solid #000;">{{ $student->polytechnic_trade_id }}</td>
                <td style="padding: 8px; border: 1px solid #000;">{{ $student->polytechnic ? $student->polytechnic->name : "Not assigned" }}</td>
                <td style="padding: 8px; border: 1px solid #000; text-align: center; font-weight: 600;">
                    @if($student->results->contains('status', 'Dropout'))
                        Dropped out in Semester {{ $student->results->firstWhere('status', 'Dropout')->semester }}
                    @elseif($student->polytechnic_completed == 1 || ($student->results->max('semester') == 8 && $student->results->where('semester', 8)->contains('status', 'Passed')))
                        Completed
                    @else
                        {{ $student->results->max('semester') + 1 }}
                    @endif
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding: 20px; text-align: center; border: 1px solid #000;">No students available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary" style="margin-top: 20px; font-size: 14px; color: #000;">
        <p><strong>Note:</strong> This report includes all active polytechnic students with their current semester status.</p>
    </div>
</div>
@endsection

@section('footer')
<div class="footer" style="margin-top: 20px; font-size: 12px; text-align: center; color: #000; position: fixed; bottom: 0; width: 100%;">
    <p>Generated on {{ date('d M Y') }} at {{ date('h:i A') }} | Page @{{ $PAGE_NUM }} of @{{ $PAGE_COUNT }}</p>
</div>
@endsection
