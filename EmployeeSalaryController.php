<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\employee_salary_detais;
use App\Models\employee_salary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class EmployeeSalaryController extends Controller
{
   function employeeSalary($id,request $request){
    $validator= validator :: make($request->all(),[
        'salary_from'=>'required|unique:employee_salaries,applicable_from',
        'salary_till'=>'required|unique:employee_salaries,applicable_till',
        'basic'=>'required',
 
    ]);
    if($validator->fails()){
        $response = [
            'success' =>false,
            'message'  => $validator->errors()
        ];
        return response()->json($response,400);
    }
    else{
        
    $user = user::find($id);
   $user_id=$user->id;
   $username=$user->username;
    $totalsalary= new employee_salary;
    $totalsalary->leave=$request->leave;
    $totalsalary->applicable_from=$request->salary_from;
    $totalsalary->applicable_till=$request->salary_till;
    $salary= new employee_salary_detais;
    $salary->user_id=$user_id;
    $salary->special_allowance=$request->special_allowance;
    $salary->medical_allowance=$request->medical_allowance;
    $salary->basic=$request->basic;
    $salary->hra=$request->hra;
    $salary->conveyance=$request->conveyance;
    
    $salary->arrear=$request->arrear;
    $salary->epf=$request->epf;
    $salary->loan=$request->loan;
    $salary->advance=$request->advance;
    $salary->misc_deduction=$request->misc_deduction;
    $salary->tds=$request->tds;
$salary->save();


    // Calculate the total salary before deductions.
$totalSalaryBeforeDeductions =  $salary->special_allowance + $salary->medical_allowance + $salary->conveyance + $salary->hra +  $salary->basic +  $salary->arrear;


$leave_deduction=$salary->basic / 30 * $totalsalary->leave;
// Calculate the total deduction amount.
$deductionAmount = $leave_deduction+$salary->misc_deduction + $salary->advance + $salary->loan + $salary->epf + $salary->tds;

// Calculate the total salary after deductions.
$totalSalaryAfterDeductions = $totalSalaryBeforeDeductions - $deductionAmount;
$totalsalary->total_salary=$totalSalaryAfterDeductions;
$totalsalary->leave=$request->leave;
$totalsalary->user_id=$user_id;
$totalsalary->save();
return response()->json([
    'success' => true,
  'total salary'=>$totalSalaryAfterDeductions,
    'message' => 'total salary of '.$username.' is '.$totalSalaryAfterDeductions.' from '. $totalsalary->applicable_from.' till '. $totalsalary->applicable_till,
    
]);
    }
   }
   function updateemployeeSalary($id,request $request){

    $user = user::find($id);
   $user_id=$user->id;
   $username=$user->username;
$i=0;
   $employee_salaries_id=DB::table('employee_salaries')->where('user_id',$user_id)->pluck('id');
    $totalsalary= employee_salary::find($employee_salaries_id[$i]);
    // dd($totalsalary);
    $totalsalary->leave=$request->leave;
    $totalsalary->applicable_from=$request->salary_from;
    $totalsalary->applicable_till=$request->salary_till;
    $salaries_id=DB::table('employee_salary_detais')->where('user_id',$user_id)->pluck('id');

    $salary= employee_salary_detais::find($salaries_id[$i]);
    $salary->user_id=$user_id;
    $salary->special_allowance=$request->special_allowance;
    $salary->medical_allowance=$request->medical_allowance;
    $salary->basic=$request->basic;
    $salary->hra=$request->hra;
    $salary->conveyance=$request->conveyance;

    $salary->arrear=$request->arrear;
    $salary->epf=$request->epf;
    $salary->loan=$request->loan;
    $salary->advance=$request->advance;
    $salary->misc_deduction=$request->misc_deduction;
    $salary->tds=$request->tds;
$salary->save();


    // Calculate the total salary before deductions.
$totalSalaryBeforeDeductions =  $salary->special_allowance + $salary->medical_allowance + $salary->conveyance + $salary->hra +  $salary->basic +  $salary->arrear;


$leave_deduction=$salary->basic / 30 * $totalsalary->leave;
// Calculate the total deduction amount.
$deductionAmount = $leave_deduction+$salary->misc_deduction + $salary->advance + $salary->loan + $salary->epf + $salary->tds;

// Calculate the total salary after deductions.
$totalSalaryAfterDeductions = $totalSalaryBeforeDeductions - $deductionAmount;
$totalsalary->total_salary=$totalSalaryAfterDeductions;
$totalsalary->leave=$request->leave;
$totalsalary->user_id=$user_id;
$totalsalary->save();
return response()->json([
    'success' => true,
  'total salary'=>$totalSalaryAfterDeductions,
  'message' =>'your salary has been successfully updated',
    'message1' => 'total salary of '.$username.' is '.$totalSalaryAfterDeductions.' from '. $totalsalary->applicable_from.' till '. $totalsalary->applicable_till
    ]);
   }
   function deleteemployeeSalary($id,request $request){
    $user = user::find($id);
    $user_id=$user->id;
    $i=0;
    $employee_salaries_id=DB::table('employee_salaries')->where('user_id',$user_id)->pluck('id');
     $totalsalary= employee_salary::find($employee_salaries_id[$i]);
    
    $salaries_id=DB::table('employee_salary_detais')->where('user_id',$user_id)->pluck('id');

    $salary= employee_salary_detais::find($salaries_id[$i]);
  
$salary->delete();

$totalsalary->delete();
return response()->json([
    'success' => true,

    'message' => 'your salary has been suuccesfully deleted'
]);
   }
}
