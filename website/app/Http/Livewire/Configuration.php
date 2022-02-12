<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Configuration as Conf;
use Illuminate\Validation\Rule;
class Configuration extends Component
{
    public $label = 1;
    public $day;
    public $wifi, $data, $span;
    public $label1, $label2, $label3, $label4;
    public function mount()
    {
        $id = auth()->user()->id;
        $conf = Conf::where('user_id', $id)->first();
        $this->day = (array)json_decode($conf->day);
        $this->wifi = (array)json_decode($conf->wifi);
        $this->label1 = (array)json_decode($conf->label1);
        $this->label2 = (array)json_decode($conf->label2);
        $this->label3 = (array)json_decode($conf->label3);
        $this->label4 = (array)json_decode($conf->label4);
        $this->data = (array)json_decode($conf->data);
        $this->span = $conf->span;
    }
    public function addLabel()
    {
        $this->label < 4?$this->label += 1:$this->label = 1;
    }
    public function saveConfiguration()
    {
       
        $this->validate([
            'span'=>'nullable|date|after:tomorrow',
            'data.barcode' => [Rule::requiredIf($this->data == [])],
            'label1.*' =>'required',
            'label1.penality' => 'required|numeric',
            'label2.start' => [Rule::requiredIf($this->label2 != [])],
            'label2.end' => [Rule::requiredIf($this->label2 != [])],
            'label3.penality' => [Rule::requiredIf($this->label2 != []), 'numeric'],
            'label3.start' => [Rule::requiredIf($this->label3 != [])],
            'label3.end' => [Rule::requiredIf($this->label3 != [])],
            'label4.penality' => [Rule::requiredIf($this->label3 != []), 'numeric'],
            'label4.start' => [Rule::requiredIf($this->label4 != [])],
            'label4.end' => [Rule::requiredIf($this->label4 != [])],
            'label2.penality' => [Rule::requiredIf($this->label4 != []), 'numeric'],
            'wifi.password' => 'required|min:8',
            'wifi.ssid' => 'required',
        ]);
        Conf::where('user_id', auth()->user()->id)->update([
            'data' => json_encode($this->data),
            'span' => $this->span,
            'day' => json_encode($this->day),
            'wifi' => json_encode($this->wifi),
            'label1' => json_encode($this->label1),
            'label2' => json_encode($this->label2),
            'label3' => json_encode($this->label3),
            'label4' => json_encode($this->label4),
        ]);

    }
    public function render()
    {
        return view('livewire.configuration');
    }
}
