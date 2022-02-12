<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Attendants;
use App\Http\Livewire\Configuration;
use App\Models\Configuration as Conf;
use Illuminate\Http\Request;
use App\Models\Attendant;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/attendants', Attendants::class)->name('attendants')->middleware('auth');
Route::get('/configure', Configuration::class)->name('configure')->middleware('auth');

Route::get('config/{id}', function($id){
    $conf = Conf::where('user_id', $id)->first();
    return json_encode($conf);
});

Route::get('/attendant/download/{id}', function($user_id){
    $attendants = Attendant::where('user_id', $user_id)->orderBy('identifier')->get();
    $total = "";
    foreach ($attendants as $attendant) {
        $att = $attendant->identifier." 0   0   -1     ".$attendant->name." |";
        $space = 51-strlen($att);
        for ($i=0; $i < $space; $i++) { 
            $att .= " ";
        }
        $att .= "|l\n";
        echo $att;
        // $total .= $att;
    }
    // return $total;
});
Route::get('/update', function(Request $request){
    if($request->has('id')){
        //url data should   {"87641":4.5,"87643":4.5}&id=1
        $id = $request->id;
        $data = json_decode($request->data);
        $data = (array)$data;
        $attendants = Attendant::where('user_id', $id)->get();
        foreach ($attendants as $attendant) {
            $attendant->attendance = $data[$attendant->identifier];
            $attendant->save();
        }
        return 'well done';
    }
    return 'please set id';
});