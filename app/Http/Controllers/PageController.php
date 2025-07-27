<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Mail\Contact;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function show(Request $request,$slug)
    {
        try {
            $listing = Page::where('slug', $slug)->firstOrFail();
            
            // SEO with enhanced optimization
            $seoMeta = \App\Helpers\SEOHelper::generateDefaultMeta(
                $listing->title,
                $listing->description,
                $request
            );
            
            $new = [$listing->title];
            $old = ['[title]'];

            $config['title'] = trim(str_replace($old, $new, trim(config('settings.page_title'))));
            $config['description'] = trim(str_replace($old, $new, trim(config('settings.page_description'))));
            
            // Merge enhanced SEO
            $config = array_merge($config, $seoMeta);

            return view('page.show', compact('config', 'listing', 'request'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::warning('Page not found: ' . $slug, [
                'slug' => $slug,
                'user_id' => auth()->id()
            ]);
            abort(404);
        } catch (\Exception $e) {
            \Log::error('Error loading page: ' . $e->getMessage(), [
                'slug' => $slug,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500);
        }
    }

    public function contact(Request $request)
    {

        // Seo
        $config['title'] = __('Contact').' - '.config('settings.title');
        $config['description'] = config('settings.description');

        return view('page.contact', compact('config', 'request'));
    }
    public function contactmail(Request $request) {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:1000',
                'email' => 'required|email'
            ]);

            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ];
            
            Mail::to(config('settings.to_email'))->send(new Contact($mailData));
            
            return redirect()->route('contact')->with('success', __('Submitted'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error sending contact email: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', __('Error sending message. Please try again.'))->withInput();
        }
    }
}
