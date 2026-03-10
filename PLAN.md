# Feature Plan: Add Student to Repeated Table (via Add Students Page)

This plan outlines the steps to add a checkbox in the "Add Students" repeater page that allows marking new students for repetition and automatically adding them to the `repeated_students` table.

## Milestone 1: Analysis & Preparation (DONE)
- [x] **Task 1.1: Identify the Target Form**
    - Target: `app/Filament/Resources/StudentResource/Pages/AddStudents.php`.
- [x] **Task 1.2: Verify Database Schema**
    - `repeated_students` requires: `name`, `phone`, `track_start`, `group_id`, `branch_id`.
- [x] **Task 1.3: Review Model Relationships**
    - Confirmed `Student` and `RepeatedStudent` are handled as separate records.

## Milestone 2: UI Implementation (DONE)
- [x] **Task 2.1: Add components to AddStudents Repeater**
- [x] **Task 2.2: Add components to StudentResource Form**
    - Add `Checkbox::make('is_repeated')` and `Select::make('track_start')` to `StudentResource::form()`.

## Milestone 3: Logic Implementation (DONE)
- [x] **Task 3.1: Update Save Logic in AddStudents Page**
    - Modify `save()` method to handle `is_repeated`, `track_start`, and `instructor_id`.
- [x] **Task 3.2: Update Logic in StudentResource**
    - Use `afterCreate` hook in `Pages/CreateStudent.php` to handle repeat logic and `instructor_id`.

## Milestone 4: Verification & Testing (IN PROGRESS)
- [ ] **Task 4.1: Manual Testing (Multi-Student)**
    - Add multiple students via "إضافة طلاب" page.
    - Check "Repeat" for some, leave others unchecked.
    - Verify both `students` and `repeated_students` tables.
- [ ] **Task 4.2: Manual Testing (Single Student)**
    - Add a single student via the "Create" button on the Students list.
    - Check "Repeat" and select track/instructor.
    - Verify no SQL errors occur and records are created in both tables.
