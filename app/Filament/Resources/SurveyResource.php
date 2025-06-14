<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SurveyResource\Pages;
use App\Models\Survey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
// استيراد أكشنات التصدير
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'الاستبيانات';
    protected static ?string $navigationLabel = 'استبيان رضا الزائرين';
    protected static ?string $modelLabel = 'استبيان';
    protected static ?string $pluralModelLabel = 'استبيانات رضا الزائرين';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات العامة (اختياري)')
                    ->schema([
                        Forms\Components\Radio::make('gender')
                            ->label('الجنس')
                            ->options([
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ])
                            ->inline()
                            ->columnSpan(1),

                        Forms\Components\Radio::make('age_group')
                            ->label('الفئة العمرية')
                            ->options([
                                'under_18' => 'أقل من 18',
                                '18_30' => '18-30',
                                '30_45' => '30-45',
                                '45_60' => '45-60',
                                'over_60' => 'أكثر من 60',
                            ])
                            ->inline()
                            ->columnSpan(1),

                        Forms\Components\Radio::make('visit_count')
                            ->label('عدد الزيارات')
                            ->options([
                                'first_time' => 'أول مرة',
                                '2_5_times' => 'من 2 إلى 5 مرات',
                                'over_5_times' => 'أكثر من 5 مرات',
                            ])
                            ->inline()
                            ->columnSpan(1),

                        Forms\Components\Radio::make('stay_duration')
                            ->label('مدة الإقامة')
                            ->options([
                                'less_1h' => 'أقل من ساعة',
                                '2_3h' => 'من 2 إلى 3 ساعات',
                                '4_6h' => 'من 4 إلى 6 ساعات',
                                'over_6h' => 'أكثر من 6 ساعات',
                            ])
                            ->inline()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('تقييم نظافة المرافق العامة')
                    ->schema([
                        Forms\Components\Radio::make('toilet_cleanliness')
                            ->label('نظافة دورات المياه')
                            ->options([
                                'excellent' => 'ممتازة',
                                'very_good' => 'جيدة جدًا',
                                'good' => 'جيدة',
                                'acceptable' => 'مقبولة',
                                'poor' => 'سيئة',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('hygiene_supplies')
                            ->label('توفر مستلزمات النظافة')
                            ->options([
                                'always' => 'دائمًا متوفرة',
                                'often' => 'غالبًا متوفرة',
                                'rarely' => 'نادرًا متوفرة',
                                'never' => 'غير متوفرة إطلاقًا',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('yard_cleanliness')
                            ->label('نظافة الساحات والممرات')
                            ->options([
                                'clean' => 'نظيفة',
                                'needs_improvement' => 'تحتاج إلى تحسين',
                                'dirty' => 'غير نظيفة',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('cleaning_teams')
                            ->label('فرق التنظيف')
                            ->options([
                                'clearly' => 'نعم، بشكل واضح',
                                'sometimes' => 'نعم، ولكن ليس دائمًا',
                                'rarely' => 'نادرًا ما ألاحظ ذلك',
                                'not_noticed' => 'لا، لم ألاحظ',
                            ])
                            ->inline(),
                    ]),

                Forms\Components\Section::make('تقييم أماكن الاستراحة والقاعات')
                    ->schema([
                        Forms\Components\Radio::make('hall_cleanliness')
                            ->label('نظافة القاعات')
                            ->options([
                                'very_clean' => 'نظيفة جدًا',
                                'clean' => 'نظيفة',
                                'needs_improvement' => 'تحتاج إلى تحسين',
                                'dirty' => 'غير نظيفة',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('bedding_condition')
                            ->label('حالة البطائن والفرش')
                            ->options([
                                'excellent' => 'نعم، بحالة ممتازة',
                                'needs_care' => 'نعم، ولكن تحتاج إلى مزيد من العناية',
                                'not_clean' => 'ليست نظيفة بما يكفي',
                                'not_available' => 'غير متوفرة بشكل كافي',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('ventilation')
                            ->label('التهوية')
                            ->options([
                                'excellent' => 'نعم، التهوية ممتازة',
                                'needs_improvement' => 'متوفرة ولكن تحتاج إلى تحسين',
                                'poor' => 'التهوية ضعيفة وغير كافية',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('lighting')
                            ->label('الإضاءة')
                            ->options([
                                'excellent' => 'ممتازة',
                                'good' => 'جيدة',
                                'needs_improvement' => 'ضعيفة وتحتاج إلى تحسين',
                            ])
                            ->inline(),
                    ]),

                Forms\Components\Section::make('تقييم خدمات سقاية المياه')
                    ->schema([
                        Forms\Components\Radio::make('water_trams_distribution')
                            ->label('توزيع ترامز الماء')
                            ->options([
                                'everywhere' => 'نعم، في كل مكان',
                                'needs_more' => 'نعم، ولكن تحتاج إلى زيادة',
                                'not_enough' => 'غير موزعة بشكل كافي',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('water_trams_cleanliness')
                            ->label('نظافة ترامز الماء')
                            ->options([
                                'very_clean' => 'نظيفة جدًا',
                                'clean' => 'نظيفة',
                                'needs_improvement' => 'تحتاج إلى تحسين',
                                'dirty' => 'غير نظيفة',
                            ])
                            ->inline(),

                        Forms\Components\Radio::make('water_availability')
                            ->label('توفر مياه الشرب')
                            ->options([
                                'always' => 'دائمًا متوفرة',
                                'often' => 'غالبًا متوفرة',
                                'rarely' => 'نادرًا ما تتوفر',
                                'not_enough' => 'لا تتوفر بشكل كافي',
                            ])
                            ->inline(),
                    ]),

                Forms\Components\Section::make('التقييم العام والملاحظات')
                    ->schema([
                        Forms\Components\Radio::make('overall_satisfaction')
                            ->label('مستوى الرضا العام')
                            ->options([
                                'very_satisfied' => 'راض جدًا',
                                'satisfied' => 'راض',
                                'acceptable' => 'مقبول',
                                'dissatisfied' => 'غير راض',
                            ])
                            ->inline(),

                        Forms\Components\Textarea::make('problems_faced')
                            ->label('المشاكل التي واجهتها')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('suggestions')
                            ->label('اقتراحات للتحسين')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإدخال')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('survey_number')
                    ->label('📄 رقم الاستبيان')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('overall_satisfaction')
                    ->label('الرضا العام')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'very_satisfied' => 'راض جدًا',
                        'satisfied' => 'راض',
                        'acceptable' => 'مقبول',
                        'dissatisfied' => 'غير راض',
                        null => 'غير محدد',
                        default => 'غير معروف',
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'very_satisfied' => 'success',
                        'satisfied' => 'primary',
                        'acceptable' => 'warning',
                        'dissatisfied' => 'danger',
                        null => 'secondary',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('visit_count')
                    ->label('عدد الزيارات')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'first_time' => 'أول مرة',
                        '2_5_times' => '2-5 مرات',
                        'over_5_times' => 'أكثر من 5',
                        null => 'غير محدد',
                        default => 'غير معروف',
                    }),

                Tables\Columns\TextColumn::make('stay_duration')
                    ->label('مدة الإقامة')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'less_1h' => '< ساعة',
                        '2_3h' => '2-3 ساعات',
                        '4_6h' => '4-6 ساعات',
                        'over_6h' => '> 6 ساعات',
                        null => 'غير محدد',
                        default => 'غير معروف',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('overall_satisfaction')
                    ->label('الرضا العام')
                    ->options([
                        'very_satisfied' => 'راض جدًا',
                        'satisfied' => 'راض',
                        'acceptable' => 'مقبول',
                        'dissatisfied' => 'غير راض',
                    ]),

                Tables\Filters\SelectFilter::make('visit_count')
                    ->label('عدد الزيارات')
                    ->options([
                        'first_time' => 'أول مرة',
                        '2_5_times' => '2-5 مرات',
                        'over_5_times' => 'أكثر من 5',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
                // زر التصدير ضمن الـ Bulk Actions
                FilamentExportBulkAction::make('export')
                    ->label('تصدير البيانات'),
            ])
            ->headerActions([
                // زر التصدير في رأس الجدول
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSurveys::route('/'),
            'create' => Pages\CreateSurvey::route('/create'),
            'edit' => Pages\EditSurvey::route('/{record}/edit'),
        ];
    }
}
