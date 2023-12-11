<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Test;
use App\Models\Student;
class CalcConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calc_consistency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */


//********************************************************************************************
    //  protected function schedule(Schedule $schedule)
    //  {
    //      $schedule->command('calc_consistency')
    //      ->dailyAt('17:15')
    //      ->timezone('Asia/Riyadh'); }
//****************************************************************************************** */
     public function handle()
     {
         $students = Student::all();

         foreach ($students as $student) {
             $tests = Test::where('student_id', $student->id)
                 ->whereDate('date', now()->toDateString())
                // ->whereTime('date', '=', now()->format('H:i:s'))
                 ->get();

                 if ($tests->isNotEmpty()) {
                 $c = 0;

                 foreach ($tests as $test) {
                     $c += $test->no_pages;
                 }

                 $current_numberofpages = $student->current_consistency * $student->days_inrow;

                 $student->days_inrow += 1;
                 $student->save();

                 $current_numberofpages += $c;

                 $student->previous_consistency = $student->current_consistency;
           //       $student->current_consistency = ceil($current_numberofpages / $student->days_inrow);

                $student->current_consistency = $current_numberofpages / $student->days_inrow;

                 if ($student->max_consistency < $student->current_consistency) {
                     $student->max_consistency = $student->current_consistency;
                 }

                 $student->save();
             }else{
                $student->days_inrow = 0;
                $student->previous_consistency = $student->current_consistency;
                $student->current_consistency = 0 ;
                $student->save();


             }

         }
     }
}
