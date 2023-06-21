<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;


class UploadFile extends Component
{
    use WithFileUploads;

    public $file;
    public $progress = 0;
    public function render()
    {
        return view('livewire.upload-file');
    }
    public function upload()
    {
        $path = $this->file->store('files');

        $this->progress = 0;

        Storage::putFileAs('public/files', $this->file, $path);

        $this->progress = 100;
    }
}
