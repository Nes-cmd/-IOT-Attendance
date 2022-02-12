<?php

namespace App\Http\Livewire;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use App\Models\Attendant;

class Attendants extends Component
{
    public $toggle = false;
    public $name,$identifier;
    public function addAttendants()
    {
        if($this->toggle){
            $this->validate([
                'name' => ['required', 'string', 'max:30'],
                'identifier' => ['required','numeric','digits:5', 'unique:attendants']
            ]);
            Attendant::create([
                'user_id' => auth()->user()->id,
                'name' => $this->name,
                'identifier' => $this->identifier,
            ]);
            $this->toggle = false;
        }
        else{
            $this->toggle = true;
        }
    }
    public function delete($id)
    {
        Attendant::where('id', $id)->delete();
    }
    public function render()
    {
        $attendant = Attendant::where('user_id', auth()->user()->id)->get();
        $totals = \DB::table('totals')->first();
        $totals = $totals?$totals->totals:0;
        return view('livewire.attendants', compact('attendant', 'totals'));
    }
}
