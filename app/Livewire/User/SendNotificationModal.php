<?php

namespace App\Livewire\User;

use App\Jobs\SendPushNotificationJob;
use App\Models\App;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class SendNotificationModal extends Component
{
    use WithFileUploads;

    public $showModal = false;

    public $app_id = '';

    public $title = '';

    public $body = '';

    public $image_file;

    public $notification_style = 'text';

    public $schedule_type = 'now';

    public $scheduled_at = '';

    public $userApps = [];

    protected function rules()
    {
        return [
            'app_id' => 'required|exists:apps,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:1000',
            'image_file' => $this->notification_style === 'image' ? 'required|image|mimes:jpeg,png,jpg|max:1024' : 'nullable',
            'notification_style' => 'required|in:text,image',
            'schedule_type' => 'required|in:now,later',
            'scheduled_at' => 'required_if:schedule_type,later|nullable|date|after:now',
        ];
    }

    protected $messages = [
        'app_id.required' => 'Please select an app.',
        'title.required' => 'Notification title is required.',
        'body.required' => 'Notification body is required.',
        'image_file.required' => 'Please upload an image.',
        'image_file.image' => 'The file must be an image.',
        'image_file.max' => 'Image size must not exceed 1MB.',
        'scheduled_at.after' => 'Scheduled time must be in the future.',
    ];

    public function mount()
    {
        $this->loadUserApps();
    }

    public function loadUserApps()
    {
        $this->userApps = App::where('user_id', auth()->id())
            ->whereHas('pushNotificationConfig', function ($query) {
                $query->where('enabled', true);
            })
            ->get();
    }

    #[On('open-send-notification-modal')]
    public function open()
    {
        $this->reset(['app_id', 'title', 'body', 'image_file', 'notification_style', 'schedule_type', 'scheduled_at']);
        $this->loadUserApps();
        $this->showModal = true;
    }

    public function updatedNotificationStyle($value)
    {
        if ($value === 'text') {
            $this->image_file = null;
        }
    }

    public function send()
    {
        $this->validate();

        // Verify app belongs to user and has FCM enabled
        $app = App::where('id', $this->app_id)
            ->where('user_id', auth()->id())
            ->whereHas('pushNotificationConfig', function ($query) {
                $query->where('enabled', true);
            })
            ->first();

        if (! $app) {
            $this->addError('app_id', 'Selected app is not valid or does not have push notifications enabled.');

            return;
        }

        // Handle image upload
        $imageUrl = null;
        if ($this->notification_style === 'image' && $this->image_file) {
            $imagePath = $this->image_file->store('notifications/'.$app->app_id, 'public');
            $imageUrl = url(Storage::url($imagePath));
        }

        // Determine status based on schedule
        $status = $this->schedule_type === 'later' ? 'scheduled' : 'pending';

        // Create the notification record
        $notification = AppNotification::create([
            'app_id' => $this->app_id,
            'user_id' => auth()->id(),
            'title' => $this->title,
            'body' => $this->body,
            'image_url' => $imageUrl,
            'notification_style' => $this->notification_style,
            'status' => $status,
            'scheduled_at' => $this->schedule_type === 'later' ? $this->scheduled_at : null,
        ]);

        // If sending now, dispatch the job immediately
        if ($this->schedule_type === 'now') {
            SendPushNotificationJob::dispatch($notification);
        }

        $this->showModal = false;
        $this->dispatch('notificationSent');

        session()->flash('message', $this->schedule_type === 'later'
            ? 'Notification scheduled successfully!'
            : 'Notification sent successfully!');
    }

    public function render()
    {
        return view('livewire.user.send-notification-modal');
    }
}
