<x-layouts.app title="Sign in">
    <form method="POST" action="{{ route('crm.login.store') }}" class="mx-auto max-w-md space-y-6 rounded-lg bg-white p-8 shadow-sm">
        @csrf
        <div class="space-y-2">
            <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                   class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-200" />
        </div>

        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input id="password" name="password" type="password" required
                   class="block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-200" />
        </div>

        <button type="submit"
                class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900">
            Sign in
        </button>
    </form>
</x-layouts.app>
