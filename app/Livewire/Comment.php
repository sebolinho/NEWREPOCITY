<?php

namespace App\Livewire;


use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\User;

class Comment extends Component
{
    use AuthorizesRequests;

    public $comment;

    public $users = [];

    public $isReplying = false;
    public $hasReplies = false;

    public $showOptions = false;

    public $isEditing = false;

    public $replyState = [
        'body' => ''
    ];

    public $editState = [
        'body' => ''
    ];

    protected $validationAttributes = [
        'replyState.body' => 'Reply',
        'editState.body' => 'Reply'
    ];

    protected $listeners = [
        'refresh' => '$refresh',
        'getUsers'
    ];

    /**
     * @param $isEditing
     * @return void
     */
    public function updatedIsEditing($isEditing): void
    {
        if (!$isEditing) {
            return;
        }
        $this->editState = [
            'body' => $this->comment->body
        ];
    }

    /**
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editComment(): void
    {
        try {
            $this->authorize('update', $this->comment);
            $this->validate([
                'editState.body' => 'required|min:5|max:500'
            ]);
            $this->comment->update($this->editState);
            $this->isEditing = false;
            $this->showOptions = false;
            $this->dispatch('show-toast', ['message' => __('Comment updated successfully')])->to(NotifyComponent::class);
        } catch (AuthorizationException $e) {
            $this->dispatch('show-toast', ['message' => __('You are not authorized to edit this comment'), 'type' => 'error'])->to(NotifyComponent::class);
            throw $e;
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error updating comment: ' . $e->getMessage(), [
                'comment_id' => $this->comment->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('show-toast', ['message' => __('Error updating comment. Please try again.'), 'type' => 'error'])->to(NotifyComponent::class);
        }
    }

    /**
     * @return void
     * @throws AuthorizationException
     */
    public function deleteComment(): void
    {
        try {
            $this->authorize('destroy', $this->comment);
            $this->comment->delete();
            $this->dispatch('refresh');
            $this->showOptions = false;
            $this->dispatch('show-toast', ['message' => __('Comment deleted successfully')])->to(NotifyComponent::class);
        } catch (AuthorizationException $e) {
            $this->dispatch('show-toast', ['message' => __('You are not authorized to delete this comment'), 'type' => 'error'])->to(NotifyComponent::class);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error deleting comment: ' . $e->getMessage(), [
                'comment_id' => $this->comment->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('show-toast', ['message' => __('Error deleting comment. Please try again.'), 'type' => 'error'])->to(NotifyComponent::class);
        }
    }

    /**
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application|null
     */
    public function render(
    ): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application|null
    {
        return view('livewire.comment');
    }

    /**
     * @return void
     */
    public function postReply(): void
    {
        try {
            if (!$this->comment->isParent()) {
                return;
            }
            $this->validate([
                'replyState.body' => 'required|min:5|max:500'
            ]);
            $reply = $this->comment->children()->make($this->replyState);
            $reply->user()->associate(auth()->user());
            $reply->commentable()->associate($this->comment->commentable);
            $reply->status = config('settings.comment_status') == 'active' ? 'draft' : 'publish';
            $reply->save();

            $this->replyState = [
                'body' => ''
            ];
            $this->isReplying = false;
            $this->showOptions = false;
            $this->dispatch('refresh')->self();
            $this->dispatch('show-toast', ['message' => __('Reply posted successfully')])->to(NotifyComponent::class);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error posting reply: ' . $e->getMessage(), [
                'parent_comment_id' => $this->comment->id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('show-toast', ['message' => __('Error posting reply. Please try again.'), 'type' => 'error'])->to(NotifyComponent::class);
        }
    }

    /**
     * @param $userName
     * @return void
     */
    public function selectUser($userName): void
    {
        if ($this->replyState['body']) {
            $this->replyState['body'] = preg_replace('/@(\w+)$/', '@'.str_replace(' ', '_', Str::lower($userName)).' ',
                $this->replyState['body']);
//            $this->replyState['body'] =$userName;
            $this->users = [];
        } elseif ($this->editState['body']) {
            $this->editState['body'] = preg_replace('/@(\w+)$/', '@'.str_replace(' ', '_', Str::lower($userName)).' ',
                $this->editState['body']);
            $this->users = [];
        }
    }

}
