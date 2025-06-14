<div lang="ar" dir="rtl">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .subtitle {
            font-size: 16px;
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: left;
        }
        .signature {
            margin-top: 30px;
            text-align: left;
        }
        .department {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>

    <div class="header">
        <div class="title">الموقف اليومي للموظفين</div>
        <div class="subtitle">قسم مدينة الإمام الحسين (ع) للزائرين</div>
        <div class="subtitle">الموقف الخاص بالشعبة الخدمية</div>
    </div>

    <table>
        <tr>
            <td colspan="2">اليوم: {{ $record->day_name }}</td>
            <td colspan="2">التاريخ: {{ $record->hijri_date }} ({{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }})</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2">م</th>
                <th colspan="2">الاجازات الدورية</th>
                <th colspan="2">الاجازات السنوية</th>
                <th colspan="3">الاجازات الزمنية</th>
                <th colspan="2">إجازة الوفاة</th>
                <th colspan="2">إجازة بدون راتب</th>
                <th colspan="2">الغياب</th>
            </tr>
            <tr>
                <th>الاسم</th><th>الرقم</th>
                <th>الاسم</th><th>الرقم</th>
                <th>الاسم</th><th>الرقم</th><th>الوقت</th>
                <th>الاسم</th><th>الرقم</th>
                <th>الاسم</th><th>الرقم</th>
                <th>الاسم</th><th>الرقم</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(
                    count($record->periodic_leaves ?? []),
                    count($record->annual_leaves ?? []),
                    count($record->temporary_leaves ?? []),
                    count($record->bereavement_leaves ?? []),
                    count($record->unpaid_leaves ?? []),
                    count($record->absences ?? [])
                );
            @endphp
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $record->periodic_leaves[$i]['employee_name'] ?? '' }}</td>
                    <td>{{ $record->periodic_leaves[$i]['employee_number'] ?? '' }}</td>
                    <td>{{ $record->annual_leaves[$i]['employee_name'] ?? '' }}</td>
                    <td>{{ $record->annual_leaves[$i]['employee_number'] ?? '' }}</td>
                    <td>{{ $record->temporary_leaves[$i]['employee_name'] ?? '' }}</td>
                    <td>{{ $record->temporary_leaves[$i]['employee_number'] ?? '' }}</td>
                    <td>
                        @if(isset($record->temporary_leaves[$i]))
                            {{ $record->temporary_leaves[$i]['from_time'] ?? '' }} - {{ $record->temporary_leaves[$i]['to_time'] ?? '' }}
                        @endif
                    </td>
                    <td>{{ $record->bereavement_leaves[$i]['employee_name'] ?? '' }}</td>
                    <td>{{ $record->bereavement_leaves[$i]['employee_number'] ?? '' }}</td>
                    <td>{{ $record->unpaid_leaves[$i]['employee_name'] ?? '' }}</td>
                    <td>{{ $record->unpaid_leaves[$i]['employee_number'] ?? '' }}</td>
                    <td>{{ $record->absences[$i]['employee_name'] ?? '' }}</td>
                    <td>{{ $record->absences[$i]['employee_number'] ?? '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <table>
        <tr>
            <th>العدد الكلي</th><th>الحضور الفعلي</th><th>الغياب</th><th>إجازات براتب</th>
            <th>إجازات بدون راتب</th><th>إجازات مرضية</th><th>نقص</th>
            <th>تعيين</th><th>نقل</th><th>فصل</th>
        </tr>
        <tr>
            <td>{{ $record->total_employees }}</td>
            <td>{{ $record->actual_attendance }}</td>
            <td>{{ $record->absences_count ?? 0 }}</td>
            <td>{{ $record->paid_leaves_count ?? 0 }}</td>
            <td>{{ $record->unpaid_leaves_count ?? 0 }}</td>
            <td>{{ $record->sick_leaves_count ?? 0 }}</td>
            <td>0</td><td>0</td><td>0</td><td>0</td>
        </tr>
    </table>

    <div class="footer">
        <div>مسؤول شعبة الخدمية</div>
        <div class="signature">
            <div>التوقيع: ........................</div>
            <div>التاريخ: {{ \Carbon\Carbon::parse($record->date)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            طباعة التقرير
        </button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">
            إغلاق النافذة
        </button>
    </div>

    <script>
        window.onload = function () {
            setTimeout(() => window.print(), 500);
        };
    </script>
</div>
