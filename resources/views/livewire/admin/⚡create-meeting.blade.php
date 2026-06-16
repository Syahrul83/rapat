<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;
use App\Models\MeetingDay;

new class extends Component {
    #[Validate('required|max:255')]
    public $title = '';

    public $description = '';

    #[Validate('required|max:255')]
    public $location = '';

    #[Validate('required')]
    public $start_time = '09:00';

    #[Validate('required')]
    public $end_time = '12:00';

    #[Validate('required|min:1')]
    public $dates = [];

    public $newDate = '';

    public function addDate()
    {
        $this->validate(['newDate' => 'required|date|after_or_equal:today']);

        if (! in_array($this->newDate, $this->dates)) {
            $this->dates[] = $this->newDate;
            sort($this->dates);
        }
        $this->newDate = '';
    }

    public function removeDate($index)
    {
        unset($this->dates[$index]);
        $this->dates = array_values($this->dates);
    }

    public function save()
    {
        $this->validate();

        $meeting = Meeting::create([
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($this->dates as $date) {
            MeetingDay::create([
                'meeting_id' => $meeting->id,
                'date' => $date,
            ]);
        }

        session()->flash('success', 'Jadwal rapat berhasil dibuat');
        return redirect()->route('admin.meetings');
    }
};
?>

<div>
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Jadwal Rapat</a>
                    <a href="{{ route('admin.meetings') }}" class="text-sm text-blue-600 font-semibold">Rapat</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold mb-6">Tambah Jadwal Rapat</h1>

        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="save">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan Rapat *</label>
                    <input wire:model.live="title" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea wire:model.live="description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi *</label>
                    <input wire:model.live="location" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai *</label>
                        <input wire:model.live="start_time" type="time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('start_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai *</label>
                        <input wire:model.live="end_time" type="time"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('end_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hari/Tanggal *</label>
                    <div class="flex gap-2 mb-2">
                        <input wire:model.live="newDate" type="date"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button" wire:click="addDate"
                            class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Tambah</button>
                    </div>
                    @error('newDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @error('dates') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($dates as $index => $date)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-sm">
                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                <button type="button" wire:click="removeDate({{ $index }})" class="ml-2 text-blue-600 hover:text-blue-800">&times;</button>
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <a href="{{ route('admin.meetings') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </main>
</div>
