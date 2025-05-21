<?php

namespace App\Http\Controllers;

use App\Models\Discipline;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\DisciplineFormRequest;
use Illuminate\Support\Facades\DB;

class DisciplineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $disciplines = Discipline::paginate(20);
        return view('disciplines.index', compact('disciplines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $discipline = new Discipline();
        // $courses no longer required, because it is available through View::share
        // Check AppServiceProvider
        //$courses = Course::all();
        return view('disciplines.create')
            ->with('discipline', $discipline);
        //->with('courses', $courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function  store(DisciplineFormRequest $request): RedirectResponse
    {
        $NewDiscipline = Discipline::create($request->validated());
        $url = route('disciplines.show', ['discipline' => $NewDiscipline]);
        $htmlMessage = "Discipline <a href='$url'><u>{$NewDiscipline->name}</u></a> ({$NewDiscipline->abbreviation}) has been created successfully!";
        return redirect()->route('disciplines.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    /**
     * Display the specified resource.
     */
    public function show(Discipline $discipline): View
    {
        return view('disciplines.show')
            ->with('discipline', $discipline);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discipline $discipline): View
    {
        return view('disciplines.edit')
            ->with('discipline', $discipline);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DisciplineFormRequest $request, Discipline $discipline): RedirectResponse
    {
        $discipline->update($request->validated());
        $url = route('disciplines.show', ['discipline' => $discipline]);
        $htmlMessage = "Discipline <a href='$url'><u>{$discipline->name}</u></a> ({$discipline->abbreviation}) has been updated successfully!";
        return redirect()->route('disciplines.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discipline $discipline): RedirectResponse
    {
        try {
            $url = route('disciplines.show', ['discipline' => $discipline]);
            $totalStudents = DB::scalar(
                'select count(*) from students_disciplines where discipline_id = ?',
                [$discipline->id]
            );
            $totalTeachers = DB::scalar(
                'select count(*) from teachers_disciplines where discipline_id = ?',
                [$discipline->id]
            );
            if ($totalStudents == 0 && $totalTeachers == 0) {
                $discipline->delete();
                $alertType = 'success';
                $alertMsg = "Discipline {$discipline->name} ({$discipline->abbreviation}) has been deleted successfully!";
            } else {
                $alertType = 'warning';
                $studentsStr = match (true) {
                    $totalStudents <= 0 => "",
                    $totalStudents == 1 => "there is 1 student enrolled in it",
                    $totalStudents > 1 => "there are $totalStudents students enrolled in it",
                };
                $teachersStr = match (true) {
                    $totalTeachers <= 0 => "",
                    $totalTeachers == 1 => "it already has 1 teacher",
                    $totalTeachers > 1 => "it already has $totalTeachers teachers",
                };
                $justification = $studentsStr && $teachersStr
                    ? "$teachersStr and $studentsStr"
                    : "$teachersStr$studentsStr";
                $alertMsg = "Discipline <a href='$url'><u>{$discipline->name}</u></a> ({$discipline->abbreviation}) cannot be deleted because $justification.";
            }
        } catch (\Exception $error) {
            $alertType = 'danger';
            $alertMsg = "It was not possible to delete the discipline
                            <a href='$url'><u>{$discipline->name}</u></a> ({$discipline->abbreviation})
                            because there was an error with the operation!";
        }
        return redirect()->back()
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
}
