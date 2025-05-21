<div>
    <figure class="h-auto md:h-72 flex flex-col md:flex-row
                    rounded-none sm:rounded-xl
                    bg-zinc-50  dark:bg-gray-900
                    border border-zinc-200
                    my-4 p-8 md:p-0">
        <a class="h-48 w-48 md:h-72 md:w-72 md:min-w-72  mx-auto" href="{{ route('users.show', ['user' => $user]) }}">
            <img class="h-full aspect-auto mx-auto rounded-full
                        md:rounded-l-xl md:rounded-r-none" src="{{ $user->imageUrl }}">
        </a>
        <div class="h-auto p-6 text-center md:text-left space-y-1 flex flex-col">
            <a class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-5" href="{{ route('users.show', ['user' => $user]) }}">
                {{ $user->name }}
            </a>
            <figcaption class="font-medium">
                <div class="flex justify-center md:justify-start font-base
                            text-base space-x-6 text-gray-700 dark:text-gray-300">
                    <div>{{ $user->email }} email</div>
                    <div>{{ $user->type }} type</div>
                    <select id="gender" name="gender" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2 py-1">
                        <option value="masculino" @selected($user->gender === 'masculino')>Masculino</option>
                        <option value="feminino" @selected($user->gender === 'feminino')>Feminino</option>
                        <option value="outro" @selected($user->gender === 'outro')>Outro</option>
                    </select>
                    <div>{{ $user->nif }} nif</div>
                    
                </div>
                <address class="font-light text-gray-700 dark:text-gray-300">
                    <a href="mailto:{{ $user->delivery_address }}">{{ $user->delivery_address }}</a>.
                </address>
            </figcaption>
        
        </div>
    </figure>
</div>
