<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير صور المهمة - {{ $record->date ? $record->date->format('Y-m-d') : 'بدون تاريخ' }}</title>
    <style>
        /* تعريف الخطوط العربية - تأكد من وجود ملفات .ttf في public/fonts */
        /* هذه الخطوط يجب أن تكون موجودة في مسار public/fonts ليتم تحميلها بواسطة Dompdf */
        @font-face {
            font-family: 'Noto Sans Arabic';
            src: url('{{ public_path('fonts/NotoSansArabic-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Noto Sans Arabic';
            src: url('{{ public_path('fonts/NotoSansArabic-Bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        /* إعدادات الصفحة للطباعة */
        @page {
            size: A4 portrait; /* حجم الصفحة واتجاهها */
            margin: 15mm; /* هوامش الصفحة من جميع الجوانب */
        }

        body {
            font-family: 'Noto Sans Arabic', sans-serif; /* استخدام الخط العربي المخصص */
            line-height: 1.6;
            color: #333;
            background: white;
            margin: 0;
            padding: 0;
            direction: rtl; /* اتجاه النص من اليمين لليسار */
            text-align: right; /* محاذاة النص لليمين */
            font-size: 11pt; /* حجم خط أساسي أكبر للطباعة */
        }

        .container {
            width: 100%;
            max-width: 750px; /* تقييد العرض ليكون مناسبًا لورقة A4 */
            margin: 0 auto; /* توسيط المحتوى */
            padding: 15px 0; /* بادينغ علوي وسفلي */
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #0056b3; /* خط فاصل بلون احترافي */
            margin-bottom: 30px;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #0056b3; /* لون عنوان احترافي */
            font-size: 24pt; /* حجم خط أكبر للعنوان الرئيسي */
            margin: 0;
            padding-top: 5px;
        }
        .header p {
            font-size: 11pt;
            color: #666;
            margin: 5px 0 0;
        }

        /* قسم المعلومات الرئيسية */
        .info-section {
            margin-bottom: 25px;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            background-color: #fcfcfc; /* خلفية فاتحة جدًا */
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* ظل خفيف */
        }
        .info-section h2 {
            font-size: 18pt;
            color: #0056b3;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ddd;
        }

        /* جدول المعلومات التفصيلية */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th,
        .details-table td {
            padding: 8px 12px;
            border: 1px solid #eee; /* حدود بسيطة للجدول */
            vertical-align: top;
            text-align: right;
        }
        .details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #555;
            width: 25%; /* تحديد عرض عمود التسمية */
        }
        .details-table td {
            color: #333;
        }

        /* أنماط البادج */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 10pt;
            font-weight: bold;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            margin-top: 2px; /* تباعد بسيط عن النص */
        }
        .badge-success { background-color: #28a745; } /* أخضر */
        .badge-warning { background-color: #ffc107; color: #333;} /* أصفر مع نص داكن */
        .badge-danger { background-color: #dc3545; } /* أحمر */
        .badge-info { background-color: #17a2b8; } /* أزرق سماوي */
        .badge-primary { background-color: #007bff; } /* أزرق داكن */
        .badge-gray { background-color: #6c757d; } /* رمادي */

        /* أنماط الملاحظات */
        .notes {
            background: #eef7ff;
            padding: 12px;
            border-right: 4px solid #007bff; /* خط أزرق على اليمين */
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #333;
        }
        .notes strong {
            display: block;
            margin-bottom: 5px;
            color: #0056b3;
            font-size: 11pt;
        }

        /* قسم الصور */
        .images-section {
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .images-section h3 {
            font-size: 16pt;
            color: #0056b3;
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #ccc;
        }
        .image-row {
            width: 100%;
            display: block; /* لضمان أن كل صف يبدأ على سطر جديد */
            margin-bottom: 15px;
            clear: both; /* لتجنب مشاكل الفلوت */
        }
        .image-col {
            width: 48%; /* لكل عمود، مع 4% تباعد */
            display: inline-block;
            vertical-align: top;
            box-sizing: border-box;
            padding: 0 1%; /* تباعد داخلي */
        }
        .image-col:first-child {
            float: right; /* أول عمود على اليمين */
        }
        .image-col:last-child {
            float: left; /* ثاني عمود على اليسار */
        }
        .image-wrapper {
            text-align: center;
            margin-bottom: 15px; /* تباعد بين الصور */
        }
        .image-wrapper img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: block; /* لجعل الصورة تأخذ عرضها بالكامل وتتوسط */
            margin: 0 auto;
        }
        .no-images {
            text-align: center;
            color: #888;
            padding: 15px;
            border: 1px dashed #ddd;
            border-radius: 5px;
            background-color: #f5f5f5;
            margin: 0 1%; /* تباعد بسيط من الجوانب */
        }

        /* قسم التوقيع */
        .signature-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
            clear: both; /* مهم لمسح الفلوتات قبل التوقيع */
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        .signature-line {
            margin-top: 60px; /* مساحة للتوقيع */
            border-bottom: 1px solid #000;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-title {
            margin-top: 8px;
            font-size: 11pt;
            font-weight: bold;
            color: #555;
        }

        /* التذييل */
        .footer {
            text-align: center;
            font-size: 9pt;
            color: #777;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        /* لمنع عرض زر الطباعة في ملف PDF نفسه */
        .no-print {
            display: none !important;
        }

        /* لضمان طباعة الألوان والخلفيات */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>التقرير المصور للمهمة</h1>
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    {{-- هنا نفترض أننا نمرر سجلًا واحدًا $record --}}
    <div class="info-section">
        <h2>معلومات المهمة الأساسية</h2>
        <table class="details-table">
            <tr>
                <th>التاريخ:</th>
                <td>{{ $record->date ? $record->date->format('Y-m-d') : 'غير محدد' }}</td>
            </tr>
            <tr>
                <th>الموقع:</th>
                <td>{{ $record->location ?? 'غير متوفر' }}</td>
            </tr>
            <tr>
                <th>الوحدة:</th>
                <td>
                    @php
                        $unitName = $record->unit_type === 'cleaning' ? 'النظافة العامة' : 'المنشآت الصحية';
                        $unitClass = $record->unit_type === 'cleaning' ? 'badge-success' : 'badge-primary';
                    @endphp
                    <span class="badge {{ $unitClass }}">{{ $unitName }}</span>
                </td>
            </tr>
            <tr>
                <th>نوع المهمة:</th>
                <td>
                    @php
                        $taskClass = $record->task_type == 'إدامة' ? 'badge-info' : 'badge-warning';
                    @endphp
                    <span class="badge {{ $taskClass }}">{{ $record->task_type ?? 'غير متوفر' }}</span>
                </td>
            </tr>
            <tr>
                <th>الحالة:</th>
                <td>
                    @php
                        $statusClass = '';
                        if ($record->status == 'مكتمل') $statusClass = 'badge-success';
                        elseif ($record->status == 'قيد التنفيذ') $statusClass = 'badge-warning';
                        elseif ($record->status == 'ملغى') $statusClass = 'badge-danger';
                        else $statusClass = 'badge-gray';
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $record->status ?? 'غير متوفر' }}</span>
                </td>
            </tr>
            @if($record->working_hours)
            <tr>
                <th>ساعات العمل:</th>
                <td>{{ $record->working_hours }} ساعة</td>
            </tr>
            @endif
            @if($record->relatedGoal)
            <tr>
                <th>الهدف المرتبط:</th>
                <td>{{ $record->relatedGoal->goal_text ?? 'غير متوفر' }}</td>
            </tr>
            @endif
            {{-- عرض الموارد المستخدمة بشكل منسق --}}
            @if(is_array($record->resources_used) && count($record->resources_used) > 0)
            <tr>
                <th>الموارد المستخدمة:</th>
                <td>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach($record->resources_used as $resource)
                            <li style="margin-bottom: 3px;">{{ $resource['name'] ?? '' }} ({{ $resource['quantity'] ?? '' }} {{ $resource['unit'] ?? '' }})</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            @endif
            {{-- عرض تفاصيل التنفيذ بناءً على الموقع (نفس منطقك السابق) --}}
            @if (str_contains($record->location, 'قاعة'))
            <tr>
                <th>تفاصيل القاعة:</th>
                <td>
                    المنادر المدامة: {{ $record->mats_count ?? 0 }}<br>
                    الوسادات المدامة: {{ $record->pillows_count ?? 0 }}<br>
                    المراوح المدامة: {{ $record->fans_count ?? 0 }}<br>
                    النوافذ المدامة: {{ $record->windows_count ?? 0 }}<br>
                    السجاد المدام: {{ $record->carpets_count ?? 0 }}<br>
                    البطانيات المدامة: {{ $record->blankets_count ?? 0 }}<br>
                    الأسرة: {{ $record->beds_count ?? 0 }}<br>
                    المستفيدون: {{ $record->beneficiaries_count ?? 0 }}
                </td>
            </tr>
            @elseif ($record->location === 'الترامز')
            <tr>
                <th>تفاصيل الترامز:</th>
                <td>
                    الترامز المملوئة والمدامة: {{ $record->filled_trams_count ?? 0 }}
                </td>
            </tr>
            @elseif ($record->location === 'السجاد')
            <tr>
                <th>تفاصيل السجاد:</th>
                <td>
                    الترامز المملوئة والمدامة: {{ $record->filled_trams_count ?? 0 }}
                </td>
            </tr>
            @elseif ($record->location === 'الحاويات')
            <tr>
                <th>تفاصيل الحاويات:</th>
                <td>
                    الحاويات الكبيرة المفرغة والمدامة: {{ $record->large_containers_count ?? 0 }}<br>
                    الحاويات الصغيرة المفرغة والمدامة: {{ $record->small_containers_count ?? 0 }}
                </td>
            </tr>
            @elseif ($record->location === 'الجامع' || $record->location === 'المركز الصحي')
            <tr>
                <th>تفاصيل الإدامة:</th>
                <td>{{ $record->maintenance_details ?? 'لا توجد تفاصيل' }}</td>
            </tr>
            @endif
             {{-- عرض الموظفين المنفذين وتقييمهم --}}
            @if($record->employeeTasks && $record->employeeTasks->count() > 0)
            <tr>
                <th>المنفذون والتقييم:</th>
                <td>
                    <div class="employee-list">
                        @foreach ($record->employeeTasks as $employeeTask)
                            <div class="employee-item">
                                @php
                                    $employeeName = $employeeTask->employee->name ?? 'غير معروف';
                                    $rating = $employeeTask->employee_rating;
                                    $ratingText = match($rating) {
                                        1 => 'ضعيف ★',
                                        2 => '★★',
                                        3 => 'متوسط ★★★',
                                        4 => '★★★★',
                                        5 => 'ممتاز ★★★★★',
                                        default => 'غير مقيم',
                                    };
                                    // تنظيف النص للفئة CSS (إزالة المسافات والنجوم والأقواس)
                                    $ratingClass = 'rating-' . str_replace([' ', '★', '(', ')'], ['-', '', '', ''], $ratingText);
                                @endphp
                                {{ $employeeName }} (<span class="rating {{ $ratingClass }}">{{ $ratingText }}</span>)
                            </div>
                        @endforeach
                    </div>
                </td>
            </tr>
            @endif
            @if($record->notes)
            <tr>
                <th>الملاحظات:</th>
                <td>{{ $record->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="images-section">
        <div class="image-row">
            <div class="image-col">
                <h3>صور قبل التنفيذ:</h3>
                @if(is_array($record->before_images_urls) && count($record->before_images_urls) > 0)
                    <div class="images-grid">
                        @foreach($record->before_images_urls as $imageData)
                            @if($imageData['url']) {{-- استخدام URL مباشرة لأنك قلت أنه يعمل لديك --}}
                                <div class="image-wrapper">
                                    <img src="{{ $imageData['url'] }}" alt="قبل التنفيذ">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="no-images">لا توجد صور قبل التنفيذ.</p>
                @endif
            </div><!--
            --><div class="image-col">
                <h3>صور بعد التنفيذ:</h3>
                @if(is_array($record->after_images_urls) && count($record->after_images_urls) > 0)
                    <div class="images-grid">
                        @foreach($record->after_images_urls as $imageData)
                            @if($imageData['url']) {{-- استخدام URL مباشرة لأنك قلت أنه يعمل لديك --}}
                                <div class="image-wrapper">
                                    <img src="{{ $imageData['url'] }}" alt="بعد التنفيذ">
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="no-images">لا توجد صور بعد التنفيذ.</p>
                @endif
            </div>
            <div style="clear: both;"></div> <!-- لضمان مسح الفلوتات بعد الأعمدة -->
        </div>
    </div>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <p class="signature-title">مسؤول الشعبة</p>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <p class="signature-title">مدير القسم</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>تم إنشاء التقرير بتاريخ: {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'نظام التقارير') }}. جميع الحقوق محفوظة.</p>
    </div>

    <!-- زر الطباعة غير ظاهر في ملف PDF نفسه، فقط في معاينة المتصفح -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">🖨️ طباعة التقرير</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #dc3545; color: white; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;">❌ إغلاق</button>
    </div>
</div>

</body>
</html>
