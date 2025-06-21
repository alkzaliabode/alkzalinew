<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المهام المصورة</title>
    <style>
        /* الخطوط سيتم تحميلها بواسطة `php artisan dompdf:install` وتعيينها في كود Filament Page */
        body {
            font-family: 'Noto Sans Arabic', sans-serif; /* <--- استخدام الخط الذي تم تثبيته */
            margin: 0;
            padding: 20px;
            direction: rtl; /* اتجاه النص من اليمين لليسار */
            text-align: right; /* محاذاة النص لليمين */
            font-size: 13px; /* حجم خط مناسب للطباعة */
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 24px;
            color: #333;
            margin: 0;
            padding-top: 10px;
        }
        .header p {
            font-size: 15px;
            color: #666;
            margin: 5px 0 0;
        }
        .section {
            margin-bottom: 25px;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .section h2 {
            font-size: 18px;
            color: #444;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ddd;
        }
        .info-item { /* <--- تغيير من info-grid إلى info-item مع layout أبسط */
            clear: both; /* مسح الفلوتات */
            margin-bottom: 8px;
        }
        .info-item .label {
            font-weight: bold;
            color: #555;
            float: right; /* للعناوين */
            width: 25%; /* عرض العنوان */
            text-align: right;
            padding-left: 10px;
        }
        .info-item .value {
            color: #333;
            overflow: hidden; /* لضمان أن القيمة لا تتداخل مع الـ label */
            text-align: right;
        }
        .images-grid {
            text-align: center;
            margin-top: 15px;
        }
        .images-grid img {
            width: 200px;   /* <--- زيادة حجم الصورة */
            height: 150px;  /* <--- زيادة حجم الصورة */
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            margin: 5px;
            display: inline-block;
        }
        .no-images {
            text-align: center;
            color: #888;
            padding: 10px;
            border: 1px dashed #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            color: #999;
        }
        /* ألوان بسيطة للبادج - لا تستخدم classes Tailwind المعقدة هنا */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            color: #fff;
            text-align: center;
            white-space: nowrap;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #333;}
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-primary { background-color: #007bff; }
        .badge-gray { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير شامل للعمليات المصورة</h1>
        <p>تاريخ التقرير: {{ now()->format('Y-m-d') }}</p>
    </div>

    @foreach($records as $record)
        <div class="section">
            <h2>تفاصيل المهمة: {{ $record->location ?? 'غير محدد' }}</h2>

            {{-- تغيير هيكل info-grid إلى عناصر info-item بسيطة --}}
            <div class="info-item">
                <span class="label">التاريخ:</span>
                <span class="value">{{ $record->date ? $record->date->format('Y-m-d') : 'غير متوفر' }}</span>
            </div>
            <div class="info-item">
                <span class="label">الموقع:</span>
                <span class="value">{{ $record->location ?? 'غير متوفر' }}</span>
            </div>
            <div class="info-item">
                <span class="label">الوحدة:</span>
                <span class="value">
                    @php
                        $unitName = $record->unit_type === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية';
                        $unitClass = $record->unit_type === 'cleaning' ? 'badge-success' : 'badge-primary';
                    @endphp
                    <span class="badge {{ $unitClass }}">{{ $unitName }}</span>
                </span>
            </div>
            <div class="info-item">
                <span class="label">نوع المهمة:</span>
                <span class="value">
                    @php
                        $taskClass = $record->task_type == 'إدامة' ? 'badge-info' : 'badge-warning';
                    @endphp
                    <span class="badge {{ $taskClass }}">{{ $record->task_type ?? 'غير متوفر' }}</span>
                </span>
            </div>
            <div class="info-item">
                <span class="label">الحالة:</span>
                <span class="value">
                    @php
                        $statusClass = '';
                        if ($record->status == 'مكتمل') $statusClass = 'badge-success';
                        elseif ($record->status == 'قيد التنفيذ') $statusClass = 'badge-warning';
                        elseif ($record->status == 'ملغى') $statusClass = 'badge-danger';
                        else $statusClass = 'badge-gray';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $record->status ?? 'غير متوفر' }}</span>
                </span>
            </div>
            @if($record->notes)
            <div class="info-item">
                <span class="label">الملاحظات:</span>
                <span class="value">{{ $record->notes }}</span>
            </div>
            @endif


            <h3 style="margin-top: 25px;">صور قبل التنفيذ:</h3>
            @if(is_array($record->before_images_urls) && count($record->before_images_urls) > 0)
                <div class="images-grid">
                    @foreach($record->before_images_urls as $imageData)
                        <img src="{{ $imageData['absolute_path'] }}" alt="قبل التنفيذ">
                    @endforeach
                </div>
            @else
                <p class="no-images">لا توجد صور قبل التنفيذ.</p>
            @endif

            <h3 style="margin-top: 25px;">صور بعد التنفيذ:</h3>
            @if(is_array($record->after_images_urls) && count($record->after_images_urls) > 0)
                <div class="images-grid">
                    @foreach($record->after_images_urls as $imageData)
                        <img src="{{ $imageData['absolute_path'] }}" alt="بعد التنفيذ">
                    @endforeach
                </div>
            @else
                <p class="no-images">لا توجد صور بعد التنفيذ.</p>
            @endif
        </div>
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
        <p>تم إنشاء هذا التقرير بتاريخ: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
