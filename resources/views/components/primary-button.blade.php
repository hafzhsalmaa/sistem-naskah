<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex min-h-[46px] items-center justify-center rounded-xl border border-transparent bg-[#0f4c81] px-5 py-2 text-sm font-extrabold text-white shadow-[0_14px_28px_rgba(15,76,129,0.2)] transition duration-200 ease-in-out hover:-translate-y-0.5 hover:bg-[#0b3d68] focus:outline-none focus:ring-2 focus:ring-[#3d78ba] focus:ring-offset-2 active:bg-[#083456] disabled:opacity-60 disabled:hover:translate-y-0']) }}>
    {{ $slot }}
</button>
