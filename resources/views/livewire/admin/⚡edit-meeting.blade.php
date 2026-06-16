<?php

use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Meeting;

new class extends Component {
    public $meetingId;

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

    public function mount($id)
    {
        $meeting = Meeting::with('meetingDays')->findOrFail($id);
        $this->meetingId = $meeting->id;
        $this->title = $meeting->title;
        $this->description = $meeting->description;
        $this->location = $meeting->location;
        $this->start_time = $meeting->start_time;
        $this->end_time = $meeting->end_time;
        $this->dates = $meeting->meetingDays->pluck('date')->map(fn($d) => $d->toDateString())->toArray();
    }

    public function addDate()
    {
        $this->validate(['newDate' => 'required|date']);

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

        $meeting = Meeting::findOrFail($this->meetingId);
        $meeting->update([
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        $meeting->meetingDays()->delete();
        foreach ($this->dates as $date) {
            $meeting->meetingDays()->create(['date' => $date]);
        }

        session()->flash('success', 'Jadwal rapat berhasil diperbarui');
        return redirect()->route('admin.meetings');
    }
};
?>

<div>
    <div class="max-w-3xl">
        <a href="{{ route('admin.meetings') }}" class="text-label-md text-primary hover:underline mb-4 inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
        <h1 class="text-headline-md text-primary mb-6">Edit Jadwal Rapat</h1>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
            <form wire:submit="save" class="space-y-5">
                <div>
                    <label class="block text-label-md text-on-surface mb-1">Nama Kegiatan Rapat *</label>
                    <input wire:model.live="title" type="text"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('title') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Keterangan</label>
                    <textarea wire:model.live="description" rows="3"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md"></textarea>
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Lokasi *</label>
                    <input wire:model.live="location" type="text"
                        class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                    @error('location') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-label-md text-on-surface mb-1">Jam Mulai *</label>
                        <input wire:model.live="start_time" type="time"
                            class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        @error('start_time') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-label-md text-on-surface mb-1">Jam Selesai *</label>
                        <input wire:model.live="end_time" type="time"
                            class="w-full px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        @error('end_time') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-label-md text-on-surface mb-1">Hari/Tanggal *</label>
                    <div class="flex gap-2 mb-2">
                        <input wire:model.live="newDate" type="date"
                            class="flex-1 px-4 py-2 bg-surface-bright border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-body-md">
                        <button type="button" wire:click="addDate" class="px-4 py-2 bg-surface-container-high text-on-surface rounded-lg hover:bg-surface-container-highest transition-colors text-label-md">Tambah</button>
                    </div>
                    @error('newDate') <span class="text-error text-label-sm">{{ $message }}</span> @enderror
                    @error('dates') <span class="text-error text-label-sm">{{ $message }}</span> @enderror

                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($dates as $index => $date)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-sm">
                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                <button type="button" wire:click="removeDate({{ $index }})" class="ml-2 hover:text-on-primary-container/70">&times;</button>
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.meetings') }}" class="px-6 py-2 border border-outline-variant rounded-lg text-label-md font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">Batal</a>
                    <button type="submit" class="px-6 py-2 bg-primary text-on-primary rounded-lg text-label-md font-bold hover:brightness-95 transition-all">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
