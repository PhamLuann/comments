@inject('markdown', 'Parsedown')
@php
    use Laravelista\Comments\LikeController;
    // TODO: There should be a better place for this.
    $markdown->setSafeMode(true);
@endphp
<div class="">
    <div id="comment-{{ $comment->getKey() }}" class="flex mb-5 @if($comment->parent != null) ml-14 md:ml-24 @endif">
        <img class="w-12 h-12 md:w-20 md:h-20 rounded-full"
             src="https://www.gravatar.com/avatar/{{ md5($comment->commenter->email ?? $comment->guest_email) }}.jpg?s=64"
             alt="{{ $comment->commenter->name ?? $comment->guest_name }} Avatar">
        <div class="w-full ml-2 md:ml-4">
            <div class="min-h-[48px] md:min-h-[80px] w-full bg-gray-200 rounded-lg pl-4 pt-3 border-b border-gray-700">
                <div class="block w-fit mb-2 md:flex items-center text-xs md:text-base border-b border-gray-300">
                    <div class="flex">
                        <h5 class="font-bold">{{ $comment->commenter->name ?? $comment->guest_name }} </h5>
                        @if($comment->child_id != null)
                            <span class="ml-1">reply to</span>
                            <span class="ml-1 font-bold">{{$comment->parent->commenter->name}}</span>
                        @endif
                    </div>
                    <div class="hidden md:block w-2 h-2 bg-black rounded-full opacity-80 mx-3"></div>
                    <p class="text-xs opacity-60"> {{ $comment->created_at->diffForHumans() }}</p>
                </div>
                <div style="white-space: pre-wrap;">{!! $markdown->line($comment->comment) !!}</div>
            </div>
            <div class="mt-1 flex relative">
                {{--Like--}}
                @auth()
                    <div class="hover:cursor-pointer flex items-center mr-3">
                        <button class="px-2 md:px-5 text-xs md:text-base rounded-2xl border border-gray-700 hover:bg-teal-300 flex items-center @if(LikeController::check($comment->getKey())) bg-teal-400 @endif"
                                @auth() @else disabled @endauth onclick="like_comment({{$comment->id}})" id="like-{{$comment->id}}">
                            @if(LikeController::check($comment->getKey()))
                                Unlike
                            @else
                                Like
                            @endif
                        </button>
                    </div>
                @endauth
                {{--Like--}}
                @can('reply-to-comment', $comment)
                    <button id="btn-reply-{{$comment->getKey()}}"
                            onclick="reply({{$comment->getKey()}})"
                            class="px-2 md:px-5 text-xs md:text-base rounded-2xl border border-gray-500 hover:bg-teal-400 mr-3"
                            type="button">
                        @lang('comments::comments.reply')
                    </button>
                @endcan
                @can('edit-comment', $comment)
                    <button data-modal-target="editComment-{{$comment->getKey()}}"
                            data-modal-toggle="editComment-{{$comment->getKey()}}"
                            class="px-2 md:px-5 text-xs md:text-base rounded-2xl border border-gray-500 hover:bg-teal-400 mr-3"
                            type="button">
                        @lang('comments::comments.edit')
                    </button>
                @endcan
                @can('delete-comment', $comment)
                    <a href="{{ route('comments.destroy', $comment->getKey()) }}"
                       onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();"
                       class="px-2 md:px-5 text-xs md:text-base rounded-2xl border border-gray-500 hover:bg-teal-400 mr-3">@lang('comments::comments.delete')</a>
                    <form id="comment-delete-form-{{ $comment->getKey() }}"
                          action="{{ route('comments.destroy', $comment->getKey()) }}" method="POST"
                          style="display: none;">
                        @method('DELETE')
                        @csrf
                    </form>
                @endcan
                {{--view Like--}}
                <div class="absolute right-1 md:right-10 -top-5 hover:cursor-pointer">
                    <button id="btn-view-{{$comment->getKey()}}" onclick="viewUserLike({{$comment->id}})"
                            class="px-2 md:px-5 text-xs md:text-base rounded-2xl border border-sky-500 text-sky-500 uppercase bg-white flex items-center">
                        <p id="count-like-{{$comment->getKey()}}">{{$comment->like()->count()}}</p>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                             class="w-5 h-5 ml-2">
                            <path
                                    d="M1 8.25a1.25 1.25 0 112.5 0v7.5a1.25 1.25 0 11-2.5 0v-7.5zM11 3V1.7c0-.268.14-.526.395-.607A2 2 0 0114 3c0 .995-.182 1.948-.514 2.826-.204.54.166 1.174.744 1.174h2.52c1.243 0 2.261 1.01 2.146 2.247a23.864 23.864 0 01-1.341 5.974C17.153 16.323 16.072 17 14.9 17h-3.192a3 3 0 01-1.341-.317l-2.734-1.366A3 3 0 006.292 15H5V8h.963c.685 0 1.258-.483 1.612-1.068a4.011 4.011 0 012.166-1.73c.432-.143.853-.386 1.011-.814.16-.432.248-.9.248-1.388z"/>
                        </svg>
                    </button>
                </div>
                {{--view Like--}}
            </div>
        </div>
    </div>
</div>
@can('edit-comment', $comment)
    <div id="editComment-{{$comment->getKey()}}" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
         class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
        <div class="relative w-full h-full max-w-2xl md:h-auto">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-md font-semibold text-gray-900 dark:text-white">
                        @lang('comments::comments.edit_comment')
                    </h3>
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                            data-modal-hide="editComment-{{$comment->getKey()}}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                  clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('comments.update', $comment->getKey()) }}" class="p-4">
                    @method('PUT')
                    @csrf
                    <label for="message">@lang('comments::comments.update_your_message_here')</label>
                    <textarea required class="block p-1 w-full border border-sky-500" name="message"
                              rows="3">{{ $comment->comment }}</textarea>
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="editComment-{{$comment->getKey()}}" type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            @lang('comments::comments.update')
                        </button>
                        <button data-modal-hide="editComment-{{$comment->getKey()}}" type="button"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            @lang('comments::comments.cancel')
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endcan

@can('reply-to-comment', $comment)
    <div id="reply-{{$comment->getKey()}}" class="ml-16 hidden md:ml-24">
        <div class="mx-4 lg:mx-24 mt-5">
            <div class="">
                @if($errors->has('commentable_type'))
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-gray-800 dark:text-red-400"
                         role="alert">
                        <span class="font-medium">{{ $errors->first('commentable_type') }}</span>
                    </div>
                @endif
                @if($errors->has('commentable_id'))
                    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-gray-800 dark:text-red-400"
                         role="alert">
                        <span class="font-medium">{{ $errors->first('commentable_id') }}</span>
                    </div>
                @endif
                <form method="POST" action="{{ route('comments.reply', $comment->getKey()) }}">
                    @csrf
                    @honeypot
                    <input type="hidden" name="commentable_type" value="\{{ get_class($model) }}"/>
                    <input type="hidden" name="commentable_id" value="{{ $model->getKey() }}"/>

                    {{-- Guest commenting --}}
                    @if(isset($guest_commenting) and $guest_commenting == true)
                        <div>
                            <div class="mt-3">
                                <input type="text" class="rounded-lg px-5 py-2 w-full"
                                       name="guest_name"
                                       placeholder="@lang('comments::comments.enter_your_name_here')"/>
                                @error('guest_name')
                                <div class="text-red-500">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mt-3">
                                <input type="email" class="rounded-lg px-5 py-2 w-full"
                                       name="guest_email"
                                       placeholder="@lang('comments::comments.enter_your_email_here')"/>
                                @error('guest_email')
                                <div class="text-red-500">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <textarea
                            class="mt-3 rounded-lg px-5 py-2 w-full h-auto md:h-24 @if($errors->has('message')) is-invalid @endif"
                            name="message" placeholder="@lang('comments::comments.enter_your_message_here')"
                            required></textarea>
                    <div class="w-full relative mb-8">
                        <div class="absolute right-0">
                            <button type="button" onclick="cancelReply({{$comment->getKey()}})"
                                    class="px-1 md:px-5 md:py-2 rounded-lg hover:drop-shadow-xl bg-gray-200 hover:bg-gray-300">
                                @lang('comments::comments.cancel')
                            </button>
                            <button type="submit"
                                    class="px-1 md:px-5 md:py-2 rounded-lg bg-red-600 hover:bg-red-400 hover:drop-shadow-xl text-white ">
                                @lang('comments::comments.reply')
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br/>
    </div>
@endcan
    <?php
    if (!isset($indentationLevel)) {
        $indentationLevel = 1;
    } else {
        $indentationLevel++;
    }
    ?>

{{-- Recursion for children --}}
@if($grouped_comments->has($comment->getKey()) && $indentationLevel <= $maxIndentationLevel)
    {{-- TODO: Don't repeat code. Extract to a new file and include it. --}}
    @foreach($grouped_comments[$comment->getKey()] as $child)
        @include('comments::_comment', [
            'comment' => $child,
            'grouped_comments' => $grouped_comments
        ])
    @endforeach
@endif


{{-- Recursion for children --}}
@if($grouped_comments->has($comment->getKey()) && $indentationLevel > $maxIndentationLevel)
    {{-- TODO: Don't repeat code. Extract to a new file and include it. --}}
    @foreach($grouped_comments[$comment->getKey()] as $child)
        @include('comments::_comment', [
            'comment' => $child,
            'grouped_comments' => $grouped_comments
        ])
    @endforeach
@endif
@include('comments::_script')
