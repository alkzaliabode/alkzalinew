<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
<title>تقرير صور المهمة - {{ $record->date ? $record->date->format('Y-m-d') : 'بدون تاريخ' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0056b3;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #0056b3;
            font-size: 22pt;
            margin: 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .info-item span:first-child {
            font-weight: bold;
        }
        .section-title {
            font-size: 16pt;
            color: #0056b3;
            margin-top: 30px;
            margin-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }
        .images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .images img {
            width: 180px;
            height: 130px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .no-images {
            color: #777;
            font-style: italic;
            margin-bottom: 15px;
        }
        .notes {
            background: #eef7ff;
            padding: 10px;
            border-left: 4px solid #007bff;
            margin-top: 15px;
            border-radius: 5px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            width: 40%;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #000;
        }
        .footer {
            text-align: center;
            font-size: 10pt;
            color: #666;
            margin-top: 50px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>التقرير المصور للمهمة</h1>
    </div>

    <div class="info">
<div class="info-item">
    <span>📅 التاريخ:</span>
    <span>{{ $record->date ? $record->date->format('Y-m-d') : 'غير محدد' }}</span>
</div>
        <div class="info-item"><span>📍 الموقع:</span> <span>{{ $record->location }}</span></div>
        <div class="info-item"><span>🧩 نوع المهمة:</span> <span>{{ $record->task_type }}</span></div>
        <div class="info-item"><span>🗂️ الوحدة:</span> <span>{{ $record->unit_type === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية' }}</span></div>
        <div class="info-item"><span>📌 الحالة:</span> <span>{{ $record->status }}</span></div>
    </div>

    @if($record->notes)
        <div class="notes">
            <strong>ملاحظات:</strong>
            <p>{{ $record->notes }}</p>
        </div>
    @endif

    <div class="section-title">📸 صور قبل التنفيذ</div>
    @if(is_array($record->before_images_for_table) && count($record->before_images_for_table) > 0)
        <div class="images">
            @foreach($record->before_images_for_table as $url)
                <img src="{{ $url }}" alt="صورة قبل التنفيذ">
            @endforeach
        </div>
    @else
        <p class="no-images">لا توجد صور قبل التنفيذ.</p>
    @endif

    <div class="section-title">📸 صور بعد التنفيذ</div>
    @if(is_array($record->after_images_for_table) && count($record->after_images_for_table) > 0)
        <div class="images">
            @foreach($record->after_images_for_table as $url)
                <img src="{{ $url }}" alt="صورة بعد التنفيذ">
            @endforeach
        </div>
    @else
        <p class="no-images">لا توجد صور بعد التنفيذ.</p>
    @endif

    <div class="signature">
        <div>
            <div class="signature-line"></div>
            <p>مسؤول الشعبة</p>
        </div>
        <div>
            <div class="signature-line"></div>
            <p>مدير القسم</p>
        </div>
    </div>

    <div class="footer">
        <p>تم إنشاء التقرير بتاريخ: {{ now()->format('Y-m-d H:i') }}</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'نظام التقارير') }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #28a745; color: white; border: none; border-radius: 4px;">🖨️ طباعة التقرير</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #dc3545; color: white; border: none; border-radius: 4px; margin-right: 10px;">❌ إغلاق</button>
    </div>
</div>

</body>
</html>
