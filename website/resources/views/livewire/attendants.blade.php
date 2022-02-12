<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendants') }}
        </h2>
    </x-slot>
    <div>
        <div>
            @if($toggle)
            <div class="flex justify-end pr-4 pt-4">
               <div class="grid grid-cols-1">
                    <input wire:model="name" type="text" class="border pl-3 mb-2 h-8" placeholder="Name" autofocus>
                    @error('name') <span class="text-red-500">{{ $message }}</span>@enderror
                    <input wire:model.lazy="identifier" type="text" class="border pl-3 h-8" name="" id="" placeholder="Identifier" autofocus>
                    @error('identifier') <span class="text-red-500">{{ $message }}</span>@enderror
                </div>
            </div>
            @endif
        </div>
        <div class="p-4 flex justify-end">
            <button wire:click="addAttendants" class="bg-gray-800 text-white w-64 p-3 rounded-lg font-semibold">Add attendants</button>
        </div>
        <div class="pl-5 mb-10">
            <div class="flex flex-rows text-2xl font-bold">
                <div class="w-1/3">Name</div>
                <div class="w-1/4">Id number</div>
                <div class="w-1/4">Fingerprint Id</div>
                <div class="w-1/4">Attendance</div>
                <div class="">Action</div>
            </div>
            @foreach($attendant as $attendanter)
                <div class="flex flex-rows">
                <div class="w-1/3">{{ $attendanter->name }}</div>
                <div class="w-1/4">{{ $attendanter->identifier }}</div>
                <div class="w-1/4">{{ ($attendanter->finger_id == -1)?'Not seted':$attendanter->finger_id }}</div>
                <div class="w-1/4">{{ $attendanter->attendance }}</div>
                <button wire:click="delete({{ $attendanter->id }})" class="bg-red-500 rounded-lg pl-2 pr-2 mb-1">Delete</button>
             </div>
            @endforeach
           <div class="text-3xl">Total attendance created were: <i class="text-blue-700">{{ $totals }}</i></div>
        </div>
    </div>
</div>
