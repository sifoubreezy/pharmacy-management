<?php


namespace App\Repositories;


use App\Expenditure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpendituresRepository
{

    public function getAll()
    {
        return Expenditure::all();
    }

    public function getTotal()
    {
        return Expenditure::query()->sum('amount');
    }

    public function getById($id)
    {
        return Expenditure::find($id);
    }

    public function getCount()
    {
        return Expenditure::all()->count();
    }

    public function getTodayExpenditures()
    {
          return Expenditure::query()
              ->select('*')
        ->whereDate("updated_at",Carbon::today()->format('Y-m-d'))

        ->orderBy('id',"asc")
        ->get()
        ;
    }

    public function getFilteredExpenditures($from_date, $to_date)
    {
        return Expenditure::query()->whereBetween('updated_at', array($from_date, $to_date))->get();
    }

    public function getCountTodayExpenditures()

    {
        return Expenditure::whereDate("updated_at",Carbon::today()->format('Y-m-d'))->count();

    }

}