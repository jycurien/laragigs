<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    // Show all listings
    public function index() 
    {
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(4)
        ]);
    }

    // Show Create Form
    public function create() 
    {
        return view('listings.create');
    }

    // Store Listing Data
    public function store(Request $request) 
    {
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if ($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formFields['user_id'] = auth()->id();

        Listing::create($formFields);

        return redirect('/')->with('message', 'Listing created successfully!');
    }

    // Show Edit Form
    public function edit(Listing $listing) 
    {
        return view('listings.edit', [
            'listing' => $listing
        ]);
    }

    // Update Listing
    public function update(Request $request, Listing $listing)
    {     
        // if ($request->user()->cannot('update', $listing)) {
        //     abort(403);
        // }
        // this does the same thing, see also can middleware in routes
        // $this->authorize('update', $listing);
        
        $formFields = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'website' => 'required',
            'email' => ['required', 'email'],
            'tags' => 'required',
            'description' => 'required'
        ]);

        if($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $listing->update($formFields);

        return back()->with('message', 'Listing updated successfully!');        
    }

    // Delete Listing
    public function destroy(Listing $listing) 
    {      
        // if ($request->user()->cannot('delete', $listing)) {
        //     abort(403);
        // }
        // this does the same thing see also can middleware in routes
        // $this->authorize('delete', $listing);
        
        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully');
    }

    // Show single listing
    public function show(Listing $listing) 
    {
        return view('listings.show', [
            'listing' => $listing
        ]);
    }

    // Manage Listings
    public function manage() 
    {
        return view('listings.manage', [
            'listings' => auth()->user()->listings()->get()
        ]);
    }
}