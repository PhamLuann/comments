@php
    if (isset($approved) and $approved == true) {
        $comments = $model->approvedComments;
    } else {
        $comments = $model->comments;
    }
@endphp

@if($comments->count() < 1)
    <div>@lang('comments::comments.there_are_no_comments')</div>
@endif

@php
    $comments = $comments->sortBy('created_at');

    if (isset($perPage)) {
        $page = request()->query('page', 1) - 1;

        $parentComments = $comments->where('child_id', '');

        $slicedParentComments = $parentComments->slice($page * $perPage, $perPage);

        $m = Config::get('comments.model'); // This has to be done like this, otherwise it will complain.
        $modelKeyName = (new $m)->getKeyName(); // This defaults to 'id' if not changed.

        $slicedParentCommentsIds = $slicedParentComments->pluck($modelKeyName)->toArray();

        // Remove parent Comments from comments.
        $comments = $comments->where('child_id', '!=', '');

        $grouped_comments = new \Illuminate\Pagination\LengthAwarePaginator(
            $slicedParentComments->merge($comments)->groupBy('child_id'),
            $parentComments->count(),
            $perPage
        );

        $grouped_comments->withPath(request()->url());
    } else {
        $grouped_comments = $comments->groupBy('child_id');
    }
@endphp
<div id="user-like" class="fixed top-0 left-0 right-0 w-full flex mt-[10%] justify-center z-30"></div>
@auth
    @include('comments::_form')
@elseif(Config::get('comments.guest_commenting') == true)
    @include('comments::_form', [
        'guest_commenting' => true
    ])
@else
    <div class="my-5 flex justify-end">
        <div>
            <span class="opacity-80">@lang('comments::comments.you_must_login_to_post_a_comment')</span>
            <a href="{{ route('login') }}"
               class="border border-sky-500 px-2 py-1 rounded-lg bg-teal-500 hover:bg-teal-600 uppercase">@lang('comments::comments.log_in')</a>
        </div>
    </div>
@endauth

<div class="max-h-[700px] overflow-y-scroll">
    @foreach($grouped_comments as $comment_id => $comments)
        {{-- Process parent nodes --}}
        @if($comment_id == '')
            @foreach($comments as $comment)
                @include('comments::_comment', [
                'comment' => $comment,
                'grouped_comments' => $grouped_comments,
                'maxIndentationLevel' => $maxIndentationLevel ?? 3
            ])
            @endforeach
        @endif
    @endforeach
</div>

@isset ($perPage)
    {{ $grouped_comments->links() }}
@endisset