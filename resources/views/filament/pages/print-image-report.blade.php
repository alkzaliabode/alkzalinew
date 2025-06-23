<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير صور المهمة - {{ $record->date ? $record->date->format('Y-m-d') : 'بدون تاريخ' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm;
        }

        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 11pt;
            color: #333;
            margin: 0;
            padding: 0;
            background: #fff;
            page-break-inside: avoid;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            page-break-inside: avoid;
        }

        .header, .section, .signature, .images-grid, table.details {
            page-break-inside: avoid;
        }

        .header h1 {
            text-align: center;
            color: #0056b3;
        }

        table.details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.details th,
        table.details td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        .badge {
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10pt;
            color: white;
            display: inline-block;
        }
        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #000; }
        .badge-danger { background: #dc3545; }
        .badge-primary { background: #007bff; }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 10px;
            justify-items: center;
            margin-top: 15px;
        }

        .images-grid img {
            width: 100%;
            max-width: 300px;
            height: auto;
            border: 2px solid #bbb;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature div {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .print-controls {
            text-align: center;
            margin-top: 30px;
        }

        @media print {
            .print-controls {
                display: none !important;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="print-controls">
    <button onclick="window.print()" style="
        padding: 10px 20px;
        font-size: 16px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    ">🖨️ طباعة التقرير</button>

    <button onclick="window.close()" style="
        padding: 10px 20px;
        font-size: 16px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    ">❌ إغلاق</button>
</div>

<div class="container">
    <div class="header">
        <h1>التقرير المصور للمهمة</h1>
    </div>

    <div class="section">
        <table class="details">
            <tr><th>التاريخ</th><td>{{ $record->date ? $record->date->format('Y-m-d') : 'غير محدد' }}</td></tr>
            <tr><th>الموقع</th><td>{{ $record->location ?? 'غير متوفر' }}</td></tr>
            <tr><th>الوحدة</th><td><span class="badge {{ $record->unit_type === 'cleaning' ? 'badge-success' : 'badge-primary' }}">
                {{ $record->unit_type === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية' }}</span></td></tr>
            <tr><th>نوع المهمة</th><td><span class="badge {{ $record->task_type == 'إدامة' ? 'badge-primary' : 'badge-warning' }}">{{ $record->task_type }}</span></td></tr>
            <tr><th>الحالة</th><td><span class="badge
                @if($record->status == 'مكتمل') badge-success
                @elseif($record->status == 'قيد التنفيذ') badge-warning
                @else badge-danger
                @endif
            ">{{ $record->status }}</span></td></tr>
        </table>
    </div>

    <div class="section">
        <h3>صور قبل التنفيذ:</h3>
        <div class="images-grid">
            @foreach($record->before_images_urls as $img)
                @if($img['exists'])
                    <img src="{{ $img['url'] }}" alt="صورة قبل التنفيذ">
                @endif
            @endforeach
        </div>
    </div>

    <div class="section">
        <h3>صور بعد التنفيذ:</h3>
        <div class="images-grid">
            @foreach($record->after_images_urls as $img)
                @if($img['exists'])
                    <img src="{{ $img['url'] }}" alt="صورة بعد التنفيذ">
                @endif
            @endforeach
        </div>
    </div>

    <div class="signature">
        <div>توقيع مسؤول الشعبة</div>
        <div>توقيع مدير القسم</div>
    </div>
</div>

</body>
</html>
