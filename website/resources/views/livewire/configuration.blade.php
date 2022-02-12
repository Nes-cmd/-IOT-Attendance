<div class="shadow-lg w-4/5">
    <x-slot name="header">
        <div class="font-semibold text-xl text-gray-800 leading-tight">
            {{__('Configuration page')}}
        </div>
    </x-slot>
    <div class="pl-10">
        <div>
            <div class="">
                <div class="text-xl">1. For how long time you need the attendance?</div>
                <div class="mb-5">
                    <label class="pl-8" for="span">End time of the attendance: </label>
                    <input wire:model="span" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3" type="date" ><br>
                    @error('span') <span class="text-red-400">{{$message}}</span> <br> @enderror
                </div>
                <div>
            </div>

            <div>
                <div class="text-xl">2. For what days of the week you need the attendance to be taken?</div>
                <div class="mb-4 flex flex-row">
                    <i>Monday</i>
                    <input wire:model="day.monday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                    <i>Thuesday</i>
                    <input wire:model="day.tuesday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                    <i>Wednsday</i>
                    <input wire:model="day.wednsday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                    <i>Thursday</i>
                    <input wire:model="day.thursday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                    <i>Friaday</i>
                    <input wire:model="day.friday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                    <i>Saturday</i>
                    <input wire:model="day.saturday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                    <i>Suanday</i>
                    <input wire:model="day.sunday" class="bg-gray-300 rounded-sm h-6 w-8 mr-2" type="checkbox" >
                </div>
                </div>
            </div>

            <div class="">
                <div class="text-xl">3. What data are you going to provide?</div>
                <div class="mb-5">
                    <label class="pl-8" for="span">Type of data: </label>
                    <i>Barcode</i> 
                    <input wire:model="data.barcode"  class="bg-gray-300 rounded-sm h-6 w-8 mr-2" name="data1" type="checkbox" >
                    <i>Fingerprint</i>
                    <input wire:model="data.fingerprint"  class="bg-gray-300 rounded-sm h-6 w-8 mr-2" name="data1" type="checkbox" >
                    @error('label1.name') <span class="text-red-400">{{$message}}</span> <br> @enderror
                </div>
                <div>
            </div>
            <div class="">
                <div class="text-xl">4. Do you need to add wifi settings?</div>
                <div class="mb-2">
                    <label  class="pl-8" for="span">SSID or name of wifi: </label>
                    <input wire:model="wifi.ssid" class="ml-2 border bg-gray-300 w-60 rounded-sm pl-2 pr-3" type="text" >
                </div>
                <div class="mb-5">
                    <label class="pl-8" for="span">Password of wifi: </label>
                    <input wire:model="wifi.password" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3" type="text" >
                    @error('wifi.password') <span class="text-red-400">{{$message}}</span> <br> @enderror
                    <p class="text-red-500">Please make sure you entered the correct name and password, Unless the device cannot connect to network.</p>
                </div>
                <div>
            </div>

            <div class="">
                <div class="text-xl">5. At what time you need the attendance to be taken?</div>
                <div class="mb-5">
                    <label class="pl-8" for="span">Label 1 <i class="text-sm text-red-400">Mandatory</i>: </label>
                    <div class="ml-20">
                        <i>Name</i>
                        <input wire:model.lazy="label1.name" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="text" placeholder="e.g Morning" ><br>
                        @error('label1.name') <span class="text-red-400">{{$message}}</span> <br> @enderror
                        <i class="ml-25">Start time</i>
                        <input wire:model.lazy="label1.start" class="border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time" ><br>
                        @error('label1.start') <span class="text-red-400">{{$message}}</span> <br> @enderror
                        <i>End time</i>
                        <input wire:model.lazy="label1.end" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time"><br>
                        @error('label1.end') <span class="text-red-400">{{$message}}</span> <br> @enderror
                        <i>Penality</i>
                        <input wire:model.lazy="label1.penality" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="number" placeholder="Penality in minute"><br>
                        @error('label1.penality') <span class="text-red-400">{{$message}}</span> <br> @enderror
                    </div>
                </div>
                
                @if($label == 2 || $label == 3 || $label == 4)
                <div class="mb-5">
                    <label class="pl-8" for="span">Label 2 <i class="text-sm"> if you have only, it is optional</i>: </label>
                    <div class="ml-20">
                        <i>Name</i>
                        <input wire:model.lazy="label2.name"  class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="text" placeholder="e.g Afternoon" ><br>
                    
                        <i class="ml-25">Start time</i>
                        <input wire:model.lazy="label2.start"  class="border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time" ><br>
                        @error('label2.start') <span class="text-red-400">{{$message}}</span> <br> @enderror

                        <i>End time</i>
                        <input  wire:model.lazy="label2.end" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time"><br>
                        @error('label2.end') <span class="text-red-400">{{$message}}</span> <br> @enderror

                        <i>Penality</i>
                        <input  wire:model.lazy="label2.penality" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="number" placeholder="Penality in minute">
                        @error('label2.penality') <span class="text-red-400">{{$message}}</span> <br> @enderror
                    </div>
                </div>
                @endif
                @if($label == 3 || $label == 4)
                <div class="mb-5">
                    <label class="pl-8" for="span">Label 3 <i class="text-sm"> if you have only, it is optional</i>: </label>
                    <div class="ml-20">
                        <i>Name</i>
                        <input wire:model.lazy="label3.name" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="text" placeholder="e.g Breakfast" ><br>
                    
                        <i class="ml-25">Start time</i>
                        <input wire:model.lazy="label3.start" class="border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time" ><br>
                        @error('label3.start') <span class="text-red-400">{{$message}}</span> <br> @enderror

                        <i>End time</i>
                        <input wire:model.lazy="label3.end" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time"><br>
                        @error('label3.end') <span class="text-red-400">{{$message}}</span> <br> @enderror

                        <i>Penality</i>
                        <input wire:model.lazy="label3.penality" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="number" placeholder="Penality in minute">
                        @error('label3.penality') <span class="text-red-400">{{$message}}</span> <br> @enderror
                    </div>
                </div>
                @endif
                @if($label == 4)
                <div class="mb-5">
                    <label class="pl-8" for="span">Label 4 <i class="text-sm"> if you have only, it is optional</i>: </label>
                    <div class="ml-20">
                        <i>Name</i>
                        <input wire:model.lazy="label4.lazy.name" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="text" placeholder="e.g To night" ><br>
                    
                        <i class="ml-25">Start time</i>
                        <input wire:model.lazy="label4.lazy.start" class="border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time" ><br>

                        <i>End time</i>
                        <input wire:model.lazy="label4.lazy.end" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="time"><br>

                        <i>Penality</i>
                        <input wire:model.lazy="label4.lazy.penality" class="ml-2 border bg-gray-300 w-64 rounded-sm pl-2 pr-3 mb-2" type="number" placeholder="Penality in minute">
                    </div>
                </div>
                @endif
                <div class="flex justify-end w-1/3">
                    @if($label <=3 )
                    <button wire:click="addLabel" class="bg-gray-800 text-white h-8 rounded-sm pl-6 pr-6"> Add label</button>
                    @else
                    <button wire:click="addLabel" class="bg-gray-800 text-white h-8 rounded-sm pl-6 pr-6"> Remove label</button>
                    @endif
                </div>
                <div>
            </div>
            <div class="m-4 flex justify-between">
                <button wire:click="saveConfiguration" class="bg-gray-900 text-white h-8 w-1/2 rounded-full">Save configuration</button>
            </div>
    </div>
</div>
