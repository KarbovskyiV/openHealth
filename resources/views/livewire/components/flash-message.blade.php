<!-- resources/views/livewire/flash-message.blade.php -->
<div>
    @if ($message || $errors)

        <div
            x-init="setTimeout(() => document.querySelector('.errors').remove(), 3000)"
             class="errors flex fixed r-0 border-l-6 bottom-[1.5rem] bg-opacity-[15%]  px-7 py-8  bottom-[1.5rem] w-2/6 z-100 {{$type == 'error' ? 'bg-[#F87171] border-[#F87171]' : 'bg-[#34D399] border-[#34D399]'}} right-2 ">
                <div @click="document.querySelector('.errors').remove()" class="absolute top-[0.5rem] right-2">
                    <svg width="30px" height="30px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                </div>


                @if($type == 'error' && !$errors)
                    <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#F87171]">
                        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M6.4917 7.65579L11.106 12.2645C11.2545 12.4128 11.4715 12.5 11.6738 12.5C11.8762 12.5 12.0931 12.4128 12.2416 12.2645C12.5621 11.9445 12.5623 11.4317 12.2423 11.1114C12.2422 11.1113 12.2422 11.1113 12.2422 11.1113C12.242 11.1111 12.2418 11.1109 12.2416 11.1107L7.64539 6.50351L12.2589 1.91221L12.2595 1.91158C12.5802 1.59132 12.5802 1.07805 12.2595 0.757793C11.9393 0.437994 11.4268 0.437869 11.1064 0.757418C11.1063 0.757543 11.1062 0.757668 11.106 0.757793L6.49234 5.34931L1.89459 0.740581L1.89396 0.739942C1.57364 0.420019 1.0608 0.420019 0.740487 0.739944C0.42005 1.05999 0.419837 1.57279 0.73985 1.89309L6.4917 7.65579ZM6.4917 7.65579L1.89459 12.2639L1.89395 12.2645C1.74546 12.4128 1.52854 12.5 1.32616 12.5C1.12377 12.5 0.906853 12.4128 0.758361 12.2645L1.1117 11.9108L0.758358 12.2645C0.437984 11.9445 0.437708 11.4319 0.757539 11.1116C0.757812 11.1113 0.758086 11.111 0.75836 11.1107L5.33864 6.50287L0.740487 1.89373L6.4917 7.65579Z"
                                fill="#ffffff" stroke="#ffffff"></path>
                        </svg>
                    </div>
                    <div class="w-full flex items-center">
                        <h5 class=" text-sm font-bold text-black dark:text-[#34D399]">
                            {{ $message }}
                        </h5>
                    </div>
                @endif
                @if($type == 'success' && !$errors)
                    <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#34D399]">
                        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.2984 0.826822L15.2868 0.811827L15.2741 0.797751C14.9173 0.401867 14.3238 0.400754 13.9657 0.794406L5.91888 9.45376L2.05667 5.2868C1.69856 4.89287 1.10487 4.89389 0.747996 5.28987C0.417335 5.65675 0.417335 6.22337 0.747996 6.59026L0.747959 6.59029L0.752701 6.59541L4.86742 11.0348C5.14445 11.3405 5.52858 11.5 5.89581 11.5C6.29242 11.5 6.65178 11.3355 6.92401 11.035L15.2162 2.11161C15.5833 1.74452 15.576 1.18615 15.2984 0.826822Z"
                                fill="white" stroke="white"></path>
                        </svg>
                    </div>
                    <div class="w-full flex items-center">
                        <h5 class=" text-sm font-bold text-black dark:text-[#34D399]">
                            {{ $message }}
                        </h5>
                    </div>
                @endif
                @if($errors)
                    <ul class="list">
                        @foreach($errors as $error)
                            <li class="text-sm font-bold text-black dark:text-[#F87171]">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
        </div>
    @endif

        <style>

        </style>
</div>