<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير مصور للمهمة</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            direction: rtl;
            text-align: right;
            background: white;
            color: #000;
            margin: 20px;
        }

        h1 {
            color: #0056b3;
            font-size: 22px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px 12px;
        }

        th {
            background: #f2f2f2;
            width: 25%;
        }

        .images {
            margin-top: 20px;
        }

        .images img {
            max-width: 45%;
            margin: 10px 2.5%;
            border: 1px solid #ccc;
            padding: 4px;
        }

        .signature {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <h1>تقرير مصور - {{ $record->date ? $record->date->format('Y-m-d') : 'بدون تاريخ' }}</h1>

    <table>
        <tr>
            <th>الموقع</th>
            <td>{{ $record->location ?? 'غير متوفر' }}</td>
        </tr>
        <tr>
            <th>الوحدة</th>
            <td>{{ $record->unit_type === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية' }}</td>
        </tr>
        <tr>
            <th>نوع المهمة</th>
            <td>{{ $record->task_type ?? 'غير محدد' }}</td>
        </tr>
        <tr>
            <th>الحالة</th>
            <td>{{ $record->status ?? 'غير محدد' }}</td>
        </tr>
        @if ($record->notes)
        <tr>
            <th>ملاحظات</th>
            <td>{{ $record->notes }}</td>
        </tr>
        @endif
    </table>

    <div class="images">
        <h3>صور قبل التنفيذ:</h3>
        @if (is_array($record->before_images_urls) && count($record->before_images_urls))
            @foreach ($record->before_images_urls as $img)
                @if ($img['exists'])
                    <img src="{{ $img['url'] }}" alt="قبل التنفيذ">
                @endif
            @endforeach
        @else
            <p>لا توجد صور قبل التنفيذ.</p>
        @endif

        <h3>صور بعد التنفيذ:</h3>
        @if (is_array($record->after_images_urls) && count($record->after_images_urls))
            @foreach ($record->after_images_urls as $img)
                @if ($img['exists'])
                    <img src="{{ $img['url'] }}" alt="بعد التنفيذ">
                @endif
            @endforeach
        @else
            <p>لا توجد صور بعد التنفيذ.</p>
        @endif
    </div>

    <div class="signature">
        <div>
            <div class="signature-line"></div>
            <p>توقيع المشرف</p>
        </div>
        <div>
            <div class="signature-line"></div>
            <p>توقيع المدقق النهائي</p>
        </div>
    </div>

</body>
</html>
