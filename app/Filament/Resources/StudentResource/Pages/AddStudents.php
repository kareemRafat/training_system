<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Group;
use Filament\Actions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

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
                ->reactive(),

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
                                    'unique' => 'الإسم مسجل من قبل',
                                ]),
                            TextInput::make('phone')
                                ->required()
                                ->label('رقم الهاتف')
                                ->type('tel')
                                ->rule(['phone:' . config('app.PHONE_COUNTRIES'),'unique:students,phone'])
                                ->validationMessages([
                                    'required' => 'يجب ادخال رقم التليفون',
                                ])
                                ->helperText('يجب أن يكون الرقم مكون من 11 رقم'),
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
            \Filament\Forms\Components\Hidden::make('branch_id')
                ->default(fn(callable $get) => optional(\App\Models\Group::find($get('../../global_group_id')))->branch_id)
                ->dehydrated(true),

        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        // Process your data here
        foreach ($data['students'] as $student) {
            // Save each student to database
            $student['created_at'] = now();
            $student['group_id'] = $data['global_group_id'];
            $student['branch_id'] = \App\Models\Group::find($data['global_group_id'])->branch_id;
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
