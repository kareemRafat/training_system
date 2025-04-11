<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Group;
use Filament\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddStudents extends Page
{
    use InteractsWithForms;

    protected static string $resource = StudentResource::class;

    protected static string $view = 'filament.pages.add-students';

    protected static ?string $title = 'إضافة طلاب';

    protected static ?string $breadcrumb = 'إضافة طلاب';

    public array $data = [];

    public function mount(): void
    {
        // when mount make three default repeaters fields
        $this->form->fill([
            'students' => array_fill(0, 3, [
                'name' => '',
                'phone' => '',
                'group_id' => null,
                'branch_id' => null,
                'start' => 'delay',
                'status' => 'normal',
            ]),
        ]);

        // store the previous URL in the session to return back to it
        // except if the previous URL contains '/manage_students'
        if (! str_contains(url()->previous(), '/manage_students')) {
            session(['back_url' => url()->previous()]);
        }
    }

    protected function getFormSchema(): array
    {
        return [
            // Global Group Selector
            Select::make('global_group_id')
                ->required()
                ->validationMessages([
                    'required' => 'يجب اختيار جروب لإضافة الطلاب',
                ])
                ->label('تحديد مجموعة موحدة لجميع الطلاب')
                ->options(
                    Group::when(
                        Auth::check() && Auth::user()->branch_id,
                        fn($query) => $query->where('branch_id', Auth::user()->branch_id),
                        fn($query) => $query
                    )
                        ->orderBy('end_date', 'desc')
                        ->limit(5)
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $students = $get('students') ?? [];
                    $group = Group::find($state);
                    foreach ($students as $key => $student) {
                        $students[$key]['group_id'] = $state;
                        if ($group) {
                            $students[$key]['branch_id'] = $group->branch_id;
                        }
                    }
                    $set('students', $students);
                }),

            // Students Repeater
            Repeater::make('students')
                ->schema([
                    // First Line - Student Basic Info
                    Grid::make(3)
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->label('اسم الطالب')
                                ->rule(['unique:students,name'])
                                ->validationMessages([
                                    'required' => 'يجب ادخال اسم الطالب',
                                    'unique' => "الإسم مسجل من قبل"
                                ]),
                            TextInput::make('phone')
                                ->required()
                                ->label('رقم الهاتف')
                                ->type('tel')
                                ->unique(ignoreRecord: true)
                                ->rule(['unique:students,phone'])
                                ->validationMessages([
                                    'required' => 'يجب ادخال رقم التليفون',
                                    'unique' => "التليفون مسجل من قبل"
                                ])
                                ->inputMode('tel')
                                ->helperText('يجب أن يكون الرقم مكون من 11 رقم')
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('phone', preg_replace('/[^0-9]/', '', $state));
                                }),
                            Radio::make('start')
                                ->required()
                                ->label('ملاحظات البداية')
                                ->options([
                                    'directly' => 'مباشرة',
                                    'delay' => 'تأجيل',
                                ])
                                ->descriptions([
                                    'directly' => 'بدء التدريب عند اقرب مجموعة',
                                    'delay' => 'تأجيل التدريب',
                                ])
                                ->default('delay')
                                ->inline()
                                ->inlineLabel(false),

                            Radio::make('status')
                                ->hidden()
                                ->required()
                                ->label('الحالة')
                                ->options([
                                    'normal' => 'عادي',
                                    'important' => 'مستعجل',
                                ])
                                ->default('normal')
                                ->inline()
                                ->inlineLabel(false),
                        ]),
                ])
                ->label('طـلاب جـدد')
                ->collapsible()
                ->columnSpanFull()
                ->itemLabel(fn(array $state): ?string => $state['name'] ?: 'طالب جديد'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        // Process your data here
        foreach ($data['students'] as $student) {
            // Save each student to database
            $student['created_at'] = now()->timezone(config('app.timezone'));
            \App\Models\Student::create($student);
        }

        Notification::make()
            ->title('تم إضافة الـطلاب بنجاح')
            ->success()
            ->send();

        $this->redirect(StudentResource::getUrl('index'));
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('رجوع')
                ->icon('heroicon-s-backward')
                ->color('warning')
                ->url(session('back_url')),
        ];
    }
}
