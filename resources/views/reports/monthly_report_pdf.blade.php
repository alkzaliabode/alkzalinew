<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التقرير الشهري</title>
    {{-- CSS styles will be injected by the Filament Page for proper Arabic rendering --}}
</head>
<body>
    <div class="header">
        <h1>التقرير الشهري المفصل</h1>
        <p>الفترة من: {{ $startDate->format('Y-m-d') }} إلى: {{ $endDate->format('Y-m-d') }}</p>
        <p>تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>

    @if ($reports->isEmpty())
        <p style="text-align: center; margin-top: 50px; font-size: 16px;">لا توجد تقارير متاحة لهذه الفترة.</p>
    @else
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">رقم التقرير</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">نوع الوحدة</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">نوع المهمة</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">التاريخ</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">الموقع</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">الحالة</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">الملاحظات</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">صور ما قبل</th>
                    <th style="background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px; text-align: right;">صور ما بعد</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->id }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->unit_type }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->task_type }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->date->format('Y-m-d') }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->location }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->status }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">{{ $report->notes }}</td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: center;">
                            @if (!empty($report->before_images_urls))
                                @foreach ($report->before_images_urls as $image)
                                    @if ($image['exists'])
                                        <img src="{{ $image['absolute_path'] }}" style="max-width: 50px; height: auto; display: inline-block; margin: 2px;" alt="صورة قبل">
                                    @else
                                        <span style="font-size: 10px; color: #888;">غير متوفرة</span>
                                    @endif
                                @endforeach
                            @else
                                <span style="font-size: 10px; color: #888;">لا توجد صور</span>
                            @endif
                        </td>
                        <td style="border: 1px solid #ccc; padding: 8px; text-align: center;">
                            @if (!empty($report->after_images_urls))
                                @foreach ($report->after_images_urls as $image)
                                    @if ($image['exists'])
                                        <img src="{{ $image['absolute_path'] }}" style="max-width: 50px; height: auto; display: inline-block; margin: 2px;" alt="صورة بعد">
                                    @else
                                        <span style="font-size: 10px; color: #888;">غير متوفرة</span>
                                    @endif
                                @endforeach
                            @else
                                <span style="font-size: 10px; color: #888;">لا توجد صور</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer" style="text-align: center; margin-top: 50px; font-size: 12px; color: #999;">
        <p>هذا التقرير تم توليده تلقائياً بواسطة نظام {{ config('app.name') }}.</p>
        <p>تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
