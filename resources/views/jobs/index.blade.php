<x-layout>

    <x-slot:heading>
        Job Listings
    </x-slot:heading>

    <div class="space-y-4">
        @foreach ($jobs as $job)
            <a>
                <a href="/jobs/{{ $job['id'] }}" class="block px-4 py-6 border border-gray-300 rounded-lg">
                    <div class="italic font-bold text-blue-500">{{ $job->employer->name }}</div>
                    <div>
                        <strong>{{ $job['title'] }}:</strong> Pays {{ $job['salary'] }} per year.
                    </div>
                </a>
            </a>
        @endforeach

        <div>
            {{ $jobs->links() }}
        </div>
    </div>
    
</x-layout>