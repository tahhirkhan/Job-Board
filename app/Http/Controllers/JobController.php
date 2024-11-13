<?php

namespace App\Http\Controllers;

use App\Mail\JobPosted;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class JobController extends Controller
{
    public function index() {
        $jobs = Job::with('employer')->latest()->simplePaginate(6);
        return view('jobs.index', ['jobs' => $jobs]);
    }

    public function create() {
        return view ('jobs.create');
    }

    public function show(Job $job) {
        return view('jobs.show', ['job' => $job]);
    }

    public function store() {
        request()->validate([
            'title' => ['required', 'min:5'],
            'salary' => ['required'],
        ]);
    
        $job = Job::create([
            'title' => request('title'),
            'salary' => request('salary'),
            'employer_id' => 1,
        ]);

        Mail::to($job->employer->user)->queue( // laravel will automatically extract email from the user instance provided
            new JobPosted($job)
        );
    
        return redirect('/jobs');
    }

    public function edit(Job $job) {
        
        // Step 1: inline authorization
        // if(Auth::guest()) {
        //     return redirect('/login');
        // }
        // if($job->employer->user->isNot(Auth::user())) {
        //     abort(403);
        // }

        // Step 2: Gates:
        // Gate::define('edit-job', function(User $user, Job $job) {
        //     return $job->employer()->user->is($user);
        // });

        // Gate::authorize('edit-job', $job); // reference gate that we defined

        // Step 3: Define Gates inside AppServiceProvider:
        // check boot method inside app/Providers/AppServiceProvider
        // we can now reference the gate from anywhere

        // Step 4: Can:
        // ex. $model->can() :- determine if the entity has the given abilities.
        // if (Auth::user()->can('edit-job', $job) {
        //     // do something
        // });
        // it means if the user is authorized to edit the job $job, then do something
        // we can also implement it using a blade directive @can (check the jobs.show view for edit button)

        // Step 5: Middleware:
        // check routes/web.php, we have defined auth & can middleware

        // Step 6: Policies:
        // policy is advanced version of facade (Gate Facade)
        // for smaller projects we prefer Gates and for bigger projects Policies are preffered.
        // artisan command to make policy: php aritsan make:policy JobPolicy

        return view('jobs.edit', ['job' => $job]);
    }

    public function update(Job $job) {
        // authorize(on hold....):

        // validate:
        request()->validate([
            'title' => ['required', 'min:5'],
            'salary' => ['required'],
        ]);

        //update the job:
        $job->update([
            'title' => request('title'),
            'salary' => request('salary'),
        ]);

        // redirect to the job page:
        return redirect('/jobs/' . $job->id);
    }

    public function destroy(Job $job) {
        // authorize: (on hold...)

        // delete job:
        $job->delete();

        // redirect to the jobs page
        return redirect('/jobs');
    }
}
