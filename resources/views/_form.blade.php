<div class="mt-5">
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
        <div class="font-bold text-lg border-l-4 border-red-600 pl-2">Comment</div>
        <form method="POST" action="{{ route('comments.store') }}">
            @csrf
            @honeypot
            <input type="hidden" name="commentable_type" value="\{{ get_class($model) }}"/>
            <input type="hidden" name="commentable_id" value="{{ $model->getKey() }}"/>

            {{-- Guest commenting --}}
            @if(isset($guest_commenting) and $guest_commenting == true)
                <div>
                    <div class="mt-3">
                        <input type="text" class="rounded-lg px-5 py-2 w-full"
                               name="guest_name" placeholder="@lang('comments::comments.enter_your_name_here')"/>
                        @error('guest_name')
                        <div class="text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mt-3">
                        <input type="email" class="rounded-lg px-5 py-2 w-full"
                               name="guest_email" placeholder="@lang('comments::comments.enter_your_email_here')"/>
                        @error('guest_email')
                        <div class="text-red-500">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            @endif

            <textarea class="mt-3 rounded-lg px-5 py-2 w-full h-auto md:h-24 @if($errors->has('message')) is-invalid @endif"
                      name="message" placeholder="@lang('comments::comments.enter_your_message_here')" required></textarea>
            <div class="w-full relative mb-8">
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-red-600 hover:bg-red-400 hover:drop-shadow-xl text-white absolute right-0">@lang('comments::comments.submit')</button>
            </div>
        </form>
    </div>
</div>
<br/>